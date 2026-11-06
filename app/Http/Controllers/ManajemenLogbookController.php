<?php

namespace App\Http\Controllers;

use App\Models\KategoriPelayananKesehatan;
use App\Models\LogDokterMagang;
use App\Models\LogMahasiswaMagang;
use Illuminate\Http\Request;

class ManajemenLogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();

        $daftarLogDokterMagang = collect();
        $daftarLogMahasiswaMagang = collect();

        if ($user->role === 'admin') {
            $daftarLogDokterMagang = LogDokterMagang::with(['unitKerja', 'departemen', 'dokterMagang'])->get();
            $daftarLogMahasiswaMagang = LogMahasiswaMagang::with(['unitKerja', 'departemen', 'mahasiswaMagang'])->get();
        } elseif ($user->role === 'dokter_magang') {
            $daftarLogDokterMagang = LogDokterMagang::with(['unitKerja', 'departemen', 'dokterMagang'])->where('dokter_magang_id', $user->id)->get();
        } elseif ($user->role === 'mahasiswa_magang') {
            $daftarLogMahasiswaMagang = LogMahasiswaMagang::with(['unitKerja', 'departemen', 'mahasiswaMagang'])->where('mahasiswa_magang_id', $user->id)->get();
        } elseif ($user->role === 'pembimbing_lapangan') {
            $daftarLogDokterMagang = $user->unit_kerja_id
                ? LogDokterMagang::with(['unitKerja', 'departemen', 'dokterMagang'])->where('unit_kerja_id', $user->unit_kerja_id)->get()
                : collect();
            $daftarLogMahasiswaMagang = $user->unit_kerja_id
                ? LogMahasiswaMagang::with(['unitKerja', 'departemen', 'mahasiswaMagang'])
                    ->where('unit_kerja_id', $user->unit_kerja_id)
                    ->get()
                : collect();
        }

        return view('manajemen-logbook', compact('daftarLogDokterMagang', 'daftarLogMahasiswaMagang', 'user'));
    }

    public function kategoriPelayananKesehatan()
    {
        return view('kategori-pelayanan-kesehatan', [
            'daftarKategoriPelayananKesehatan' => KategoriPelayananKesehatan::all(),
        ]);
    }

    public function storeKategori(Request $request)
    {
        $validated = $request->validate(
            ['nama' => ['required', 'string', 'max:255', 'unique:kategori_pelayanan_kesehatans,nama']],
            ['nama.unique' => 'Kategori pelayanan kesehatan sudah terdaftar']
        );

        KategoriPelayananKesehatan::create($validated);

        return back()->with('status', 'Kategori pelayanan kesehatan berhasil ditambah');
    }

    public function destroyKategori(KategoriPelayananKesehatan $kategoriPelayananKesehatan)
    {
        $kategoriPelayananKesehatan->delete();

        return back()->with('status', 'Kategori pelayanan kesehatan berhasil dihapus');
    }
}
