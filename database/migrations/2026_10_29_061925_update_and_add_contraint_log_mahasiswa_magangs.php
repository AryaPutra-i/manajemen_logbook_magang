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
        Schema::table('log_mahasiswa_magangs', function (Blueprint $table) {
            $table->renameColumn('pembimbing_lapangan_id', 'pembimbing_lapangan_mahasiswa_id');
            $table->string('dokumentasi')->nullable()->after('status_verifikasi');
            $table->foreignId('departemen_id')->after('pembimbing_lapangan_mahasiswa_id')->constrained('departemens')->cascadeOnDelete();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_mahasiswa_magangs', function (Blueprint $table) {
            $table->renameColumn('pembimbing_lapangan_mahasiswa_id', 'pembimbing_lapangan_id');
            $table->dropColumn('dokumentasi');
            $table->dropForeign(['departemen_id']);
            $table->dropColumn('departemen_id');
        });
    }
};
