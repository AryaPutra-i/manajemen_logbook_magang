<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory(9)->create();
        $dinkesId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');

        User::factory()->create([
            'username' => 'admin_dkk_2',
            'password' => 'password123',
            'role' => 'admin',
            'nip' => null,
            'nik' => null,
            'nim' => null,
            'alamat' => null,
            'universitas' => null,
            'unit_kerja_id' => null,
            'departemen_id' => null,
        ]);

        User::factory()->create([
            'username' => 'dokter.magang_2',
            'password' => 'password123',
            'role' => 'dokter_magang',
            'nip' => null,
            'nik' => fake()->regexify('[0-9]{16}'),
            'nim' => fake()->regexify('[0-9]{10}'),
            'alamat' => fake()->streetAddress(),
            'universitas' => fake()->company(),
            'unit_kerja_id' => 1,
            'departemen_id' => null,

        ]);

        User::factory()->create([
            'username' => 'pembimbing.lapangan_2',
            'password' => 'password123',
            'role' => 'pembimbing_lapangan',
            'nip' => fake()->regexify('[0-9]{18}'),
            'nik' => null,
            'nim' => null,
            'alamat' => null,
            'universitas' => null,
            'unit_kerja_id' => 1,
            'departemen_id' => null,
        ]);

        User::factory()->create([
            'username' => 'mahasiswa.magang_2',
            'password' => 'password123',
            'role' => 'mahasiswa_magang',
            'nip' => null,
            'nik' => fake()->regexify('[0-9]{16}'),
            'nim' => fake()->regexify('[0-9]{10}'),
            'alamat' => fake()->streetAddress(),
            'universitas' => fake()->company(),
            'unit_kerja_id' => $dinkesId,
            'departemen_id' => 1,

        ]);
        User::factory()->create([
            'username' => 'pembimbing.lapangan.com',
            'password' => 'password123',
            'role' => 'pembimbing_lapangan',
            'nip' => fake()->regexify('[0-9]{18}'),
            'nik' => null,
            'nim' => null,
            'alamat' => null,
            'universitas' => null,
            'unit_kerja_id' => $dinkesId,
            'departemen_id' => 1,

        ]);
    }
}
