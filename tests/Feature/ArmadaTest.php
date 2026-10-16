<?php

namespace Tests\Feature;

use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArmadaTest extends TestCase
{
    use RefreshDatabase;

    public function test_simpan_unit_baru_redirect_back_dengan_pesan_mobil_telah_ditambahkan(): void
    {
        $wilayah = Wilayah::create([
            'nama' => 'Site 01 Pomalaa',
            'tipe' => 'tambang',
        ]);

        $response = $this->withSession([
            'user_id' => 1,
            'user_nama' => 'Admin',
            'user_peran' => 'admin',
            'wilayah_id' => $wilayah->id,
        ])->from('/armada')->post('/armada/store', [
            'merek' => 'Toyota',
            'model' => 'Hilux Double Cabin 4x4',
            'plat_nomor' => 'DT 8899 XY',
            'status' => 'tersedia',
        ]);

        // Memastikan tidak dibawa ke halaman lain (tetap redirect kembali ke /armada)
        $response->assertRedirect('/armada');
        $response->assertSessionHas('success', 'Mobil telah ditambahkan.');

        // Memastikan unit tersimpan di database
        $this->assertDatabaseHas('kendaraan', [
            'plat_nomor' => 'DT 8899 XY',
            'merek' => 'Toyota',
            'model' => 'Hilux Double Cabin 4x4',
            'status' => 'tersedia',
        ]);
    }
}
