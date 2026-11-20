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
        Schema::table('log_magangs', function (Blueprint $table) {
            $table->dropForeign(['puskesmas_id']);
        });

        Schema::rename('log_magangs', 'log_dokter_magangs');

        Schema::table('log_dokter_magangs', function (Blueprint $table) {
            $table->renameColumn('puskesmas_id', 'unit_kerja_id');
        });

        Schema::table('log_dokter_magangs', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->constrained('departemens')->cascadeOnDelete();
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerjas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_dokter_magangs', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropForeign(['unit_kerja_id']);
        });

        Schema::table('log_dokter_magangs', function (Blueprint $table) {
            $table->renameColumn('unit_kerja_id', 'puskesmas_id');
        });

        Schema::rename('log_dokter_magangs', 'log_magangs');

        Schema::table('log_magangs', function (Blueprint $table) {
            $table->foreign('puskesmas_id')->references('id')->on('unit_kerjas')->cascadeOnDelete();
        });
    }
};
