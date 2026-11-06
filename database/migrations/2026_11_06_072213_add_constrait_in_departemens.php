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
        Schema::table('departemens', function (Blueprint $table) {
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerjas')->cascadeOnDelete()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departemens', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
        });
    }
};
