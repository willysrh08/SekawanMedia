<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemeliharaanKendaraan extends Model
{
    protected $table = 'pemeliharaan_kendaraan';

    protected $guarded = [];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }
}
