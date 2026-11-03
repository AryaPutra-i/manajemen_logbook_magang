<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\Puskesmas;
use App\Models\User;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        $role = $row['role'];

        $puskesmasId = in_array($role, ['dokter_magang', 'pembimbing_lapangan_puskesmas'], true)
            || ($role === 'mahasiswa_magang' && ! empty($row['puskesmas']))
            ? Puskesmas::where('nama', $row['puskesmas'] ?? null)->value('id')
            : null;

        $departemenId = in_array($role, ['pembimbing_lapangan_departemen'], true)
            || ($role === 'mahasiswa_magang' && ! empty($row['departemen']))
            ? Departemen::where('nama', $row['departemen'] ?? null)->value('id')
            : null;

        $isMagang = in_array($role, ['dokter_magang', 'mahasiswa_magang'], true);

        return new User([
            'username' => User::generateUniqueUsername($row['nama_lengkap']),
            'password' => Hash::make('password123'),
            'nama' => $row['nama_lengkap'],
            'role' => $role,
            'puskesmas_id' => $puskesmasId,
            'departemen_id' => $departemenId,
            'nip' => $isMagang ? null : ($row['nip_pembimbing_lapangan'] ?? null),
            'nik' => $isMagang ? ($row['nik_magang'] ?? null) : null,
            'nim' => $isMagang ? ($row['nim_magang'] ?? null) : null,
            'alamat' => $isMagang ? ($row['alamat_magang'] ?? null) : null,
            'universitas' => $isMagang ? ($row['universitas_magang'] ?? null) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:dokter_magang,pembimbing_lapangan_puskesmas,mahasiswa_magang,pembimbing_lapangan_departemen'],
            'puskesmas' => ['nullable', 'required_if:role,dokter_magang,pembimbing_lapangan_puskesmas', 'string', 'exists:puskesmas,nama'],
            'departemen' => ['nullable', 'required_if:role,pembimbing_lapangan_departemen', 'string', 'exists:departemens,nama'],
            'nip_pembimbing_lapangan' => ['nullable', 'required_if:role,pembimbing_lapangan_puskesmas,pembimbing_lapangan_departemen', 'string', 'size:18', 'unique:users,nip'],
            'nik_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'size:16', 'unique:users,nik'],
            'nim_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'max:255', 'unique:users,nim'],
            'alamat_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'max:255'],
            'universitas_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'max:255'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'role.required' => 'Role wajib diisi',
            'role.in' => 'Role tidak valid',
            'puskesmas.required_if' => 'Puskesmas wajib diisi untuk dokter magang atau pembimbing lapangan puskesmas',
            'puskesmas.exists' => 'Puskesmas tidak terdaftar',
            'departemen.required_if' => 'Departemen wajib diisi untuk pembimbing lapangan departemen',
            'departemen.exists' => 'Departemen tidak terdaftar',
            'nip_pembimbing_lapangan.string' => 'NIP harus berupa teks, bukan angka (ubah format sel menjadi Teks di Excel)',
            'nip_pembimbing_lapangan.size' => 'NIP harus 18 digit',
            'nip_pembimbing_lapangan.unique' => 'NIP sudah terdaftar',
            'nik_magang.string' => 'NIK harus berupa teks, bukan angka (ubah format sel menjadi Teks di Excel)',
            'nik_magang.size' => 'NIK harus 16 digit',
            'nik_magang.unique' => 'NIK sudah terdaftar',
            'nim_magang.string' => 'NIM harus berupa teks, bukan angka (ubah format sel menjadi Teks di Excel)',
            'nim_magang.unique' => 'NIM sudah terdaftar',
            'nip_pembimbing_lapangan.required_if' => 'NIP wajib diisi untuk pembimbing lapangan',
            'nik_magang.required_if' => 'NIK wajib diisi untuk dokter atau mahasiswa magang',
            'nim_magang.required_if' => 'NIM wajib diisi untuk dokter atau mahasiswa magang',
            'alamat_magang.required_if' => 'Alamat wajib diisi untuk dokter atau mahasiswa magang',
            'universitas_magang.required_if' => 'Universitas wajib diisi untuk dokter atau mahasiswa magang',
        ];
    }
}
