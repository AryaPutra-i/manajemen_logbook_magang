<?php

namespace App\Http\Controllers;

use App\Models\RiwayatProfilDokterMagang;
use App\Models\User;

class VerifikasiUbahDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        $daftarPerubahanData = RiwayatProfilDokterMagang::query()
            ->with('dokterMagang')
            ->whereHas('dokterMagang', function ($q) use ($user) {
                $q->where(['puskesmas_id' => $user->puskesmas_id, 'is_profile_change_pending' => true]);
            })
            ->get();
        $daftarDokterMagang = User::where('puskesmas_id', $user->puskesmas_id)->get();

        return view('manajemen-ubah-data', compact('daftarPerubahanData', 'daftarDokterMagang'));
    }

    private function scopedDokterMagang(User $dokterMagang): void
    {
        $user = request()->user();

        if ($user->role !== 'pembimbing_lapangan'
            || ! $user->puskesmas_id
            || $dokterMagang->role !== 'dokter_magang'
            || $dokterMagang->puskesmas_id !== $user->puskesmas_id
            || ! $dokterMagang->is_profile_change_pending) {
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $dokterMagang)
    {
        $this->scopedDokterMagang($dokterMagang);
        $dataBaru = RiwayatProfilDokterMagang::where('dokter_magang_id', $dokterMagang->id)->latest()->first();

        return view('verifikasi-ubah-data', compact('dokterMagang', 'dataBaru'));
    }

    public function verify(User $dokterMagang)
    {
        $this->scopedDokterMagang($dokterMagang);
        $dataBaru = RiwayatProfilDokterMagang::where('dokter_magang_id', $dokterMagang->id)->latest()->first();

        if (! $dataBaru) {
            return redirect()->route('ubah-data.queue')->withErrors(['status' => 'Data perubahan tidak ditemukan']);
        }

        $dokterMagang->nama = $dataBaru->nama;
        $dokterMagang->nik = $dataBaru->nik;
        $dokterMagang->nim = $dataBaru->nim;
        $dokterMagang->alamat = $dataBaru->alamat;
        $dokterMagang->universitas = $dataBaru->universitas;
        $dokterMagang->is_profile_change_pending = false;

        $dokterMagang->save();

        RiwayatProfilDokterMagang::where('dokter_magang_id', $dokterMagang->id)->delete();

        return redirect()->route('ubah-data.queue')->with('status', 'Ubah data user dengan username '.$dokterMagang->username.' berhasil diverifikasi');

    }

    public function reject(User $dokterMagang)
    {
        $this->scopedDokterMagang($dokterMagang);
        $dokterMagang->is_profile_change_pending = false;
        $dokterMagang->save();

        RiwayatProfilDokterMagang::where('dokter_magang_id', $dokterMagang->id)->delete();

        return redirect()->route('ubah-data.queue')->with('status', 'Ubah data user dengan username '.$dokterMagang->username.' berhasil ditolak');
    }
}
