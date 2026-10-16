<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\KonsumsiBbm;
use App\Models\Pengguna;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrudManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wilayah = Wilayah::create([
            'nama' => 'Site Pomalaa',
            'tipe' => 'tambang',
        ]);

        $this->admin = Pengguna::create([
            'wilayah_id' => $this->wilayah->id,
            'nama' => 'Admin Tambang',
            'email' => 'admin@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
        ]);
    }

    /**
     * Test CRUD Jadwal Driver & Servis
     */
    public function test_crud_jadwal(): void
    {
        // 1. Create
        $response = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->post('/jadwal', [
                'judul' => 'DT-01 Hauling Nikel',
                'kategori' => 'driver',
                'tanggal' => now()->format('Y-m-d'),
                'waktu' => '07:00 - 15:00 WITA',
                'penanggung_jawab' => 'Budi Supir',
                'keterangan' => 'Rute Pit ke Jetty',
            ]);

        $response->assertRedirect('/jadwal');
        $this->assertDatabaseHas('jadwals', ['judul' => 'DT-01 Hauling Nikel']);
        $jadwal = Jadwal::first();

        // 2. Read (Index In-Place Modal & Show)
        $indexResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->get('/jadwal');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('modal-edit-jadwal', false);
        $indexResponse->assertSee('openEditJadwalModal', false);

        $showResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->get('/jadwal/'.$jadwal->id);
        $showResponse->assertStatus(200);

        $editResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->get('/jadwal/'.$jadwal->id.'/edit');
        $editResponse->assertStatus(200);

        // 3. Update
        $updateResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->put('/jadwal/'.$jadwal->id, [
                'judul' => 'DT-01 Hauling Nikel Diperbarui',
                'kategori' => 'driver',
                'tanggal' => now()->format('Y-m-d'),
                'waktu' => '08:00 - 16:00 WITA',
                'penanggung_jawab' => 'Budi Supir Senior',
                'keterangan' => 'Rute Pit ke Stockpile',
            ]);

        $updateResponse->assertRedirect('/jadwal');
        $this->assertEquals('DT-01 Hauling Nikel Diperbarui', $jadwal->fresh()->judul);

        // 4. Delete
        $deleteResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->delete('/jadwal/'.$jadwal->id);
        $deleteResponse->assertRedirect('/jadwal');
        $this->assertDatabaseMissing('jadwals', ['id' => $jadwal->id]);
    }

    /**
     * Test CRUD Armada Kendaraan
     */
    public function test_crud_kendaraan(): void
    {
        // 0. Render Index Page
        $indexResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->get('/armada');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('detailUnitModal', false);

        // 1. Create
        $response = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->post('/armada/store', [
                'merek' => 'Hino',
                'model' => 'Dump Truck 500',
                'plat_nomor' => 'DT 8888 XX',
                'status' => 'tersedia',
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kendaraan', ['plat_nomor' => 'DT 8888 XX']);
        $kendaraan = Kendaraan::where('plat_nomor', 'DT 8888 XX')->first();

        // 2. Read (Detail)
        $showResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->getJson('/armada/'.$kendaraan->id);
        $showResponse->assertStatus(200);
        $showResponse->assertJsonPath('data.plat_nomor', 'DT 8888 XX');

        // 3. Update (Web Form)
        $updateResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->put('/armada/'.$kendaraan->id, [
                'merek' => 'Hino Updated',
                'model' => 'Dump Truck 700',
                'plat_nomor' => 'DT 8888 XX',
                'status' => 'maintenance',
                'jenis' => 'barang',
                'kepemilikan' => 'milik_perusahaan',
                'kapasitas' => 2,
            ]);

        $updateResponse->assertStatus(302);
        $this->assertEquals('maintenance', $kendaraan->fresh()->status);
        $this->assertEquals('barang', $kendaraan->fresh()->jenis);

        // 4. Safety: Prevent Deleting in-use Vehicle
        $kendaraan->update(['status' => 'digunakan']);
        $deleteBlocked = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->delete('/armada/'.$kendaraan->id);
        $deleteBlocked->assertStatus(302);
        $deleteBlocked->assertSessionHas('error');
        $this->assertDatabaseHas('kendaraan', ['id' => $kendaraan->id]);

        // 5. Delete (Normal)
        $kendaraan->update(['status' => 'tersedia']);
        $deleteResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->delete('/armada/'.$kendaraan->id);
        $deleteResponse->assertStatus(302);
        $this->assertDatabaseMissing('kendaraan', ['id' => $kendaraan->id]);
    }

    /**
     * Test CRUD Driver
     */
    public function test_crud_driver(): void
    {
        // 1. Create
        $response = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->postJson('/driver', [
                'nama' => 'Pak Joko Supir',
                'telepon' => '089912345678',
                'status' => 'tersedia',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('driver', ['nama' => 'Pak Joko Supir']);
        $driver = Driver::where('nama', 'Pak Joko Supir')->first();

        // 2. Update
        $updateResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->putJson('/driver/'.$driver->id, [
                'nama' => 'Pak Joko Supir Senior',
                'telepon' => '089912345678',
                'status' => 'libur',
            ]);

        $updateResponse->assertStatus(200);
        $this->assertEquals('libur', $driver->fresh()->status);

        // 3. Delete
        $deleteResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->deleteJson('/driver/'.$driver->id);
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('driver', ['id' => $driver->id]);
    }

    /**
     * Test CRUD Konsumsi BBM
     */
    public function test_crud_bbm(): void
    {
        // 1. Create
        $response = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->post('/bbm/store', [
                'no_lambung' => 'LV-99',
                'jenis_kendaraan' => 'LV',
                'driver' => 'Pak Driver BBM',
                'tanggal' => now()->format('Y-m-d'),
                'odometer' => 500,
                'jumlah_bbm' => 50,
                'tipe_bbm' => 'Solar Industri',
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('konsumsi_bbms', ['no_lambung' => 'LV-99']);
        $log = KonsumsiBbm::where('no_lambung', 'LV-99')->first();

        // 2. Update
        $updateResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->putJson('/bbm/'.$log->id, [
                'no_lambung' => 'LV-99',
                'jenis_kendaraan' => 'LV',
                'driver' => 'Pak Driver BBM Updated',
                'tanggal' => now()->format('Y-m-d'),
                'odometer' => 600,
                'jumlah_bbm' => 60,
                'tipe_bbm' => 'Solar Industri',
            ]);

        $updateResponse->assertStatus(200);
        $this->assertEquals('Pak Driver BBM Updated', $log->fresh()->driver);

        // 3. Delete
        $deleteResponse = $this->withSession(['user_id' => $this->admin->id, 'user_peran' => 'admin'])
            ->deleteJson('/bbm/'.$log->id);
        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('konsumsi_bbms', ['id' => $log->id]);
    }
}
