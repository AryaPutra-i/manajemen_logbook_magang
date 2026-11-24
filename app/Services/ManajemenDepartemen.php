<?php

namespace App\Services;

use App\Models\Departemen;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
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

        if (! empty($filters['unit_kerja_id'])) {
            $query->where('unit_kerja_id', $filters['unit_kerja_id']);
        }

        if (! empty($filters['nama'])) {
            $query->where('nama', 'like', "%{$filters['nama']}%");
        }

        return $query->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getDataTableData(Request $request, $user): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $baseQuery = Departemen::query();
        if ($user->role === 'admin_unit_kerja') {
            $baseQuery->where('unit_kerja_id', $user->unit_kerja_id);
        }
        $recordsTotal = $baseQuery->count();

        $query = Departemen::with('unitKerja');
        if ($user->role === 'admin_unit_kerja') {
            $query->where('departemens.unit_kerja_id', $user->unit_kerja_id);
        }

        $tanggalMulai = $request->input('tanggalMulaiPembuatanDepartemen') ?: $request->input('tanggal_mulai');
        if (is_string($tanggalMulai) && trim($tanggalMulai) !== '') {
            $query->whereDate('departemens.created_at', '>=', trim($tanggalMulai));
        }

        $tanggalAkhir = $request->input('tanggalAkhirPembuatanDepartemen') ?: $request->input('tanggal_akhir');
        if (is_string($tanggalAkhir) && trim($tanggalAkhir) !== '') {
            $query->whereDate('departemens.created_at', '<=', trim($tanggalAkhir));
        }

        $unitKerjaInput = $request->input('unit_kerja');
        if ($unitKerjaInput === null && $request->has('unit_kerjas')) {
            $unitKerjasParam = $request->input('unit_kerjas');
            $unitKerjaInput = is_array($unitKerjasParam) ? ($unitKerjasParam[0] ?? null) : $unitKerjasParam;
        }
        if (is_array($unitKerjaInput)) {
            $unitKerjaInput = $unitKerjaInput[0] ?? null;
        }
        if (! empty($unitKerjaInput)) {
            $query->whereHas('unitKerja', function ($q) use ($unitKerjaInput): void {
                if (is_numeric($unitKerjaInput)) {
                    $q->where('id', (int) $unitKerjaInput);
                } else {
                    $q->where('nama', 'like', "%{$unitKerjaInput}%");
                }
            });
        }

        $search = $request->input('search');
        $searchValue = is_array($search) ? ($search['value'] ?? '') : (string) $search;
        $searchValue = trim($searchValue);
        if ($searchValue !== '') {
            $query->where(function ($q) use ($searchValue) {
                $q->where('departemens.nama', 'like', "%{$searchValue}%")
                    ->orWhereHas('unitKerja', function ($qu) use ($searchValue) {
                        $qu->where('nama', 'like', "%{$searchValue}%")
                            ->orWhere('jenis', 'like', "%{$searchValue}%");
                    });
            });
        }

        $recordsFiltered = (clone $query)->count('departemens.id');

        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $columnMap = [
            1 => 'departemens.nama',
            2 => 'unit_kerja',
            3 => 'unit_kerja_jenis',
            4 => 'departemens.created_at',
        ];

        if (isset($columnMap[$orderColumnIndex])) {
            $col = $columnMap[$orderColumnIndex];
            if ($col === 'unit_kerja') {
                $query->leftJoin('unit_kerjas', 'departemens.unit_kerja_id', '=', 'unit_kerjas.id')
                    ->orderBy('unit_kerjas.nama', $orderDir)
                    ->select('departemens.*');
            } elseif ($col === 'unit_kerja_jenis') {
                $query->leftJoin('unit_kerjas', 'departemens.unit_kerja_id', '=', 'unit_kerjas.id')
                    ->orderBy('unit_kerjas.jenis', $orderDir)
                    ->select('departemens.*');
            } else {
                $query->orderBy($col, $orderDir);
            }
        } else {
            $query->latest('departemens.created_at');
        }

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $departemens = $query->get();

        $data = [];
        $csrfToken = csrf_token();
        foreach ($departemens as $index => $departemen) {
            $editUrl = route('manajemen-departemen.edit', $departemen->id);
            $destroyUrl = route('manajemen-departemen.destroy', $departemen->id);

            $jenisUnitKerja = '-';
            if ($departemen->unitKerja) {
                $jenisUnitKerja = $departemen->unitKerja->jenis === 'Dinas_Kesehatan'
                    ? 'Dinas Kesehatan'
                    : 'Puskesmas';
            }

            $aksi = '<div class="d-flex gap-1">'
                . '<a href="' . $editUrl . '" class="btn btn-success btn-sm">'
                . '<i class="bi bi-pencil-square text-white"></i>'
                . '</a>'
                . '<form action="' . $destroyUrl . '" method="POST" onsubmit="return confirm(\'Yakin ingin menghapus departemen ini?\')">'
                . '<input type="hidden" name="_token" value="' . $csrfToken . '">'
                . '<input type="hidden" name="_method" value="DELETE">'
                . '<button type="submit" class="btn btn-danger btn-sm">'
                . '<i class="bi bi-trash-fill text-white"></i>'
                . '</button>'
                . '</form>'
                . '</div>';

            $data[] = [
                'no' => $start + $index + 1,
                'nama' => e($departemen->nama),
                'unit_kerja' => e($departemen->unitKerja?->nama ?? '-'),
                'jenis_unit_kerja' => $jenisUnitKerja,
                'tanggal_dibuat' => $departemen->created_at?->format('d-m-Y') ?? '-',
                'aksi' => $aksi,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function getUnitKerjaData(?int $unitKerjaId = null)
    {
        return UnitKerja::when($unitKerjaId !== null, fn($q) => $q->where('id', $unitKerjaId))
            ->orderBy('nama')
            ->get();
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
