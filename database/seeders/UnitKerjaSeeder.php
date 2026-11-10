<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;
use Illuminate\Support\LazyCollection;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('Data Seeder Puskesmas.csv');

        if (! file_exists($filePath)) {
            $this->command->error("CSV file not found at: {$filePath}");

            return;
        }

        LazyCollection::make(function () use ($filePath) {
            $handle = fopen($filePath, 'r');
            $header = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                yield array_combine($header, $row);
            }

            fclose($handle);
        })->each(function (array $unitKerja): void {
            $isDinkes = str_contains(strtolower($unitKerja['nama']), 'dinas kesehatan')
                || ($unitKerja['email'] ?? null) === 'dinkes@surabaya.go.id';

            UnitKerja::updateOrCreate(
                ['email' => $unitKerja['email']],
                [
                    ...$unitKerja,
                    'jenis' => $isDinkes ? 'Dinas_Kesehatan' : 'Puskesmas',
                ]
            );
        });

        UnitKerja::updateOrCreate(
            ['email' => 'dinkes@surabaya.go.id'],
            [
                'nama' => 'Dinas Kesehatan',
                'jenis' => 'Dinas_Kesehatan',
                'alamat' => 'Jl. Raya Jemursari No.197, Sidosermo, Kec. Wonocolo, Surabaya, Jawa Timur',
                'kepala_upt' => 'dr. Billy Daniel Messakh, Sp.B',
                'nomor_telepon' => '(031) 8439473',
                'email' => 'dinkes@surabaya.go.id',
            ]
        );
    }
}
