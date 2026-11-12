<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departemen;
use App\Http\Requests\ManajemenDepartemen as ManajemenDepartemenRequest;
use App\Services\ManajemenDepartemen as ManajemenDepartemenService;

class ManajemenDepartemen extends Controller
{
    protected ManajemenDepartemenService $manajemenDepartemenService;

    public function __construct(
        ManajemenDepartemenService $manajemenDepartemenService
    ) {
        $this->manajemenDepartemenService = $manajemenDepartemenService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $departemens = $this->manajemenDepartemenService
            ->getPageData($perPage);

        return view('manajemen-departemen', compact('departemens'));
    }

    public function create()
{
    $departemen = null;

    $unitKerjas = $this->manajemenDepartemenService
        ->getUnitKerjaData();

    return view('form-tambah-departemen', compact(
        'departemen',
        'unitKerjas'
    ));
}

    public function store(ManajemenDepartemenRequest $request)
    {
        $validData = $request->validated();

        $this->manajemenDepartemenService
            ->createDepartemen($validData);

        return redirect()
            ->route('manajemen-departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit($id)
{
    $departemen = $this->manajemenDepartemenService
        ->getDepartemenById($id);

    $unitKerjas = $this->manajemenDepartemenService
        ->getUnitKerjaData();

    return view('form-tambah-departemen', compact(
        'departemen',
        'unitKerjas'
    ));
}


    public function update(ManajemenDepartemenRequest $request, $id)
    {
        $validData = $request->validated();

        $departemen = $this->manajemenDepartemenService
            ->getDepartemenById($id);

        $this->manajemenDepartemenService
            ->updateDepartemen($departemen, $validData);

        return redirect()
            ->route('manajemen-departemen.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $departemen = $this->manajemenDepartemenService
            ->getDepartemenById($id);

        $this->manajemenDepartemenService
            ->deleteDepartemen($departemen);

        return redirect()
            ->route('manajemen-departemen.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
