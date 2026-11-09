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
        $table = 'log_mahasiswa_magangs';

        if (! Schema::hasTable($table)) {
            return;
        }

        $hasLegacyColumn = Schema::hasColumn($table, 'puskesmas_id');
        $hasCurrentColumn = Schema::hasColumn($table, 'unit_kerja_id');

        if ($hasLegacyColumn && $hasCurrentColumn) {
            throw new RuntimeException("Both puskesmas_id and unit_kerja_id exist on {$table}; resolve the duplicate columns before migrating.");
        }

        if (! $hasLegacyColumn && ! $hasCurrentColumn) {
            throw new RuntimeException("Neither puskesmas_id nor unit_kerja_id exists on {$table}.");
        }

        if ($hasLegacyColumn) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->renameColumn('puskesmas_id', 'unit_kerja_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = 'log_mahasiswa_magangs';

        if (! Schema::hasTable($table)) {
            return;
        }

        $hasLegacyColumn = Schema::hasColumn($table, 'puskesmas_id');
        $hasCurrentColumn = Schema::hasColumn($table, 'unit_kerja_id');

        if ($hasLegacyColumn && $hasCurrentColumn) {
            throw new RuntimeException("Both puskesmas_id and unit_kerja_id exist on {$table}; resolve the duplicate columns before rolling back.");
        }

        if (! $hasLegacyColumn && ! $hasCurrentColumn) {
            throw new RuntimeException("Neither puskesmas_id nor unit_kerja_id exists on {$table}.");
        }

        if ($hasCurrentColumn) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->renameColumn('unit_kerja_id', 'puskesmas_id');
            });
        }
    }
};
