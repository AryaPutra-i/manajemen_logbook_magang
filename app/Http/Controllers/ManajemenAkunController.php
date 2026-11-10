<?php

namespace App\Http\Controllers;

use App\Http\Requests\TambahAkunRequest;
use App\Http\Requests\UbahDataRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\UsersImport;
use App\Models\Departemen;
use App\Models\RiwayatProfilDokterMagang;
use App\Models\RiwayatProfilMahasiswaMagang;
use App\Models\UnitKerja;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ManajemenAkunController extends Controller
{
    public function menu()
    {
        $user = request()->user();

        if ($user->role === 'admin') {
            return view('manajemen-akun', ['users' => User::all()]);
        } else {
            return view('manajemen-akun-menu', compact('user'));
        }
    }

    public function create()
    {
        $daftarUnitKerja = UnitKerja::with('departemens')->get(['id', 'nama', 'jenis', 'created_at']);
        $daftarUnitKerja->each(function (UnitKerja $unitKerja): void {
            if ($unitKerja->jenis === 'Dinas_Kesehatan' && $unitKerja->created_at === null) {
                $unitKerja->jenis = 'Puskesmas';
            }
        });
        $dinkesId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
        $daftarDepartemenDinasKesehatan = Departemen::where(function ($query) use ($dinkesId) {
            $query->where('unit_kerja_id', $dinkesId)->orWhereNull('unit_kerja_id');
        })
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return view('tambah-akun', compact('daftarUnitKerja', 'daftarDepartemenDinasKesehatan'));
    }

    public function editData()
    {
        $user = request()->user();
        $dataBaru = match ($user->role) {
            'dokter_magang' => RiwayatProfilDokterMagang::where('dokter_magang_id', $user->id)->latest()->first(),
            'mahasiswa_magang' => RiwayatProfilMahasiswaMagang::where('mahasiswa_magang_id', $user->id)->latest()->first(),
            default => null,
        };

        return view('ubah-data', compact('user', 'dataBaru'));
    }

    public function updateData(UbahDataRequest $request, User $user)
    {
        if ($user->id !== request()->user()->id) {
            abort(403);
        }

        $status = 'Data akun Anda berhasil di-update';

        if (in_array($user->role, ['dokter_magang', 'mahasiswa_magang'], true)) {
            $riwayatPerubahan = $user->role === 'dokter_magang'
                ? RiwayatProfilDokterMagang::where('dokter_magang_id', $user->id)
                : RiwayatProfilMahasiswaMagang::where('mahasiswa_magang_id', $user->id);

            if ($user->is_profile_change_pending && $riwayatPerubahan->exists()) {
                return back()->withErrors(['status' => 'Ubah data masih dalam status pending']);
            }

            $isResubmitting = $user->is_profile_change_pending;
            $status .= ', silahkan konfirmasi ke pembimbing lapangan';

            DB::transaction(function () use ($request, $user): void {
                if ($user->role === 'dokter_magang') {
                    RiwayatProfilDokterMagang::create([...$request->validated(), 'dokter_magang_id' => $user->id]);
                } else {
                    RiwayatProfilMahasiswaMagang::create([...$request->validated(), 'mahasiswa_magang_id' => $user->id]);
                }

                $user->is_profile_change_pending = true;
                $user->save();
            });

            if ($isResubmitting) {
                $status = 'Pengajuan ubah data berhasil dikirim ulang, silahkan konfirmasi ke pembimbing lapangan';
            }
        } else {
            $validated = $request->validated();

            if ($user->nama !== $validated['nama']) {
                $validated['username'] = User::generateUniqueUsername($validated['nama'], $user->id);
            }

            $user->update($validated);
        }

        return back()->with('status', $status);
    }

    public function tambahAkun(TambahAkunRequest $request)
    {
        $validated = $request->validated();

        if ($validated['role'] === 'admin') {
            return back()->withErrors(['status' => 'Menambah akun dengan role admin tidak diperbolehkan']);
        }

        $username = User::generateUniqueUsername($validated['nama']);
        $jenisUnitKerja = $validated['jenisUnitKerja'] ?? null;
        $unitKerjaId = null;

        if (in_array($validated['role'], ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true)) {
            if ($jenisUnitKerja === 'Dinas_Kesehatan') {
                $unitKerjaId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
            } elseif ($jenisUnitKerja === 'Puskesmas') {
                $unitKerjaId = UnitKerja::where('nama', $validated['namaUnitKerja'] ?? null)
                    ->where('jenis', 'Puskesmas')
                    ->value('id');
            }
        }

        $departemenId = null;
        if ($validated['role'] !== 'dokter_magang' && ! blank($validated['namaDepartemen'] ?? null)) {
            $departemenId = Departemen::where('nama', $validated['namaDepartemen'])
                ->where(function ($query) use ($unitKerjaId) {
                    $query->where('unit_kerja_id', $unitKerjaId)->orWhereNull('unit_kerja_id');
                })
                ->value('id');
        }

        unset($validated['jenisUnitKerja'], $validated['namaUnitKerja'], $validated['namaDepartemen']);
        User::create([
            ...$validated,
            'username' => $username,
            'password' => Hash::make('password123'),
            'unit_kerja_id' => $unitKerjaId,
            'departemen_id' => $departemenId,
        ]);

        return redirect()->route('akun.menu')->with('status', 'Akun berhasil ditambah');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $role = $validated['role'] ?? $user->role;

        $namaUnitKerja = array_key_exists('namaUnitKerja', $validated)
            ? $validated['namaUnitKerja']
            : $user->unitKerja?->nama;

        $namaDepartemen = $role === 'dokter_magang'
            ? null
            : (array_key_exists('namaDepartemen', $validated)
                ? $validated['namaDepartemen']
                : $user->departemen?->nama);
        $jenisUnitKerja = array_key_exists('jenisUnitKerja', $validated)
            ? $validated['jenisUnitKerja']
            : ($user->unitKerja?->jenis ?? ($user->departemen_id !== null && $user->departemen?->unit_kerja_id === null ? 'Dinas_Kesehatan' : null));

        $unitKerjaId = null;
        if (in_array($role, ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true)) {
            if ($jenisUnitKerja === 'Dinas_Kesehatan') {
                $unitKerjaId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
            } elseif ($jenisUnitKerja === 'Puskesmas') {
                $unitKerjaId = UnitKerja::where('nama', $namaUnitKerja)
                    ->where('jenis', 'Puskesmas')
                    ->value('id');
            }
        }

        $departemenId = null;
        if ($role !== 'dokter_magang' && ! blank($namaDepartemen)) {
            $departemenId = Departemen::where('nama', $namaDepartemen)
                ->where(function ($query) use ($unitKerjaId) {
                    $query->where('unit_kerja_id', $unitKerjaId)->orWhereNull('unit_kerja_id');
                })
                ->value('id');
        }

        unset($validated['jenisUnitKerja'], $validated['namaUnitKerja'], $validated['namaDepartemen']);
        $updatedData = [...$validated, 'unit_kerja_id' => $unitKerjaId, 'departemen_id' => $departemenId];

        if ($user->nama !== $validated['nama']) {
            $updatedData['username'] = User::generateUniqueUsername($validated['nama'], $user->id);
        }

        $user->update($updatedData);

        return redirect()->route('akun.menu')->with('status', "Akun dengan username $user->username berhasil di-update");
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin' || $user->id === request()->user()->id) {
            return back()->withErrors(['status' => 'User dengan role admin tidak dapat dihapus']);
        }

        $username = $user->username;
        $user->delete();

        return redirect()->route('akun.menu')->with('status', "Akun dengan username $username berhasil dihapus");
    }

    public function edit(User $user)
    {
        $daftarUnitKerja = UnitKerja::with('departemens')->get(['id', 'nama', 'jenis', 'created_at']);
        $daftarUnitKerja->each(function (UnitKerja $unitKerja): void {
            if ($unitKerja->jenis === 'Dinas_Kesehatan' && $unitKerja->created_at === null) {
                $unitKerja->jenis = 'Puskesmas';
            }
        });
        $user->setRelation('unitKerja', $daftarUnitKerja->firstWhere('id', $user->unit_kerja_id));
        $dinkesId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
        $daftarDepartemenDinasKesehatan = Departemen::where(function ($query) use ($dinkesId) {
            $query->where('unit_kerja_id', $dinkesId)->orWhereNull('unit_kerja_id');
        })
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return view('edit-akun', compact('user', 'daftarUnitKerja', 'daftarDepartemenDinasKesehatan'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'accounts_xlsx' => ['required', 'file', 'mimes:xlsx', 'max:2048'],
        ]);

        try {
            Excel::import(new UsersImport, $request->file('accounts_xlsx'));
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $status = 'Terdapat kesalahan pada file di baris '.$failures[0]->row().': ';

            foreach ($failures as $failure) {
                $status .= $failure->errors()[0].'; ';
            }

            $status .= 'File tidak akan diimpor hingga seluruh kesalahan diperbaiki.';

            return back()->withErrors(['status' => $status]);
        }

        return back()->with('status', 'Akun berhasil diimpor');
    }

    public function resetPasswordById(User $user)
    {
        if (request()->user()->role !== 'admin') {
            abort(403);
        }

        $user->password = Hash::make('password123');
        $user->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return redirect()->route('akun.menu')->with('status', "Password untuk user dengan username $user->username berhasil di-reset, sesi aktif user tersebut telah diakhiri");
    }
}
