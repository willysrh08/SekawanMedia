<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\LogAktivitas;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\PersetujuanPemesanan;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VehicleManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $wilayah;

    protected $manajer;

    protected $atasan;

    protected $pegawai;

    protected $admin;

    protected $kendaraan;

    protected $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wilayah = Wilayah::create([
            'nama' => 'Site Pomalaa',
            'tipe' => 'tambang',
        ]);

        $this->manajer = Pengguna::create([
            'wilayah_id' => $this->wilayah->id,
            'nama' => 'Bapak Manajer',
            'email' => 'manajer@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'manajer',
        ]);

        $this->atasan = Pengguna::create([
            'wilayah_id' => $this->wilayah->id,
            'atasan_id' => $this->manajer->id,
            'nama' => 'Ibu Atasan',
            'email' => 'atasan1@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'atasan',
        ]);

        $this->pegawai = Pengguna::create([
            'wilayah_id' => $this->wilayah->id,
            'atasan_id' => $this->atasan->id,
            'nama' => 'Aqwila Pegawai',
            'email' => 'pegawai@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'pegawai',
        ]);

        $this->admin = Pengguna::create([
            'wilayah_id' => $this->wilayah->id,
            'nama' => 'Admin Pool',
            'email' => 'admin@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
        ]);

        $this->kendaraan = Kendaraan::create([
            'wilayah_id' => $this->wilayah->id,
            'nama' => 'Toyota Hilux 4x4',
            'merek' => 'Toyota',
            'model' => 'Hilux',
            'plat_nomor' => 'DT 1001 AB',
            'jenis' => 'barang',
            'kepemilikan' => 'milik_perusahaan',
            'kapasitas' => 2,
            'status' => 'tersedia',
        ]);

        $this->driver = Driver::create([
            'wilayah_id' => $this->wilayah->id,
            'nama' => 'Pak Supir',
            'telepon' => '08123456789',
            'status' => 'tersedia',
        ]);
    }

    /**
     * Test alur pengajuan pemesanan & persetujuan berjenjang dengan penetapan driver/armada oleh Admin
     */
    public function test_pengajuan_dan_penetapan_driver_armada_oleh_admin(): void
    {
        // 1. Pegawai mengajukan pemesanan
        $response = $this->withSession([
            'user_id' => $this->pegawai->id,
            'user_nama' => $this->pegawai->nama,
            'user_peran' => 'pegawai',
        ])->post('/pemesanan/ajukan', [
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDay()->addHours(5)->format('Y-m-d H:i:s'),
            'keperluan' => 'Inspeksi Pit Tambang Barat',
        ]);

        $response->assertStatus(302);
        $pemesanan = Pemesanan::first();
        $this->assertNotNull($pemesanan);
        $this->assertEquals('tertunda', $pemesanan->status);

        // Memastikan LogAktivitas tercatat
        $this->assertDatabaseHas('log_aktivitas', [
            'pengguna_id' => $this->pegawai->id,
            'aksi' => 'Pengajuan Pemesanan Kendaraan',
        ]);

        // 2. Admin menetapkan armada & driver
        $assignResponse = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->post('/pemesanan/'.$pemesanan->id.'/admin-assign', [
            'kendaraan_id' => $this->kendaraan->id,
            'driver_id' => $this->driver->id,
        ]);

        $assignResponse->assertSessionHas('success');
        $this->assertEquals($this->kendaraan->id, $pemesanan->fresh()->kendaraan_id);
        $this->assertEquals($this->driver->id, $pemesanan->fresh()->driver_id);

        // 3. Admin mencoba approve -> HARUS DITOLAK
        $adminApprove = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
            'catatan' => 'Unit diverifikasi oke',
        ]);

        $adminApprove->assertSessionHas('error');
        $this->assertEquals('tertunda', $pemesanan->fresh()->status);

        // 4. Atasan approve Tahap 1
        $atasanApprove = $this->withSession([
            'user_id' => $this->atasan->id,
            'user_nama' => $this->atasan->nama,
            'user_peran' => 'atasan',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
            'catatan' => 'Disetujui tahap 1 oleh Atasan',
        ]);

        $atasanApprove->assertSessionHas('success');
        $this->assertEquals('tertunda', $pemesanan->fresh()->status);

        // 5. Manajer approve Tahap 2 -> Pemesanan final disetujui & unit status digunakan
        $manajerApprove = $this->withSession([
            'user_id' => $this->manajer->id,
            'user_nama' => $this->manajer->nama,
            'user_peran' => 'manajer',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
            'catatan' => 'Disetujui untuk berangkat oleh Manajer',
        ]);

        $manajerApprove->assertSessionHas('success');
        $this->assertEquals('disetujui', $pemesanan->fresh()->status);
        $this->assertEquals('digunakan', $this->kendaraan->fresh()->status);
    }

    /**
     * Test isolasi ID: memastikan menyetujui pemesanan #2 tidak salah menyetujui pemesanan #1
     */
    public function test_approval_pemesanan_kedua_tidak_mempengaruhi_pemesanan_pertama(): void
    {
        // Buat Pemesanan 1
        $p1 = Pemesanan::create([
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDay(),
            'waktu_selesai' => now()->addDay()->addHours(2),
            'keperluan' => 'Tugas 1',
            'status' => 'tertunda',
        ]);
        PersetujuanPemesanan::create(['pemesanan_id' => $p1->id, 'approver_id' => $this->atasan->id, 'level' => 1, 'status' => 'tertunda']);
        PersetujuanPemesanan::create(['pemesanan_id' => $p1->id, 'approver_id' => $this->manajer->id, 'level' => 2, 'status' => 'tertunda']);

        // Buat Pemesanan 2
        $p2 = Pemesanan::create([
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDays(2),
            'waktu_selesai' => now()->addDays(2)->addHours(2),
            'keperluan' => 'Tugas 2',
            'status' => 'tertunda',
        ]);
        PersetujuanPemesanan::create(['pemesanan_id' => $p2->id, 'approver_id' => $this->atasan->id, 'level' => 1, 'status' => 'tertunda']);
        PersetujuanPemesanan::create(['pemesanan_id' => $p2->id, 'approver_id' => $this->manajer->id, 'level' => 2, 'status' => 'tertunda']);

        // Setujui Pemesanan 2 oleh Manajer (Bypass)
        $this->withSession([
            'user_id' => $this->manajer->id,
            'user_nama' => $this->manajer->nama,
            'user_peran' => 'manajer',
        ])->post('/approval/'.$p2->id, [
            'status' => 'disetujui',
        ]);

        // Pemesanan 2 harus 'disetujui', sedangkan Pemesanan 1 tetap 'tertunda'
        $this->assertEquals('disetujui', $p2->fresh()->status);
        $this->assertEquals('tertunda', $p1->fresh()->status);
    }

    /**
     * Test Pencatatan Konsumsi BBM & Odometer (BbmController@store & index)
     */
    public function test_pencatatan_konsumsi_bbm_dan_filter(): void
    {
        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->post('/bbm/store', [
            'no_lambung' => 'LV-01',
            'jenis_kendaraan' => 'LV',
            'driver' => 'Budi Driver',
            'tanggal' => now()->format('Y-m-d'),
            'odometer' => 150,
            'jumlah_bbm' => 15,
            'tipe_bbm' => 'Dexlite',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('konsumsi_bbms', [
            'no_lambung' => 'LV-01',
            'driver' => 'Budi Driver',
            'odometer' => 150,
            'jumlah_bbm' => 15,
            'efisiensi' => 10.00,
            'status_efisiensi' => 'Efisien',
        ]);

        // Test filter pencarian
        $filterResponse = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->get('/bbm?search=LV-01&kategori=LV');

        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('LV-01');
    }

    /**
     * Test CRUD Jadwal Driver & Servis
     */
    public function test_crud_jadwal(): void
    {
        // 1. Simpan jadwal
        $storeResponse = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->post('/jadwal', [
            'judul' => 'Shift Malam Hauling',
            'kategori' => 'driver',
            'tanggal' => now()->format('Y-m-d'),
            'waktu' => '20:00 - 04:00',
            'penanggung_jawab' => 'Pak Supir',
            'keterangan' => 'Pengantaran nikel ore',
        ]);

        $storeResponse->assertRedirect(route('jadwal.index'));
        $jadwal = Jadwal::first();
        $this->assertNotNull($jadwal);
        $this->assertEquals('Shift Malam Hauling', $jadwal->judul);

        // 2. Akses index jadwal
        $indexResponse = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->get('/jadwal');

        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Shift Malam Hauling');

        // 3. Hapus jadwal
        $deleteResponse = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->delete('/jadwal/'.$jadwal->id);

        $deleteResponse->assertRedirect(route('jadwal.index'));
        $this->assertDatabaseMissing('jadwals', ['id' => $jadwal->id]);
    }

    /**
     * Test Halaman Dashboard & Ekspor Rekapitulasi Laporan
     */
    public function test_dashboard_dan_ekspor_rekapitulasi(): void
    {
        // 1. Dashboard
        $dashResponse = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->get('/dashboard');

        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Selamat Datang');

        // 2. Rekapitulasi view
        $rekapResponse = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->get('/rekapitulasi');

        $rekapResponse->assertStatus(200);
        $rekapResponse->assertSee('Pilih Kategori');

        // 3. Ekspor format Excel/CSV
        $exportXlsx = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->get('/rekapitulasi/export?kategori=bbm&format=xlsx&site=site_01');

        $exportXlsx->assertStatus(200);
        $exportXlsx->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // 4. Ekspor format PDF (Print View)
        $exportPdf = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->get('/rekapitulasi/export?kategori=pemesanan&format=pdf&site=site_01');

        $exportPdf->assertStatus(200);
        $exportPdf->assertSee('Laporan Rekapitulasi PEMESANAN');
    }
}
