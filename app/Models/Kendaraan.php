<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $guarded = [];

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'kendaraan_id');
    }

    public function bbm()
    {
        return $this->hasMany(KonsumsiBbm::class, 'kendaraan_id');
    }

    public function pemeliharaan()
    {
        return $this->hasMany(PemeliharaanKendaraan::class, 'kendaraan_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'kendaraan_id');
    }
}
