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
                $q->where([
                    'unit_kerja_id' => $user->unit_kerja_id,
                    'departemen_id' => $user->departemen_id,
                    'is_profile_change_pending' => true,
                ]);
            })
            ->get();
        $daftarDokterMagang = User::where('unit_kerja_id', $user->unit_kerja_id)
            ->where('departemen_id', $user->departemen_id)
            ->get();

        return view('manajemen-ubah-data', compact('daftarPerubahanData', 'daftarDokterMagang'));
    }

    private function scopedDokterMagang(User $dokterMagang): void
    {
        $user = request()->user();

        if ($user->role !== 'pembimbing_lapangan'
            || ! $user->unit_kerja_id
            || ! $user->departemen_id
            || $dokterMagang->role !== 'dokter_magang'
            || $dokterMagang->unit_kerja_id !== $user->unit_kerja_id
            || $dokterMagang->departemen_id !== $user->departemen_id
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

        if ($dokterMagang->nama !== $dataBaru->nama) {
            $dokterMagang->username = User::generateUniqueUsername($dataBaru->nama, $dokterMagang->id);
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
