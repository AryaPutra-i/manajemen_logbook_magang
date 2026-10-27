<?php

namespace Database\Factories;

use App\Models\KategoriPelayananKesehatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriPelayananKesehatan>
 */
class KategoriPelayananKesehatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->sentence(5),
        ];
    }
}
