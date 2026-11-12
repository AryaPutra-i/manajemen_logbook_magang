<?php

namespace App\Services;

use App\Models\Departemen;
use App\Models\UnitKerja;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ManajemenDepartemen
{
    public function getPageData(int $perPage = 10): LengthAwarePaginator
    {
        return Departemen::with('unitKerja')
            ->latest()
            ->paginate($perPage);
    }

    public function getUnitKerjaData()
    {
        return UnitKerja::orderBy('nama')->get();
    }

    public function createDepartemen(array $data): Departemen
    {
        return DB::transaction(function () use ($data) {
            return Departemen::create([
                'nama' => $data['nama'],
                'unit_kerja_id' => $data['unit_kerja_id'],
            ]);
        });
    }

    public function getDepartemenById(int $id): Departemen
    {
        return Departemen::with('unitKerja')->findOrFail($id);
    }

    public function updateDepartemen(
        Departemen $departemen,
        array $data
    ): Departemen {
        return DB::transaction(function () use ($departemen, $data) {

            $departemen->update([
                'nama' => $data['nama'],
                'unit_kerja_id' => $data['unit_kerja_id'],
            ]);

            return $departemen;
        });
    }

    public function deleteDepartemen(Departemen $departemen): bool
    {
        return $departemen->delete();
    }

    
}
