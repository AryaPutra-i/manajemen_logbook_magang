<?php

namespace Database\Seeders;

use App\Models\KategoriPelayananKesehatan;
use Illuminate\Database\Seeder;

class KategoriPelayananKesehatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KategoriPelayananKesehatan::factory(5)->create();
    }
}
