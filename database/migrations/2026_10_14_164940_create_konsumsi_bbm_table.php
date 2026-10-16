<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah nama tabel menjadi 'konsumsi_bbms' agar sesuai dengan konvensi plural Laravel
        Schema::create('konsumsi_bbms', function (Blueprint $table) {
            $table->id();
            $table->string('no_lambung'); // Contoh: LV-01, DT-02
            $table->string('jenis_kendaraan'); // LV, Truk, Bus
            $table->string('driver');
            $table->date('tanggal');
            $table->integer('odometer'); // dalam km
            $table->integer('jumlah_bbm'); // dalam Liter
            $table->string('tipe_bbm'); // Solar, Dexlite, dll
            $table->decimal('efisiensi', 5, 2)->nullable(); // km/L
            $table->string('status_efisiensi')->default('Normal'); // Efisien, Normal, Boros
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsumsi_bbms');
    }
};
