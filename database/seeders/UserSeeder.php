<?php

namespace Database\Seeders;
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

        User::factory()->create([
            'username' => 'admin_dkk',
            'password' => 'password123',
            'role' => 'admin',
            'nip' => null,
            'nik' => null,
            'nim' => null,
            'alamat' => null,
            'universitas' => null,
            'puskesmas_id' => null,
        ]);

        User::factory()->create([
            'username' => 'dokter.magang',
            'password' => 'password123',
            'role' => 'dokter_magang',
            'nip' => null,
            'nik' => fake()->regexify('[0-9]{16}'),
            'nim' => fake()->regexify('[0-9]{10}'),
            'alamat' => fake()->streetAddress(),
            'universitas' => fake()->company(),
            'puskesmas_id' => 1,

        ]);

        User::factory()->create([
            'username' => 'pembimbing.lapangan',
            'password' => 'password123',
            'role' => 'pembimbing_lapangan',
            'nip' => fake()->regexify('[0-9]{18}'),
            'nik' => null,
            'nim' => null,
            'alamat' => null,
            'universitas' => null,
            'puskesmas_id' => 1,
        ]);

        User::factory()->create([
            'username' => 'mahasiswa.magang',
            'password' => 'password123',
            'role' => 'mahasiswa_magang',
            'nip' => null,
            'nik' => fake()->regexify('[0-9]{16}'),
            'nim' => fake()->regexify('[0-9]{10}'),
            'alamat' => fake()->streetAddress(),
            'universitas' => fake()->company(),
            'puskesmas_id' => null,

        ]);
    }
}
