<?php

namespace App\Http\Controllers;

use App\Http\Requests\TambahAkunRequest;
use App\Http\Requests\UbahDataRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\UsersImport;
use App\Models\Puskesmas;
use App\Models\RiwayatProfilDokterMagang;
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
        $daftarNamaPuskesmas = Puskesmas::select('nama')->pluck('nama')->toArray();

        return view('tambah-akun', compact('daftarNamaPuskesmas'));
    }

    public function editData()
    {
        $user = request()->user();
        $dataBaru = RiwayatProfilDokterMagang::where('dokter_magang_id', $user->id)->latest()->first();

        return view('ubah-data', compact('user', 'dataBaru'));
    }

    public function updateData(UbahDataRequest $request, User $user)
    {
        if ($user->id !== request()->user()->id) {
            abort(403);
        }

        $status = 'Data akun Anda berhasil di-update';

        if ($user->role === 'dokter_magang') {
            if ($user->is_profile_change_pending) {
                return back()->withErrors(['status' => 'Ubah data masih dalam status pending']);
            }

            $user->is_profile_change_pending = true;
            $user->save();

            $status .= ', silahkan konfirmasi ke pembimbing lapangan';

            RiwayatProfilDokterMagang::create([...$request->validated(), 'dokter_magang_id' => $user->id]);
        } else {
            $user->update($request->validated());
        }

        return back()->with('status', $status);
    }

    public function tambahAkun(TambahAkunRequest $request)
    {
        $validated = $request->validated();

        if ($validated['role'] === 'admin') {
            return back()->withErrors('status', 'Menambah akun dengan role admin tidak diperbolehkan');
        }

        $username = User::generateUniqueUsername($validated['nama']);

        User::create([...$validated, 'username' => $username, 'password' => Hash::make('password123'), 'puskesmas_id' => Puskesmas::where('nama', $validated['namaPuskesmas'] ?? null)->value('id')]);

        return redirect()->route('akun.menu')->with('status', 'Akun berhasil ditambah');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update([...$validated, 'puskesmas_id' => Puskesmas::where('nama', $validated['namaPuskesmas'] ?? null)->value('id')]);

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
        $daftarNamaPuskesmas = Puskesmas::select('nama')->pluck('nama')->toArray();

        return view('edit-akun', compact('user', 'daftarNamaPuskesmas'));
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
