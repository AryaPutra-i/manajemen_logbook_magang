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
        Schema::table('users', function(Blueprint $table){
            $table->foreignId('puskesmas_id')->nullable()->change();
            $table->foreignId('departemen_id')->nullable()->change();
        });

        Schema::table('log_mahasiswa_magangs', function(Blueprint $table){
            $table->renameColumn('pembimbing_lapangan_mahasiswa_id', 'pembimbing_lapangan_id');
            $table->foreignId('pembimbing_lapangan_id')->nullable()->change();
            $table->foreignId('puskesmas_id')->nullable()->constrained('puskesmas')->cascadeOnDelete()->after('pembimbing_lapangan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function(Blueprint $table){
            $table->foreignId('puskesmas_id')->nullable(false)->change();
            $table->foreignId('departemen_id')->nullable(false)->change();
        });

        Schema::table('log_mahasiswa_magangs', function(Blueprint $table){
            $table->renameColumn('pembimbing_lapangan_id', 'pembimbing_lapangan_mahasiswa_id');
            $table->foreignId('pembimbing_lapangan_mahasiswa_id')->nullable(false)->change();
            $table->dropForeign(['puskesmas_id']);
            $table->dropColumn('puskesmas_id');
        });
    }
};
