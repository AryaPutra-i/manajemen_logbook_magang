<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogDokterMagangRequest;
use App\Models\KategoriPelayananKesehatan;
use App\Models\LogDokterMagang;
use App\Models\User;
use App\StatusLogMagang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogDokterController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $daftarKategoriPelayananKesehatan = KategoriPelayananKesehatan::all();

        return view('tambah-log', ['daftarKategoriPelayananKesehatan' => $daftarKategoriPelayananKesehatan]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LogDokterMagangRequest $request)
    {
        $validated = $request->validated();
        $jsonData = [];
        foreach ($validated['kategoriPelayananKesehatan'] as $index => $kategori) {
            $jsonData[$kategori] = $validated['jumlahPasien'][$index];
        }

        $filePath = $request->file('dokumentasi')->store('uploads', 'public');

        LogDokterMagang::create([
            'jumlah_pasien_per_kategori_pelayanan_kesehatan' => $jsonData,
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'hambatan' => $validated['hambatan'],
            'puskesmas_id' => $request->user()->puskesmas_id,
            'dokter_magang_id' => $request->user()->id,
            'dokumentasi' => $filePath,
        ]);

        return redirect()->route('logbook.index')->with('status', 'Log berhasil ditambah');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LogDokterMagang $logMagang)
    {
        $user = request()->user();

        if ($user->role === 'dokter_magang' && $logMagang->dokter_magang_id !== $user->id) {
            abort(403);
        }

        if ($user->role === 'pembimbing_lapangan' && $logMagang->puskesmas_id !== $user->puskesmas_id) {
            abort(403);
        }

        $daftarKategoriPelayananKesehatan = KategoriPelayananKesehatan::all();
        $daftarKategoriPelayananKesehatanTersimpan = collect($logMagang->jumlah_pasien_per_kategori_pelayanan_kesehatan)->keys()->all();
        $daftarJumlahPasienTersimpan = collect($logMagang->jumlah_pasien_per_kategori_pelayanan_kesehatan)->values()->all();

        return view('edit-log', compact('logMagang', 'daftarKategoriPelayananKesehatan', 'daftarKategoriPelayananKesehatanTersimpan', 'daftarJumlahPasienTersimpan', 'user'));
    }

    public function verifikasiLog(LogDokterMagang $logMagang)
    {
        if ($logMagang->puskesmas_id !== request()->user()->puskesmas_id) {
            abort(403);
        }

        $logMagang->status_verifikasi = StatusLogMagang::Selesai;
        $logMagang->save();

        return back()->with('status', 'Log dari '.$logMagang->dokterMagang->username.' pada tanggal '.$logMagang->tanggal.' diverifikasi');
    }

    public function mintaRevisiLog(LogDokterMagang $logMagang)
    {
        if ($logMagang->puskesmas_id !== request()->user()->puskesmas_id) {
            abort(403);
        }

        $logMagang->status_verifikasi = StatusLogMagang::Revisi;
        $logMagang->save();

        return back()->with('status', 'Log dari '.$logMagang->dokterMagang->username.' pada tanggal '.$logMagang->tanggal.' dimintai revisi');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LogDokterMagangRequest $request, string $id)
    {
        $logMagang = LogDokterMagang::findOrFail($id);

        if ($logMagang->dokter_magang_id !== $request->user()->id) {
            abort(403);
        }

        if ($logMagang->status_verifikasi === 'selesai') {
            return back()->withErrors(['status' => 'Log yang sudah selesai tidak dapat diubah lagi']);
        }

        $validated = $request->validated();

        $jsonData = [];
        foreach ($validated['kategoriPelayananKesehatan'] as $index => $kategori) {
            $jsonData[$kategori] = $validated['jumlahPasien'][$index];
        }

        $dokumentasi = $logMagang->dokumentasi;

        if ($request->hasFile('dokumentasi')) {
            if ($logMagang->dokumentasi && Storage::disk('public')->exists($logMagang->dokumentasi)) {
                Storage::disk('public')->delete($logMagang->dokumentasi);
            }

            $dokumentasi = $request->file('dokumentasi')->store('uploads', 'public');
        }

        $logMagang->fill([
            'jumlah_pasien_per_kategori_pelayanan_kesehatan' => $jsonData,
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'hambatan' => $validated['hambatan'],
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
    public function destroy(string $id)
    {
        $logMagang = LogDokterMagang::findOrFail($id);
        $user = request()->user();

        abort_unless($user->role === 'dokter_magang', 404);

        if ($user->role !== 'admin' && $logMagang->dokter_magang_id !== $user->id) {
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

    public function generateRekapan(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
            'tanggalLogMulai' => ['required', 'date_format:Y-m-d'],
            'tanggalLogAkhir' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggalLogMulai'],
        ]);

        $user = User::where('username', $validated['username'])->firstOrFail();

        $tanggalLogMulai = Carbon::createFromFormat('Y-m-d', $validated['tanggalLogMulai'])->startOfDay();
        $tanggalLogAkhir = Carbon::createFromFormat('Y-m-d', $validated['tanggalLogAkhir'])->endOfDay();

        if ($tanggalLogMulai->diffInDays($tanggalLogAkhir) > 366) {
            return redirect()->route('logbook.index')->withErrors(['status' => 'Rentang tanggal maksimal 366 hari']);
        }

        $daftarLogMagang = LogDokterMagang::where('dokter_magang_id', $user->id)
            ->where('status_verifikasi', 'selesai')
            ->whereBetween('tanggal', [$tanggalLogMulai, $tanggalLogAkhir])
            ->get();

        $daftarTanggal = [];
        for ($t = $tanggalLogMulai->copy(); $t->lte($tanggalLogAkhir); $t->addDay()) {
            $daftarTanggal[] = $t->copy();
        }

        $rekap = [];

        foreach ($daftarLogMagang as $log) {
            foreach ($log->jumlah_pasien_per_kategori_pelayanan_kesehatan as $kategori => $jumlah) {
                $rekap[$kategori][$log->tanggal->toDateString()] =
                    ($rekap[$kategori][$log->tanggal->toDateString()] ?? 0) + (int) $jumlah;
            }
        }

        $totalPerTanggal = collect($daftarTanggal)
            ->mapWithKeys(fn ($t) => [$t->toDateString() => collect($rekap)->sum(fn ($row) => $row[$t->toDateString()] ?? 0)])
            ->all();

        $grupBulan = [];
        $cursor = $tanggalLogMulai->copy()->startOfMonth();
        $akhirBulan = $tanggalLogAkhir->copy()->endOfMonth();

        while ($cursor->lte($akhirBulan)) {
            $days = [];
            for ($d = 1; $d <= $cursor->daysInMonth; $d++) {
                $days[] = $cursor->copy()->day($d)->startOfDay();
            }

            $grupBulan[] = [
                'key' => $cursor->format('Y-m'),
                'label' => mb_strtoupper($cursor->copy()->locale('id')->isoFormat('MMMM YYYY')),
                'days' => $days,
            ];

            $cursor->addMonth()->startOfMonth();
        }

        return view('generate-rekapan', compact('user', 'daftarTanggal', 'rekap', 'totalPerTanggal', 'tanggalLogMulai', 'tanggalLogAkhir', 'grupBulan'));
    }
}
