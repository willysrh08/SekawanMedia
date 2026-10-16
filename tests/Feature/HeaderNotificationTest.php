<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\Pemesanan;
use App\Models\Pengguna;
use App\Models\PersetujuanPemesanan;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HeaderNotificationTest extends TestCase
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

    public function test_header_does_not_contain_settings_button()
    {
        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_email' => $this->admin->email,
            'user_peran' => 'admin',
        ])->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('title="Pengaturan"', false);
        $response->assertDontSee('>settings<', false);
    }

    public function test_header_displays_notification_for_pending_approval()
    {
        $kendaraan = Kendaraan::create([
            'wilayah_id' => $this->wilayah->id,
            'nama' => 'Toyota Hilux 4x4',
            'merek' => 'Toyota',
            'model' => 'Hilux Double Cabin',
            'plat_nomor' => 'DT 1001 SK',
            'jenis' => 'penumpang',
            'kepemilikan' => 'milik_sendiri',
            'kapasitas' => 5,
            'status' => 'tersedia',
        ]);

        $pemesanan = Pemesanan::create([
            'pegawai_id' => $this->admin->id,
            'kendaraan_id' => $kendaraan->id,
            'lokasi_asal' => 'Main Office',
            'tujuan' => 'Pit Barat Tambang',
            'waktu_mulai' => now()->addDay(),
            'waktu_selesai' => now()->addDays(2),
            'keperluan' => 'Inspeksi Geoteknik Rutin',
            'status' => 'tertunda',
        ]);

        PersetujuanPemesanan::create([
            'pemesanan_id' => $pemesanan->id,
            'approver_id' => $this->admin->id,
            'level' => 1,
            'status' => 'tertunda',
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_nama' => $this->admin->nama,
            'user_email' => $this->admin->email,
            'user_peran' => 'admin',
        ])->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Persetujuan Pemesanan');
        $response->assertSee('#REQ-'.$pemesanan->id);
        $response->assertSee('Inspeksi Geoteknik Rutin');
        $response->assertSee('Butuh Persetujuan Atasan (L1)');
    }
}
