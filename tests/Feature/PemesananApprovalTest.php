<?php

namespace Tests\Feature;

use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\PersetujuanPemesanan;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PemesananApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $wilayah;

    protected $manajer;

    protected $atasan;

    protected $pegawai;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wilayah = Wilayah::create([
            'nama' => 'Site Pomalaa',
            'tipe' => 'tambang',
        ]);

        $this->manajer = Pengguna::create([
            'wilayah_id' => $this->wilayah->id,
            'nama' => 'Bapak Manajer (L2)',
            'email' => 'manajer@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'manajer',
        ]);

        $this->atasan = Pengguna::create([
            'wilayah_id' => $this->wilayah->id,
            'atasan_id' => $this->manajer->id,
            'nama' => 'Ibu Atasan (L1)',
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
    }

    /**
     * Test alur normal 2 tahap: Atasan approve Tahap 1, lalu Manajer approve Tahap 2.
     * Admin dilarang melakukan persetujuan.
     */
    public function test_alur_normal_dua_tahap_atasan_lalu_manajer(): void
    {
        // 1. Pegawai mengajukan pemesanan
        $response = $this->withSession([
            'user_id' => $this->pegawai->id,
            'user_nama' => $this->pegawai->nama,
            'user_peran' => 'pegawai',
        ])->post('/pemesanan/ajukan', [
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDay()->addHours(4)->format('Y-m-d H:i:s'),
            'keperluan' => 'Inspeksi Pit Area',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('pemesanan', [
            'pegawai_id' => $this->pegawai->id,
            'status' => 'tertunda',
        ]);

        $pemesanan = Pemesanan::first();
        $this->assertCount(2, $pemesanan->persetujuan);

        // 2. Admin mencoba menyetujui -> HARUS DITOLAK KARENA ADMIN TIDAK MEMILIKI HAK APPROVAL
        $responseAdminGagal = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
        ]);

        $responseAdminGagal->assertSessionHas('error');
        $this->assertEquals('tertunda', $pemesanan->fresh()->status);

        // 3. Atasan menyetujui Tahap 1 (Level 1)
        $responseAtasan = $this->withSession([
            'user_id' => $this->atasan->id,
            'user_nama' => $this->atasan->nama,
            'user_peran' => 'atasan',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
            'catatan' => 'Unit armada diverifikasi oke oleh Atasan',
        ]);

        $responseAtasan->assertSessionHas('success');
        $lvl1 = PersetujuanPemesanan::where('pemesanan_id', $pemesanan->id)->where('level', 1)->first();
        $this->assertEquals('disetujui', $lvl1->status);
        $this->assertEquals('tertunda', $pemesanan->fresh()->status); // Masih tertunda karena butuh Tahap 2 Manajer

        // 4. Manajer menyetujui Tahap 2
        $responseManajer = $this->withSession([
            'user_id' => $this->manajer->id,
            'user_nama' => $this->manajer->nama,
            'user_peran' => 'manajer',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
            'catatan' => 'Disetujui untuk operasional oleh Manajer',
        ]);

        $responseManajer->assertSessionHas('success');
        $lvl2 = PersetujuanPemesanan::where('pemesanan_id', $pemesanan->id)->where('level', 2)->first();
        $this->assertEquals('disetujui', $lvl2->status);
        $this->assertEquals('disetujui', $pemesanan->fresh()->status); // Kini status pemesanan final disetujui!
    }

    /**
     * Test kondisi khusus: jika Manajer approve langsung (Bypass).
     */
    public function test_manajer_bisa_langsung_approve_tanpa_menunggu_atasan(): void
    {
        // 1. Pegawai mengajukan pemesanan
        $this->withSession([
            'user_id' => $this->pegawai->id,
            'user_nama' => $this->pegawai->nama,
            'user_peran' => 'pegawai',
        ])->post('/pemesanan/ajukan', [
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDay()->addHours(3)->format('Y-m-d H:i:s'),
            'keperluan' => 'Kunjungan Direksi',
        ]);

        $pemesanan = Pemesanan::first();

        // 2. Manajer LANGSUNG approve tanpa menunggu persetujuan Atasan
        $responseManajer = $this->withSession([
            'user_id' => $this->manajer->id,
            'user_nama' => $this->manajer->nama,
            'user_peran' => 'manajer',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
            'catatan' => 'Prioritas darurat operasional',
        ]);

        $responseManajer->assertSessionHas('success');

        $lvl1 = PersetujuanPemesanan::where('pemesanan_id', $pemesanan->id)->where('level', 1)->first();
        $lvl2 = PersetujuanPemesanan::where('pemesanan_id', $pemesanan->id)->where('level', 2)->first();

        // Tahap 1 Atasan otomatis dilewati (bypass)
        $this->assertEquals('dilewati', $lvl1->status);
        // Tahap 2 Manajer disetujui
        $this->assertEquals('disetujui', $lvl2->status);
        // Status pemesanan langsung disetujui!
        $this->assertEquals('disetujui', $pemesanan->fresh()->status);
    }

    /**
     * Test penolakan pemesanan oleh Atasan
     */
    public function test_penolakan_pemesanan(): void
    {
        $this->withSession([
            'user_id' => $this->pegawai->id,
            'user_nama' => $this->pegawai->nama,
            'user_peran' => 'pegawai',
        ])->post('/pemesanan/ajukan', [
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'keperluan' => 'Tugas Non-Urgent',
        ]);

        $pemesanan = Pemesanan::first();

        // Atasan tolak pemesanan
        $response = $this->withSession([
            'user_id' => $this->atasan->id,
            'user_nama' => $this->atasan->nama,
            'user_peran' => 'atasan',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'ditolak',
            'catatan' => 'Unit kendaraan sedang tidak memadai',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('ditolak', $pemesanan->fresh()->status);
    }

    /**
     * Test admin dilarang melakukan approval
     */
    public function test_admin_ditolak_melakukan_persetujuan(): void
    {
        $this->withSession([
            'user_id' => $this->pegawai->id,
            'user_nama' => $this->pegawai->nama,
            'user_peran' => 'pegawai',
        ])->post('/pemesanan/ajukan', [
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'keperluan' => 'Operasional Tambang',
        ]);

        $pemesanan = Pemesanan::first();

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
        ])->post('/approval/'.$pemesanan->id, [
            'status' => 'disetujui',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('tertunda', $pemesanan->fresh()->status);
    }

    /**
     * Test tampilan Blade memastikan tombol dan output persetujuan untuk Atasan, bukan Admin.
     */
    public function test_view_approval_khusus_atasan_dan_tidak_ditampilkan_ke_admin(): void
    {
        $this->withSession([
            'user_id' => $this->pegawai->id,
            'user_nama' => $this->pegawai->nama,
            'user_peran' => 'pegawai',
        ])->post('/pemesanan/ajukan', [
            'pegawai_id' => $this->pegawai->id,
            'waktu_mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'keperluan' => 'Pemeriksaan Lapangan',
        ]);

        // 1. Tampilan untuk Atasan: Melihat Tahap 1 (Atasan) dan tombol persetujuan
        $responseAtasan = $this->withSession([
            'user_id' => $this->atasan->id,
            'user_nama' => $this->atasan->nama,
            'user_peran' => 'atasan',
            'user_email' => 'atasan1@sekawan.com',
        ])->get('/pemesanan');

        $responseAtasan->assertStatus(200);
        $responseAtasan->assertSee('Tahap 1 (Atasan)');
        $responseAtasan->assertSee('Setujui Tahap 1 (Atasan)');
        $responseAtasan->assertDontSee('Tahap 1 (Admin)');
        $responseAtasan->assertDontSee('Menunggu Tahap 1 Admin');

        // 2. Tampilan untuk Admin: TIDAK melihat tombol persetujuan
        $responseAdmin = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_peran' => 'admin',
            'user_email' => 'admin@sekawan.com',
        ])->get('/pemesanan');

        $responseAdmin->assertStatus(200);
        $responseAdmin->assertDontSee('Setujui Tahap 1 (Admin)');
        $responseAdmin->assertDontSee('Setujui Tahap 1 (Atasan)');
        $responseAdmin->assertDontSee('Otorisasi Admin');
    }
}
