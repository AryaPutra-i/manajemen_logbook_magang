<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ManajemenDepartemen as ManajemenDepartemenRequest;
use App\Services\ManajemenDepartemen as ManajemenDepartemenService;

class ManajemenDepartemen extends Controller
{
    protected ManajemenDepartemenService $manajemenDepartemenService;

    public function __construct(ManajemenDepartemenService $manajemenDepartemenService)
    {
        $this->manajemenDepartemenService = $manajemenDepartemenService;
    }

    public function index(){
        $perPage = $request->input('per_page', 10);
        $unitKerja = $this->manajemenDepartemenService->getPageData($perPage);

        return view('manajemen-departemen.index', compact('unitKerja'));
    }

    public function create(){
        return view('manajemen-departemen.create');
    }

    public function store(ManajemenenDepartemenRequest $request){
        $validData = $request->validated();
        $this->ManajemenDepartemenService->createUnitKerja($validData);

        return redirect()->route('manajemen-departemen.index')->with('success', 'Unit Kerja berhasil ditambahkan.');    
    }

    public function edit(ManajemenDepartemenService $manajemenDepartemenService){
        $unitKerja = UnitKerja::findOrFail($id);
        return view('manajemen-departemen.edit', compact('unitKerja'));
    }

    public function update(ManajemenDepartemenRequest $request, ManajemenDepartemenService $manajemenDepartemenService){
        $validData = $request->validated();
        $unitKerja = UnitKerja::findOrFail($id);
        $this->manajemenDepartemenService->updateUnitKerja($unitKerja, $validData);

        return redirect()->route('manajemen-departemen.index')->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    public function destroy(ManajemenDepartemenService $manajemenDepartemenService){
        $unitKerja = UnitKerja::findOrFail($id);
        $this->manajemenDepartemenService->deleteUnitKerja($unitKerja);

        return redirect()->route('manajemen-departemen.index')->with('success', 'Unit Kerja berhasil dihapus.');
    }


}
