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
        Schema::create('riwayat_profil_mahasiswa_magangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('nik')->unique();
            $table->integer('nim');
            $table->string('alamat');
            $table->string('universitas');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_profil_mahasiswa_magangs');
    }
};
