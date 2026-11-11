<?php

namespace App\Services;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\DB;

class ManajemenDepartemen
{
    /**
     * Create a new class instance.
     */
    public function createUnitKerja(array $data): UnitKerja
    {
        return DB::transaction(function () use ($data) {
            $unitKerja = UnitKerja::create([
                'nama' => $data['nama'],
                'alamat' => $data['alamat'],
                'kepala_upt' => $data['kepala_upt'],
                'nomor_telepon' => $data['nomor_telepon'],
                'email' => $data['email'],
                'kode_puskesmas' => $data['kode_puskesmas'],
                'jenis' => $data['jenis'],
            ]);
            return $unitKerja;
        });
        return $unitKerja;
    }

    public function updateUnitKerja(array $data, UnitKerja $unitKerja): UnitKerja {

        $unitKerjaUpdate->update([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'kepala_upt' => $data['kepala_upt'],
            'nomor_telepon' => $data['nomor_telepon'],
            'email' => $data['email'],
            'kode_puskesmas' => $data['kode_puskesmas'],
            'jenis' => $data['jenis']
        ]);

        return $unitKerjaUpdate;
    }

    public function getPageData(int $perPage = 10): LengthAwarePaginator {
        return UnitKerja::with(['nama', 'alamat', 'kepala_upt', 'nomor_telepon', 'email', 'kode_puskesmas', 'jenis'])
            ->latest()
            ->paginate($perPage);
    }

    public function deleteUnitKerja(int $id): bool {
        $unitKerja = UnitKerja::findOrFail($id);
        return $unitKerja->delete();
    }
}
