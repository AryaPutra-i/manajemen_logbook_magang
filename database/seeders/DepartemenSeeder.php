<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarDepartemen = [
            'Sekretariat',
            'Bidang Kesehatan Masyarakat',
            'Bidang Pencegahan dan Pengendalian Penyakit',
            'Bidang Pelayanan Kesehatan',
            'Bidang Sumber Daya Kesehatan',
            'UPTD Laboratorium Kesehatan Daerah',
            'UPTD Instalasi Farmasi',
        ];

        foreach ($daftarDepartemen as $nama) {
            Departemen::firstOrCreate(['nama' => $nama]);
        }
    }
}
