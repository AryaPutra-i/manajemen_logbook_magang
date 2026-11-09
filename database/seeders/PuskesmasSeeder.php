<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;
use Illuminate\Support\LazyCollection;

class PuskesmasSeeder extends Seeder
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
            UnitKerja::updateOrCreate(
                ['email' => $unitKerja['email']],
                [...$unitKerja, 'jenis' => 'Puskesmas']
            );
        });
    }
}
