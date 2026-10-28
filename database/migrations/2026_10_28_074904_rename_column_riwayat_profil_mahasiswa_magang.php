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
        Schema::table('riwayat_profil_mahasiswa_magangs', function (Blueprint $table) {
            $table->renameColumn('user_id', 'mahasiswa_magang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_profil_mahasiswa_magangs', function (Blueprint $table) {
            $table->renameColumn('mahasiswa_magang_id', 'user_id');
        });
    }
};
