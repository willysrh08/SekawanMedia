<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersetujuanPemesanan extends Model
{
    protected $table = 'persetujuan_pemesanan';

    protected $fillable = [
        'pemesanan_id',
        'approver_id',
        'level',
        'status',
        'catatan',
    ];

    // Relasi ke Pemesanan
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    // Relasi ke Pengguna (Approver)
    public function approver()
    {
        return $this->belongsTo(Pengguna::class, 'approver_id');
    }
}
