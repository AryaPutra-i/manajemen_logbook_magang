<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('unit_kerjas')
            ->whereNull('created_at')
            ->update(['jenis' => 'Puskesmas']);
    }

    public function down(): void
    {
        DB::table('unit_kerjas')
            ->whereNull('created_at')
            ->update(['jenis' => 'Dinas_Kesehatan']);
    }
};
