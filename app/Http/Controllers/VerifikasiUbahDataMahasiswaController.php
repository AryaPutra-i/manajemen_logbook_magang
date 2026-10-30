<?php

namespace App\Http\Controllers;

use App\Models\RiwayatProfilMahasiswaMagang;
use App\Models\User;

class VerifikasiUbahDataMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $daftarPerubahanData = RiwayatProfilMahasiswaMagang::query()
            ->with('mahasiswaMagang')
            ->whereHas('mahasiswaMagang', function ($q) use ($user) {
                $q->where(['departemen_id' => $user->departemen_id,
                'is_profile_change_pending' => true]);
            })
            ->get();
        $daftarMahasiswaMagang = User::where('departemen_id', $user->departemen_id)->get();

        return view('manajemen-ubah-data-mahasiswa', compact('daftarPerubahanData', 'daftarMahasiswaMagang'));
    }

    private function scopedMahasiswaMagang(User $mahasiswaMagang): void
    {
        $user = request()->user();

        if ($user->role !== 'pembimbing_lapangan_mahasiswa'
        || ! $user->departemen_id
        || $mahasiswaMagang->role !== 'mahasiswa_magang'
        || $mahasiswaMagang->departemen_id !== $user->departemen_id
        || ! $mahasiswaMagang->is_profile_change_pending) {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $mahasiswaMagang)
    {
        $this->scopedMahasiswaMagang($mahasiswaMagang);
        $dataBaru = RiwayatProfilMahasiswaMagang::where('mahasiswa_magang_id', $mahasiswaMagang->id)->latest()->first();

        return view('verifikasi-ubah-data-mahasiswa', compact('mahasiswaMagang', 'dataBaru'));
    }

    public function verify(User $mahasiswaMagang)
    {
        $this->scopedMahasiswaMagang($mahasiswaMagang);
        $dataBaru = RiwayatProfilMahasiswaMagang::where('mahasiswa_magang_id', $mahasiswaMagang->id)->latest()->first();

        if (! $dataBaru) {
            return redirect()->route('ubah-data-mahasiswa.queue')->withErrors(['status' => 'Data perubahan tidak ditemukan']);
        }

        $mahasiswaMagang->nama = $dataBaru->nama;
        $mahasiswaMagang->nik = $dataBaru->nik;
        $mahasiswaMagang->nim = $dataBaru->nim;
        $mahasiswaMagang->alamat = $dataBaru->alamat;
        $mahasiswaMagang->universitas = $dataBaru->universitas;
        $mahasiswaMagang->is_profile_change_pending = false;

        $mahasiswaMagang->save();

        RiwayatProfilMahasiswaMagang::where('mahasiswa_magang_id', $mahasiswaMagang->id)->delete();

        return redirect()->route('ubah-data-mahasiswa.queue')->with('status', 'Ubah data user dengan username '.$mahasiswaMagang->username.' berhasil diverifikasi');
    }

    public function reject(User $mahasiswaMagang)
    {
        $this->scopedMahasiswaMagang($mahasiswaMagang);
        $mahasiswaMagang->is_profile_change_pending = false;
        $mahasiswaMagang->save();

        RiwayatProfilMahasiswaMagang::where('mahasiswa_magang_id', $mahasiswaMagang->id)->delete();

        return redirect()->route('ubah-data-mahasiswa.queue')->with('status', 'Ubah data user dengan username '.$mahasiswaMagang->username.' berhasil ditolak');
    }
}
