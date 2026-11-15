<?php

namespace App\Services;

use App\Models\Departemen;
use App\Models\UnitKerja;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ManajemenDepartemen
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function getPageData(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Departemen::with('unitKerja');

        if (! empty($filters['tanggal_mulai'])) {
            $query->whereDate('created_at', '>=', $filters['tanggal_mulai']);
        }

        if (! empty($filters['tanggal_akhir'])) {
            $query->whereDate('created_at', '<=', $filters['tanggal_akhir']);
        }

        if (! empty($filters['unit_kerja'])) {
            $unitKerja = $filters['unit_kerja'];
            $query->whereHas('unitKerja', function ($q) use ($unitKerja): void {
                if (is_numeric($unitKerja)) {
                    $q->where('id', (int) $unitKerja);
                } else {
                    $q->where('nama', 'like', "%{$unitKerja}%");
                }
            });
        }

        if (! empty($filters['nama'])) {
            $query->where('nama', 'like', "%{$filters['nama']}%");
        }

        return $query->latest()
            ->paginate($perPage)
            ->withQueryString();
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
