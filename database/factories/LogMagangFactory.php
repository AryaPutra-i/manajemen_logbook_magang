<?php

namespace Database\Factories;

use App\Models\KategoriPelayananKesehatan;
use App\Models\LogMagang;
use App\Models\Puskesmas;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LogMagang>
 */
class LogMagangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $daftarKategoriPelayananKesehatan = KategoriPelayananKesehatan::select('nama')->pluck('nama')->toArray();
        $jsonData = [];
        foreach ($daftarKategoriPelayananKesehatan as $kategori) {
            $jsonData[$kategori] = fake()->numberBetween(1, 100);
        }

        return [
            'tanggal' => fake()->date(),
            'jumlah_pasien_per_kategori_pelayanan_kesehatan' => $jsonData,
            'deskripsi' => fake()->text(),
            'hambatan' => fake()->text(),
            'dokumentasi' => 'uploads/dummy.jpeg',
            'puskesmas_id' => fake()->randomElement(Puskesmas::select('id')->pluck('id')->toArray()),
            'dokter_magang_id' => fake()->randomElement(User::where('role', UserRole::DokterMagang)->pluck('id')->toArray()),
            'pembimbing_lapangan_id' => fake()->randomElement(User::where('role', UserRole::PembimbingLapangan)->pluck('id')->toArray()),
        ];
    }
}
