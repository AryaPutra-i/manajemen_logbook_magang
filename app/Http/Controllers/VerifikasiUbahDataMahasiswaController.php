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
        $daftarPerubahanData = match ($user->role) {
            'pembimbing_lapangan_puskesmas' => $user->puskesmas_id
                ? User::query()
                    ->with('puskesmas')
                    ->where('role', 'mahasiswa_magang')
                    ->where('puskesmas_id', $user->puskesmas_id)
                    ->where('is_profile_change_pending', true)
                    ->get()
                : collect(),
            'pembimbing_lapangan_departemen' => $user->departemen_id
                ? User::query()
                    ->with('departemen')
                    ->where('role', 'mahasiswa_magang')
                    ->where('departemen_id', $user->departemen_id)
                    ->where('is_profile_change_pending', true)
                    ->get()
                : collect(),
            default => collect(),
        };

        return view('manajemen-ubah-data-mahasiswa', compact('daftarPerubahanData'));
    }

    private function scopedMahasiswaMagang(User $mahasiswaMagang): void
    {
        $user = request()->user();

        $authorized = match ($user->role) {
            'pembimbing_lapangan_puskesmas' => $user->puskesmas_id
                && $mahasiswaMagang->puskesmas_id === $user->puskesmas_id,
            'pembimbing_lapangan_departemen' => $user->departemen_id
                && $mahasiswaMagang->departemen_id === $user->departemen_id,
            default => false,
        };

        if (! $authorized
        || $mahasiswaMagang->role !== 'mahasiswa_magang'
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

        if ($mahasiswaMagang->nama !== $dataBaru->nama) {
            $mahasiswaMagang->username = User::generateUniqueUsername($dataBaru->nama, $mahasiswaMagang->id);
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
