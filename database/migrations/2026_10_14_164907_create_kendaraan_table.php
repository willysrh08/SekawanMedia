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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayah')->onDelete('cascade');
            $table->string('nama');
            $table->string('merek')->nullable();
            $table->string('model')->nullable();
            $table->string('plat_nomor')->unique();
            $table->string('jenis');
            $table->string('kepemilikan');
            $table->integer('kapasitas');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
