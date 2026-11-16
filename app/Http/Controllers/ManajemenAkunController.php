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
use App\UserRole;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ManajemenAkunController extends Controller
{
    public function menu()
    {
        $user = request()->user();

        if ($user->role === UserRole::Admin->value) {
            $users = User::all();
        } elseif ($user->role === UserRole::AdminUnitKerja->value) {
            $this->abortIfUnitAdminWithoutUnit($user);
            $users = User::query()
                ->where('unit_kerja_id', $user->unit_kerja_id)
                ->where('role', '!=', UserRole::Admin->value)
                ->get();
        } else {
            return view('manajemen-akun-menu', compact('user'));
        }

        return view('manajemen-akun', compact('users'));
    }

    public function create()
    {
        $authUser = request()->user();
        $this->abortIfUnitAdminWithoutUnit($authUser);

        $daftarUnitKerja = $authUser->role === UserRole::AdminUnitKerja->value
            ? UnitKerja::with('departemens')->where('id', $authUser->unit_kerja_id)->get(['id', 'nama', 'jenis', 'created_at'])
            : UnitKerja::with('departemens')->get(['id', 'nama', 'jenis', 'created_at']);

        $daftarUnitKerja->each(function (UnitKerja $unitKerja): void {
            if ($unitKerja->jenis === 'Dinas_Kesehatan' && $unitKerja->created_at === null) {
                $unitKerja->jenis = 'Puskesmas';
            }
        });
        $daftarUnitKerja = $daftarUnitKerja->filter(fn (UnitKerja $unitKerja): bool => $unitKerja->jenis === 'Puskesmas')->values();
        $daftarDepartemenDinasKesehatan = collect();

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
        $authUser = request()->user();
        $this->abortIfUnitAdminWithoutUnit($authUser);
        $validated = $request->validated();

        if (in_array($validated['role'], [UserRole::Admin->value, UserRole::AdminUnitKerja->value], true)) {
            return back()->withErrors(['status' => 'Menambah akun dengan role ini tidak diperbolehkan']);
        }

        $username = User::generateUniqueUsername($validated['nama']);
        $unitKerjaId = null;
        $isUnitAdmin = $authUser->role === UserRole::AdminUnitKerja->value;

        if ($isUnitAdmin) {
            $unitKerjaId = $authUser->unit_kerja_id;
        } elseif (in_array($validated['role'], [UserRole::DokterMagang->value, UserRole::PembimbingLapangan->value, UserRole::MahasiswaMagang->value], true)) {
            $jenisUnitKerja = $validated['jenisUnitKerja'] ?? null;
            if ($jenisUnitKerja === 'Puskesmas') {
                $unitKerjaId = UnitKerja::where('nama', $validated['namaUnitKerja'] ?? null)->where('jenis', 'Puskesmas')->value('id');
            }
        }

        $departemenId = $validated['role'] === UserRole::DokterMagang->value
            ? null
            : $this->resolveDepartemenId($validated['namaDepartemen'] ?? null, $unitKerjaId, $isUnitAdmin);

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
        $authUser = request()->user();
        $this->abortUnlessUserInUnitScope($authUser, $user, 'mengubah akun ini');

        $validated = $request->validated();
        $role = $validated['role'] ?? $user->role;

        if ($authUser->role === UserRole::AdminUnitKerja->value && $role === UserRole::Admin->value) {
            return back()->withErrors(['status' => 'Menetapkan role admin tidak diperbolehkan']);
        }

        if ($authUser->role !== UserRole::Admin->value && ($validated['role'] ?? null) === UserRole::AdminUnitKerja->value) {
            return back()->withErrors(['status' => 'Hanya super admin yang dapat menetapkan role admin unit kerja']);
        }

        $unitKerjaId = null;
        $isUnitAdmin = $authUser->role === UserRole::AdminUnitKerja->value;
        if ($isUnitAdmin) {
            $unitKerjaId = $authUser->unit_kerja_id;
            $namaDepartemen = $role === 'dokter_magang'
                ? null
                : ($validated['namaDepartemen'] ?? $user->departemen?->nama);
        } else {
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

            if (in_array($role, [UserRole::DokterMagang->value, UserRole::PembimbingLapangan->value, UserRole::MahasiswaMagang->value, UserRole::AdminUnitKerja->value], true)) {
                if ($jenisUnitKerja === 'Dinas_Kesehatan') {
                    $unitKerjaId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
                } elseif ($jenisUnitKerja === 'Puskesmas') {
                    $unitKerjaId = UnitKerja::where('nama', $namaUnitKerja)->where('jenis', 'Puskesmas')->value('id');
                }
            }
        }

        $departemenId = $role === UserRole::DokterMagang->value
            ? null
            : $this->resolveDepartemenId($namaDepartemen, $unitKerjaId, $isUnitAdmin);

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
        $authUser = request()->user();
        $user = User::findOrFail($id);

        if ($user->role === UserRole::Admin->value) {
            return back()->withErrors(['status' => 'Akun dengan role admin tidak dapat dihapus']);
        }
        if ($user->role === UserRole::AdminUnitKerja->value && $authUser->role !== UserRole::Admin->value) {
            return back()->withErrors(['status' => 'Hanya super admin yang dapat menghapus akun admin unit kerja']);
        }
        if ($user->id === $authUser->id) {
            return back()->withErrors(['status' => 'Anda tidak dapat menghapus akun Anda sendiri']);
        }

        $this->abortUnlessUserInUnitScope($authUser, $user, 'menghapus akun ini');

        $username = $user->username;
        $user->delete();

        return redirect()->route('akun.menu')->with('status', "Akun dengan username $username berhasil dihapus");
    }

    public function edit(User $user)
    {
        $authUser = request()->user();

        $this->abortUnlessUserInUnitScope($authUser, $user, 'mengedit akun ini');

        if ($authUser->role === UserRole::AdminUnitKerja->value) {
            $daftarUnitKerja = UnitKerja::with('departemens')->where('id', $authUser->unit_kerja_id)->get(['id', 'nama', 'jenis', 'created_at']);
        } else {
            $daftarUnitKerja = UnitKerja::with('departemens')->get(['id', 'nama', 'jenis', 'created_at']);
        }

        $daftarUnitKerja->each(function (UnitKerja $unitKerja): void {
            if ($unitKerja->jenis === 'Dinas_Kesehatan' && $unitKerja->created_at === null) {
                $unitKerja->jenis = 'Puskesmas';
            }
        });
        $user->setRelation('unitKerja', $daftarUnitKerja->firstWhere('id', $user->unit_kerja_id));
        $daftarDepartemenDinasKesehatan = $this->daftarDepartemenDinasKesehatanFor($authUser);

        return view('edit-akun', compact('user', 'daftarUnitKerja', 'daftarDepartemenDinasKesehatan'));
    }

    public function import(Request $request)
    {
        $this->abortIfUnitAdminWithoutUnit($request->user());

        $request->validate([
            'accounts_xlsx' => ['required', 'file', 'mimes:xlsx', 'max:2048'],
        ]);

        try {
            Excel::import(new UsersImport($request->user()), $request->file('accounts_xlsx'));
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $status = 'Terdapat kesalahan pada file di baris ' . $failures[0]->row() . ': ';

            foreach ($failures as $failure) {
                $status .= $failure->errors()[0] . '; ';
            }

            $status .= 'File tidak akan diimpor hingga seluruh kesalahan diperbaiki.';

            return back()->withErrors(['status' => $status]);
        }

        return back()->with('status', 'Akun berhasil diimpor');
    }

    public function resetPasswordById(User $user)
    {
        $authUser = request()->user();

        $this->abortUnlessUserInUnitScope($authUser, $user, 'me-reset password akun ini');

        if (! in_array($authUser->role, [UserRole::Admin->value, UserRole::AdminUnitKerja->value], true)) {
            abort(403);
        }

        $user->password = Hash::make('password123');
        $user->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return redirect()->route('akun.menu')->with('status', "Password untuk user dengan username $user->username berhasil di-reset, sesi aktif user tersebut telah diakhiri");
    }

    private function abortIfUnitAdminWithoutUnit(User $actor): void
    {
        if ($actor->role === UserRole::AdminUnitKerja->value && $actor->unit_kerja_id === null) {
            abort(403, 'Akun admin unit kerja harus memiliki unit kerja.');
        }
    }

    private function abortUnlessUserInUnitScope(User $actor, User $target, string $action): void
    {
        if ($actor->role !== UserRole::AdminUnitKerja->value) {
            return;
        }

        $this->abortIfUnitAdminWithoutUnit($actor);

        $sameUnit = $target->unit_kerja_id !== null
            && (int) $target->unit_kerja_id === (int) $actor->unit_kerja_id;

        if ($target->role === UserRole::Admin->value || ! $sameUnit) {
            abort(403, 'Anda tidak memiliki akses untuk '.$action);
        }

        if ($target->role === UserRole::AdminUnitKerja->value) {
            abort(403, 'Hanya super admin yang memiliki wewenang untuk '.$action.' dengan role admin unit kerja');
        }
    }

    private function resolveDepartemenId(?string $namaDepartemen, ?int $unitKerjaId, bool $onlyWithinUnit): ?int
    {
        if (blank($namaDepartemen)) {
            return null;
        }

        $query = Departemen::query()->where('nama', $namaDepartemen);

        if ($onlyWithinUnit) {
            $query->where('unit_kerja_id', $unitKerjaId);
        } else {
            $query->where(function ($departemenQuery) use ($unitKerjaId): void {
                $departemenQuery->where('unit_kerja_id', $unitKerjaId)->orWhereNull('unit_kerja_id');
            });
        }

        return $query->value('id');
    }

    private function daftarDepartemenDinasKesehatanFor(User $actor): Collection
    {
        if ($actor->role === UserRole::AdminUnitKerja->value) {
            $jenis = UnitKerja::query()->where('id', $actor->unit_kerja_id)->value('jenis');

            if ($jenis !== 'Dinas_Kesehatan') {
                return collect();
            }

            return Departemen::query()
                ->where('unit_kerja_id', $actor->unit_kerja_id)
                ->orderBy('nama')
                ->get(['id', 'nama']);
        }

        $dinkesId = UnitKerja::query()->where('jenis', 'Dinas_Kesehatan')->value('id');

        return Departemen::query()
            ->where(function ($query) use ($dinkesId): void {
                $query->where('unit_kerja_id', $dinkesId)->orWhereNull('unit_kerja_id');
            })
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }
}
