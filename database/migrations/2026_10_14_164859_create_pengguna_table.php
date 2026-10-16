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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayah')->onDelete('cascade');
            $table->unsignedBigInteger('atasan_id')->nullable();
            $table->foreign('atasan_id')->references('id')->on('pengguna')->onDelete('set null');
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('peran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
