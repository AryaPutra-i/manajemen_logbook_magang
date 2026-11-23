<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\UnitKerja;
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
        ];

        $dinkes = UnitKerja::where('jenis', 'Dinas_Kesehatan')->first();

        foreach ($daftarDepartemen as $nama) {
            Departemen::updateOrCreate(
                ['nama' => $nama],
                ['unit_kerja_id' => $dinkes?->id]
            );
        }
    }
}
