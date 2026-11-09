<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const APPLICATION_ROLES = [
        'admin',
        'dokter_magang',
        'mahasiswa_magang',
        'pembimbing_lapangan',
    ];

    private const LEGACY_SUPERVISOR_ROLES = [
        'pembimbing_lapangan_departemen',
        'pembimbing_lapangan_puskesmas',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE "users" DROP CONSTRAINT IF EXISTS "users_role_check"');
            DB::table('users')
                ->whereIn('role', self::LEGACY_SUPERVISOR_ROLES)
                ->update(['role' => 'pembimbing_lapangan']);
            $this->setPostgresRoleConstraint(self::APPLICATION_ROLES);

            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', [...self::APPLICATION_ROLES, ...self::LEGACY_SUPERVISOR_ROLES])->change();
        });

        DB::table('users')
            ->whereIn('role', self::LEGACY_SUPERVISOR_ROLES)
            ->update(['role' => 'pembimbing_lapangan']);

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', self::APPLICATION_ROLES)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('users')->where('role', 'pembimbing_lapangan')->exists()) {
            throw new RuntimeException(
                "Tidak bisa rollback karena role 'pembimbing_lapangan' masih digunakan. "
                .'Migrasikan atau hapus akun pembimbing sebelum rollback.'
            );
        }

        $legacyRoles = [
            'admin',
            'dokter_magang',
            'mahasiswa_magang',
            ...self::LEGACY_SUPERVISOR_ROLES,
        ];

        if (DB::connection()->getDriverName() === 'pgsql') {
            $this->setPostgresRoleConstraint($legacyRoles);

            return;
        }

        Schema::table('users', function (Blueprint $table) use ($legacyRoles): void {
            $table->enum('role', $legacyRoles)->change();
        });
    }

    /**
     * Update PostgreSQL's role check constraint without changing unrelated columns.
     *
     * @param  list<string>  $roles
     */
    private function setPostgresRoleConstraint(array $roles): void
    {
        $values = implode(', ', array_map(
            fn (string $role): string => DB::connection()->getPdo()->quote($role),
            $roles,
        ));

        DB::statement(
            'ALTER TABLE "users" ADD CONSTRAINT "users_role_check" CHECK ("role" IN ('
            .$values
            .'))'
        );
    }
};
