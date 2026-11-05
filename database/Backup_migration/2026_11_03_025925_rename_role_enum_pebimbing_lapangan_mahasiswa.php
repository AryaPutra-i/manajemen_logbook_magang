<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->updateRoleConstraint([
            'dokter_magang',
            'pembimbing_lapangan',
            'admin',
            'mahasiswa_magang',
            'pembimbing_lapangan_mahasiswa',
            'pembimbing_lapangan_departemen',
            'pembimbing_lapangan_puskesmas',
        ]);

        DB::table('users')->where('role', 'pembimbing_lapangan_mahasiswa')->update(['role' => 'pembimbing_lapangan_departemen']);
        DB::table('users')->where('role', 'pembimbing_lapangan')->update(['role' => 'pembimbing_lapangan_puskesmas']);

        $this->updateRoleConstraint([
            'dokter_magang',
            'pembimbing_lapangan_puskesmas',
            'admin',
            'mahasiswa_magang',
            'pembimbing_lapangan_departemen',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->updateRoleConstraint([
            'dokter_magang',
            'pembimbing_lapangan',
            'admin',
            'mahasiswa_magang',
            'pembimbing_lapangan_mahasiswa',
            'pembimbing_lapangan_departemen',
            'pembimbing_lapangan_puskesmas',
        ]);

        DB::table('users')->where('role', 'pembimbing_lapangan_departemen')->update(['role' => 'pembimbing_lapangan_mahasiswa']);
        DB::table('users')->where('role', 'pembimbing_lapangan_puskesmas')->update(['role' => 'pembimbing_lapangan']);

        $this->updateRoleConstraint([
            'dokter_magang',
            'pembimbing_lapangan',
            'admin',
            'mahasiswa_magang',
            'pembimbing_lapangan_mahasiswa',
        ]);
    }   

    private function updateRoleConstraint(array $roles): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $values = implode(', ', array_map(
                fn (string $role): string => DB::connection()->getPdo()->quote($role),
                $roles,
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
