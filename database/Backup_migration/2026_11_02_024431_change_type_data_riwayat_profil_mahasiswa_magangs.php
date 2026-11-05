<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('riwayat_profil_mahasiswa_magangs', function (Blueprint $table) {
            $table->string('nik')->change();
            $table->string('nim')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_profil_mahasiswa_magangs', function (Blueprint $table) {
            $table->integer('nik')->change();
            $table->integer('nim')->change();
        });
    }
};
