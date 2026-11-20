<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ROLES = [
        'dokter_magang',
        'pembimbing_lapangan',
        'admin',
        'mahasiswa_magang',
        'admin_unit_kerja',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('puskesmas_id', 'unit_kerja_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->constrained('departemens')->cascadeOnDelete();
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerjas')->cascadeOnDelete();
        });

        $this->updateRoleConstraints(self::ROLES);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $newRoles = [
            'mahasiswa_magang',
            'admin_unit_kerja',
        ];

        foreach ($newRoles as $role) {
            if (DB::table('users')->where('role', $role)->exists()) {
                throw new RuntimeException(
                    "Tidak bisa rollback karena masih terdapat user dengan role '{$role}'."
                );
            }
        }

        $this->updateRoleConstraints([
            'dokter_magang',
            'pembimbing_lapangan',
            'admin',
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('departemen_id');
            $table->renameColumn('unit_kerja_id', 'puskesmas_id');
            $table->foreign('puskesmas_id')->references('id')->on('unit_kerjas')->cascadeOnDelete();
        });
    }

    /**
     * Update PostgreSQL's role check constraint without changing user rows.
     */
    private function updateRoleConstraints(array $roles): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $values = implode(', ', array_map(
                fn (string $role): string => DB::connection()->getPdo()->quote($role),
                $roles
            ));

            DB::statement('ALTER TABLE "users" DROP CONSTRAINT IF EXISTS "users_role_check"');
            DB::statement(
                'ALTER TABLE "users" ADD CONSTRAINT "users_role_check" CHECK ("role" IN ('
                .$values
                .'))'
            );
            DB::statement('ALTER TABLE "users" ALTER COLUMN "role" SET NOT NULL');

            return;
        }

        Schema::table('users', function (Blueprint $table) use ($roles) {
            $table->enum('role', $roles)->nullable(false)->change();
        });
    }
};
