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
        ]);
        
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('users')->where('role', 'pembimbing_lapangan_mahasiswa')->exists()){
            throw new \RuntimeException("Tidak bisa menambahkan constraint karena ada data dengan role 'pembimbing_lapangan_mahasiswa' yang masih ada di tabel users.");
        }
        $this->updateRoleConstraint([
            'dokter_magang',
            'pembimbing_lapangan',
            'admin',
            'mahasiswa_magang',
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
            $table->dropColumn('role');
            $table->enum('role', $roles)->nullable(false)->change();
        });
    }
};
