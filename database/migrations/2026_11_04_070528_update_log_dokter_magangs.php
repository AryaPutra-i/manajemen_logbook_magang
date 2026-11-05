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
        Schema::table('log_dokter_magangs', function (BLueprint $table){
            $table->renameColumn('puskesmas_id', 'unit_kerja_id');
            // $table->foreignId('unit_kerja_id')->constrained('unit_kerjas')->cascadeOnDelete()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_dokter_magangs', function (BLueprint $table){
            $table->renameColumn('unit_kerja_id', 'puskesmas_id');
            // $table->dropColumn('unit_kerja_id');
        });
    }
};
