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
        Schema::rename('log_magangs', 'log_dokter_magangs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('log_dokter_magangs', 'log_magangs');
    }
};
