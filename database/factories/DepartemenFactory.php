<?php

namespace Database\Factories;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Departemen>
 */
class DepartemenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement([
                'Sekretariat',
                'Bidang Kesehatan Masyarakat',
                'Bidang Pencegahan dan Pengendalian Penyakit',
                'Bidang Pelayanan Kesehatan',
                'Bidang Sumber Daya Kesehatan',
                'UPTD Laboratorium Kesehatan Daerah',
                'UPTD Instalasi Farmasi',
            ]),
        ];
    }
}
