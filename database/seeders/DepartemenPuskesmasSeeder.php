<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class DepartemenPuskesmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarDepartemen = [
            'Tenaga Teknis Kefarmasian/Asisten Apoteker',
            'Apoteker',
            'Terapi Gigi dan Mulut',
            'Fisioterapis',
            'Analis Laboratorium',
            'Radiografer',
            'Ahli Gizi',
            'Psikolog Klinis',
            'Penata Anestesi',
            'Perekam Medis',
            'Perawat',
            'Teknisi Pemeliharaan Sarana Non Medis',
            'Teknisi Elektromedik',
            'Bidan',
            'Pranata Laboratorium',
            'Analis Hukum',
            'Pranata Humas',
            'Teknisi Informatika (IT)',
            'Administrasi Perkantoran',
            'Analis Bangunan Gedung dan Pemukiman',
        ];

        UnitKerja::query()
            ->where('jenis', 'Puskesmas')
            ->with('departemens:id,nama,unit_kerja_id')
            ->get(['id'])
            ->each(function (UnitKerja $unitKerja) use ($daftarDepartemen): void {
                $departemenYangSudahAda = $unitKerja->departemens
                    ->pluck('nama')
                    ->all();

                foreach (array_diff($daftarDepartemen, $departemenYangSudahAda) as $namaDepartemen) {
                    Departemen::create([
                        'unit_kerja_id' => $unitKerja->id,
                        'nama' => $namaDepartemen,
                    ]);
                }
            });
    }
}
