<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $table = 'pengguna';

    protected $fillable = [
        'wilayah_id',
        'atasan_id',
        'nama',
        'email',
        'password',
        'peran',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi ke Wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    // Relasi ke Atasan Langsung (Approver L1 / L2)
    public function atasan()
    {
        return $this->belongsTo(Pengguna::class, 'atasan_id');
    }

    // Relasi ke Bawahan (Pegawai / Atasan di bawahnya)
    public function bawahan()
    {
        return $this->hasMany(Pengguna::class, 'atasan_id');
    }
}
