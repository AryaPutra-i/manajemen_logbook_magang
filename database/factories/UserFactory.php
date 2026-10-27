<?php

namespace Database\Factories;

use App\Models\Puskesmas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['dokter_magang', 'pembimbing_lapangan'];
        $role = fake()->randomElement($roles);

        return [
            'username' => fake()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => $role,
            'nama' => fake()->name(),
            'nip' => $role === 'pembimbing_lapangan' ? fake()->regexify('[0-9]{18}') : null,
            'nik' => $role === 'dokter_magang' ? fake()->regexify('[0-9]{16}') : null,
            'nim' => $role === 'dokter_magang' ? fake()->regexify('[0-9]{10}') : null,
            'alamat' => $role === 'dokter_magang' ? fake()->streetAddress() : null,
            'universitas' => $role === 'dokter_magang' ? fake()->company() : null,
            'puskesmas_id' => fake()->randomElement(Puskesmas::select('id')->pluck('id')->toArray()),
            'remember_token' => Str::random(10),
        ];
    }
}
