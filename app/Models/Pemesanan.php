<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $table = 'pemesanan';

    protected $guarded = [];

    public function pegawai()
    {
        return $this->belongsTo(Pengguna::class, 'pegawai_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    // Sesuaikan nama relasi dan pastikan mengarah ke model PersetujuanPemesanan
    public function persetujuanPemesanan()
    {
        return $this->hasMany(PersetujuanPemesanan::class, 'pemesanan_id');
    }

    // Alias jika di controller menggunakan pemanggilan 'persetujuan'
    public function persetujuan()
    {
        return $this->hasMany(PersetujuanPemesanan::class, 'pemesanan_id');
    }
}
