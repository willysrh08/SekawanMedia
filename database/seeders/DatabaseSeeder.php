<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Insert Data Wilayah (Kantor Pusat & Tambang)
        $wilayahPusat = DB::table('wilayah')->insertGetId([
            'nama' => 'Kantor Pusat Jakarta',
            'tipe' => 'kantor_pusat',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $wilayahTambang = DB::table('wilayah')->insertGetId([
            'nama' => 'Tambang Nikel Blok A',
            'tipe' => 'tambang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert Approver Level 2 (Manajer / Kepala Wilayah)
        $manajerId = DB::table('pengguna')->insertGetId([
            'wilayah_id' => $wilayahPusat,
            'atasan_id' => null,
            'nama' => 'Manajer(L2)',
            'email' => 'manajer@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'manajer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Insert Approver Level 1 (Atasan Langsung) dengan atasan_id merujuk ke Manajer
        $atasanId = DB::table('pengguna')->insertGetId([
            'wilayah_id' => $wilayahTambang,
            'atasan_id' => $manajerId,
            'nama' => 'Atasan(L1)',
            'email' => 'atasan1@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'atasan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Insert Pegawai (Pemesan) dengan atasan_id merujuk ke Atasan L1
        DB::table('pengguna')->insert([
            'wilayah_id' => $wilayahTambang,
            'atasan_id' => $atasanId,
            'nama' => 'Kezi',
            'email' => 'pegawai@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'pegawai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Insert Admin Pool
        DB::table('pengguna')->insert([
            'wilayah_id' => $wilayahPusat,
            'atasan_id' => null,
            'nama' => 'Admin',
            'email' => 'admin@sekawan.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Insert Driver Contoh
        DB::table('driver')->insert([
            'wilayah_id' => $wilayahTambang,
            'nama' => 'Budi Driver',
            'telepon' => '081234567890',
            'status' => 'tersedia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Insert Kendaraan Contoh (Milik Perusahaan & Sewa)
        DB::table('kendaraan')->insert([
            'wilayah_id' => $wilayahTambang,
            'nama' => 'Toyota Hilux 4x4 (Tambang)',
            'plat_nomor' => 'N 1234 AB',
            'jenis' => 'barang',
            'kepemilikan' => 'milik_perusahaan',
            'kapasitas' => 2,
            'status' => 'tersedia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
