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
            $daftarLogDokterMagang = LogDokterMagang::with(['puskesmas', 'dokterMagang'])->get();
            $daftarLogMahasiswaMagang = LogMahasiswaMagang::with(['departemen', 'mahasiswaMagang'])->get();
        } elseif ($user->role === 'dokter_magang') {
            $daftarLogDokterMagang = LogDokterMagang::with(['puskesmas', 'dokterMagang'])->where('dokter_magang_id', $user->id)->get();
        } elseif ($user->role === 'mahasiswa_magang') {
            $daftarLogMahasiswaMagang = LogMahasiswaMagang::with(['departemen', 'mahasiswaMagang'])->where('mahasiswa_magang_id', $user->id)->get();
        } elseif ($user->role === 'pembimbing_lapangan') {
            $daftarLogDokterMagang = $user->puskesmas_id
                ? LogDokterMagang::with(['puskesmas', 'dokterMagang'])->where('puskesmas_id', $user->puskesmas_id)->get()
                : collect();
        } elseif ($user->role === 'pembimbing_lapangan_mahasiswa') {
            $daftarLogMahasiswaMagang = $user->departemen_id
                ? LogMahasiswaMagang::with(['departemen', 'mahasiswaMagang'])->where('departemen_id', $user->departemen_id)->get()
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
