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

        Schema::rename('puskesmas', 'unit_kerjas');

        Schema::table('unit_kerjas', function (Blueprint $table) {
            $table->enum('jenis', ['Dinas_Kesehatan', 'Puskesmas'])->default('Dinas_Kesehatan')->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_kerjas', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
