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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['puskesmas_id']);
        });

        Schema::rename('puskesmas', 'unit_kerjas');

        Schema::table('unit_kerjas', function (Blueprint $table) {
            $table->enum('jenis', ['Dinas_Kesehatan', 'Puskesmas'])->default('Puskesmas');
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

        Schema::rename('unit_kerjas', 'puskesmas');

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('puskesmas_id')->references('id')->on('puskesmas')->onDelete('cascade');
        });
    }
};
