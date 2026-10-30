<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogMahasiswaMagangRequest;
use App\Models\LogMahasiswaMagang;
use App\Models\User;
use App\StatusLogMagang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogMahasiswaController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function createMahasiswa()
    {
        return view('tambah-log-mahasiswa');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeMahasiswa(LogMahasiswaMagangRequest $request)
    {
        $validated = $request->validated();

        $filePath = $request->file('dokumentasi')->store('uploads', 'public');

        LogMahasiswaMagang::create([
            'nama_kegiatan' => $validated['namaKegiatan'],
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'departemen_id' => $request->user()->departemen_id,
            'mahasiswa_magang_id' => $request->user()->id,
            'dokumentasi' => $filePath,
        ]);

        return redirect()->route('logbook.index')->with('status', 'Log berhasil ditambah');
    }

    /**
     * Show the form for editing the specified
     */
    public function editMahasiswa(LogMahasiswaMagang $logMagang)
    {
        $user = request()->user();

        if ($user->role === 'mahasiswa_magang' && $logMagang->mahasiswa_magang_id !== $user->id) {
            abort(403);
        }

        if ($user->role === 'pembimbing_lapangan_mahasiswa' && $logMagang->departemen_id !== $user->departemen_id) {
            abort(403);
        }

        return view('edit-log-mahasiswa', ['logMahasiswa' => $logMagang, 'user' => $user]);
    }

    public function verifikasiLogMahasiswa(LogMahasiswaMagang $logMagang)
    {
        if ($logMagang->departemen_id !== request()->user()->departemen_id) {
            abort(403);
        }

        $logMagang->status_verifikasi = StatusLogMagang::Selesai;
        $logMagang->save();

        return back()->with('status', 'Log dari '.$logMagang->mahasiswaMagang->username.' pada tanggal '.$logMagang->tanggal.' diverifikasi');
    }

    public function mintaRevisiLogMahasiswa(LogMahasiswaMagang $logMagang)
    {
        if ($logMagang->departemen_id !== request()->user()->departemen_id) {
            abort(403);
        }

        $logMagang->status_verifikasi = StatusLogMagang::Revisi;
        $logMagang->save();

        return back()->with('status', 'Log dari '.$logMagang->mahasiswaMagang->username.' pada tanggal '.$logMagang->tanggal.' dimintai revisi');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateMahasiswa(LogMahasiswaMagangRequest $request, string $id)
    {
        $logMagang = LogMahasiswaMagang::findOrFail($id);

        if ($logMagang->mahasiswa_magang_id !== $request->user()->id) {
            abort(403);
        }

        if ($logMagang->status_verifikasi === 'selesai') {
            return back()->withErrors(['status' => 'Log yang sudah selesai tidak dapat diubah lagi']);
        }

        $validated = $request->validated();

        $dokumentasi = $logMagang->dokumentasi;

        if ($request->hasFile('dokumentasi')) {
            if ($logMagang->dokumentasi && Storage::disk('public')->exists($logMagang->dokumentasi)) {
                Storage::disk('public')->delete($logMagang->dokumentasi);
            }

            $dokumentasi = $request->file('dokumentasi')->store('uploads', 'public');
        }

        $logMagang->fill([
            'nama_kegiatan' => $validated['namaKegiatan'],
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'dokumentasi' => $dokumentasi,
            'status_verifikasi' => StatusLogMagang::Pending,
        ]);

        $logMagang->save();

        return redirect()->route('logbook.index')
            ->with('status', "Log dengan tanggal {$logMagang->tanggal} berhasil diedit");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyMahasiswa(string $id)
    {
        $logMagang = LogMahasiswaMagang::findOrFail($id);
        $user = request()->user();

        if ($user->role !== 'admin' && $logMagang->mahasiswa_magang_id !== $user->id) {
            return back()->withErrors(['status' => 'Anda tidak dapat menghapus log milik orang lain']);
        }

        if ($logMagang->status_verifikasi === 'selesai') {
            return back()->withErrors(['status' => 'Log yang sudah selesai tidak dapat dihapus lagi']);
        }

        if ($logMagang->dokumentasi && Storage::disk('public')->exists($logMagang->dokumentasi)) {
            Storage::disk('public')->delete($logMagang->dokumentasi);
        }

        $tanggal = $logMagang->tanggal;
        $logMagang->delete();

        return redirect()->route('logbook.index')->with('status', "Log dengan tanggal $tanggal berhasil dihapus");
    }

    public function generateRekapanMahasiswa(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
            'tanggalLogMulai' => ['required', 'date_format:Y-m-d'],
            'tanggalLogAkhir' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggalLogMulai'],
        ]);

        $user = User::where('username', $validated['username'])->firstOrFail();

        abort_unless($user->role === 'mahasiswa_magang', 404);

        $tanggalLogMulai = Carbon::createFromFormat('Y-m-d', $validated['tanggalLogMulai'])->startOfDay();

        $tanggalLogAkhir = Carbon::createFromFormat('Y-m-d', $validated['tanggalLogAkhir'])->endOfDay();

        $daftarLog = LogMahasiswaMagang::where('mahasiswa_magang_id', $user->id)
            ->where('status_verifikasi', 'selesai')
            ->whereBetween('tanggal', [$tanggalLogMulai, $tanggalLogAkhir])
            ->get();

        return view('generate-rekapan-mahasiswa', [
            'user' => $user,
            'daftarLog' => $daftarLog,
            'tanggalLogMulai' => $tanggalLogMulai,
            'tanggalLogAkhir' => $tanggalLogAkhir,
        ]);
    }
}
