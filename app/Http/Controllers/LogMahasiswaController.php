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
        $user = request()->user();

        if ($user->unit_kerja_id === null) {
            abort(422, 'Akun mahasiswa harus memiliki unit kerja.');
        }

        $validated = $request->validated();

        $filePath = $request->file('dokumentasi')->store('uploads', 'public');

        LogMahasiswaMagang::create([
            'nama_kegiatan' => $validated['namaKegiatan'],
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'departemen_id' => $user->departemen_id,
            'unit_kerja_id' => $user->unit_kerja_id,
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

        if ($user->role === 'mahasiswa_magang') {
            abort_unless($logMagang->mahasiswa_magang_id === $user->id, 403);
        } elseif ($user->role === 'pembimbing_lapangan') {
            abort_unless(
                $this->isAccessibleByPembimbing($logMagang, $user),
                403
            );
        } elseif ($user->role === 'admin_unit_kerja') {
            abort_unless(
                $user->unit_kerja_id !== null && $logMagang->unit_kerja_id === $user->unit_kerja_id,
                403
            );
        }

        return view('edit-log-mahasiswa', ['logMahasiswa' => $logMagang, 'user' => $user]);
    }

    private function authorizePembimbing(LogMahasiswaMagang $logMagang, User $user): void
    {
        abort_unless($this->isAccessibleByPembimbing($logMagang, $user), 403);
    }

    private function isAccessibleByPembimbing(LogMahasiswaMagang $logMagang, User $user): bool
    {
        return $user->role === 'pembimbing_lapangan'
            && $user->unit_kerja_id !== null
            && $logMagang->unit_kerja_id === $user->unit_kerja_id
            && ($user->departemen_id === null || $logMagang->departemen_id === $user->departemen_id);
    }

    public function verifikasiLogMahasiswa(LogMahasiswaMagang $logMagang)
    {
        $user = request()->user();

        $this->authorizePembimbing($logMagang, $user);

        $logMagang->status_verifikasi = StatusLogMagang::Selesai;
        $logMagang->pembimbing_lapangan_id = $user->id;
        $logMagang->save();

        return back()->with('status', 'Log dari '.$logMagang->mahasiswaMagang->username.' pada tanggal '.$logMagang->tanggal.' diverifikasi');
    }

    public function mintaRevisiLogMahasiswa(LogMahasiswaMagang $logMagang)
    {
        $user = request()->user();

        $this->authorizePembimbing($logMagang, $user);

        $logMagang->status_verifikasi = StatusLogMagang::Revisi;
        $logMagang->pembimbing_lapangan_id = $user->id;
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

        if ($user->role === 'admin_unit_kerja') {
            if ($logMagang->unit_kerja_id !== $user->unit_kerja_id) {
                abort(403, 'Anda tidak memiliki hak akses untuk menghapus log ini');
            }
        } elseif ($user->role !== 'admin' && $logMagang->mahasiswa_magang_id !== $user->id) {
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
            'username' => ['nullable', 'string', 'exists:users,username'],
            'unitKerja' => ['nullable', 'string'],
            'tanggalLogMulai' => ['required', 'date_format:Y-m-d'],
            'tanggalLogAkhir' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggalLogMulai'],
        ]);

        $user = null;
        if (!empty($validated['username'])) {
            $user = User::where('username', $validated['username'])->firstOrFail();
            abort_unless($user->role === 'mahasiswa_magang', 404);

            if (request()->user()->role === 'admin_unit_kerja') {
                if ($user->unit_kerja_id !== request()->user()->unit_kerja_id) {
                    abort(403, 'Anda hanya dapat mengunduh rekapan logbook di unit kerja Anda');
                }
            }
        } elseif (request()->user()->role === 'admin_unit_kerja') {
            $user = request()->user();
        }

        $tanggalLogMulai = Carbon::createFromFormat('Y-m-d', $validated['tanggalLogMulai'])->startOfDay();
        $tanggalLogAkhir = Carbon::createFromFormat('Y-m-d', $validated['tanggalLogAkhir'])->endOfDay();

        $logQuery = LogMahasiswaMagang::with(['mahasiswaMagang', 'unitKerja', 'departemen'])
            ->where('status_verifikasi', 'selesai')
            ->whereBetween('tanggal', [$tanggalLogMulai, $tanggalLogAkhir]);

        if ($user && $user->role === 'mahasiswa_magang') {
            $logQuery->where('mahasiswa_magang_id', $user->id);
        } elseif (request()->user()->role === 'admin_unit_kerja') {
            $logQuery->where('unit_kerja_id', request()->user()->unit_kerja_id);
        } elseif (!empty($validated['unitKerja'])) {
            $unitKerjaNama = $validated['unitKerja'];
            $logQuery->whereHas('unitKerja', function ($q) use ($unitKerjaNama) {
                $q->where('nama', $unitKerjaNama);
            });
        }

        $daftarLog = $logQuery->orderBy('tanggal')->get();

        $rekap = [];
        foreach ($daftarLog as $log) {
            $mahasiswa = $log->mahasiswaMagang;
            $key = $mahasiswa?->nama ?: ($mahasiswa?->username ?? ($user?->nama ?: 'Mahasiswa'));
            $tgl = $log->tanggal->toDateString();
            $rekap[$key][$tgl] = ($rekap[$key][$tgl] ?? 0) + 1;
        }

        $totalPerTanggal = [];
        foreach ($daftarLog as $log) {
            $tgl = $log->tanggal->toDateString();
            $totalPerTanggal[$tgl] = ($totalPerTanggal[$tgl] ?? 0) + 1;
        }

        $grupBulan = [];
        $cursor = $tanggalLogMulai->copy()->startOfMonth();
        $akhirBulan = $tanggalLogAkhir->copy()->endOfMonth();

        while ($cursor->lte($akhirBulan)) {
            $days = [];
            for ($d = 1; $d <= $cursor->daysInMonth; $d++) {
                $days[] = $cursor->copy()->day($d)->startOfDay();
            }

            $grupBulan[] = [
                'key'   => $cursor->format('Y-m'),
                'label' => mb_strtoupper($cursor->copy()->locale('id')->isoFormat('MMMM YYYY')),
                'days'  => $days,
            ];

            $cursor->addMonth()->startOfMonth();
        }

        return view('generate-rekapan-mahasiswa', [
            'user'            => $user,
            'daftarLog'       => $daftarLog,
            'rekap'           => $rekap,
            'totalPerTanggal' => $totalPerTanggal,
            'grupBulan'       => $grupBulan,
            'tanggalLogMulai' => $tanggalLogMulai,
            'tanggalLogAkhir' => $tanggalLogAkhir,
        ]);
    }
}
