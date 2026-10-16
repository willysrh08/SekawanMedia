<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pengguna')->onDelete('cascade');
            $table->foreignId('kendaraan_id')->nullable()->constrained('kendaraan')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('driver')->onDelete('set null');
            $table->string('lokasi_asal')->nullable();
            $table->string('tujuan')->nullable();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->text('keperluan');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
