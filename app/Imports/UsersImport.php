<?php

namespace App\Imports;

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
        if (($row['role'] ?? null) === 'admin') {
            return null;
        }

        return new User([
            'username' => User::generateUniqueUsername($row['nama_lengkap']),
            'password' => Hash::make('password123'),
            'nama' => $row['nama_lengkap'],
            'role' => $row['role'],
            'puskesmas_id' => Puskesmas::where('nama', $row['puskesmas'])->value('id'),
            'nip' => $row['nip_pembimbing_lapangan'] ?? null,
            'nik' => $row['nik_dokter_magang'] ?? null,
            'nim' => $row['nim_dokter_magang'] ?? null,
            'alamat' => $row['alamat_dokter_magang'] ?? null,
            'universitas' => $row['universitas_dokter_magang'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:dokter_magang,pembimbing_lapangan'],
            'puskesmas' => ['required', 'string', 'exists:puskesmas,nama'],
            'nip_pembimbing_lapangan' => ['nullable', 'required_if:role,pembimbing_lapangan', 'string', 'size:18', 'unique:users,nip'],
            'nik_dokter_magang' => ['nullable', 'required_if:role,dokter_magang', 'string', 'size:16', 'unique:users,nik'],
            'nim_dokter_magang' => ['nullable', 'required_if:role,dokter_magang', 'string', 'max:255', 'unique:users,nim'],
            'alamat_dokter_magang' => ['nullable', 'required_if:role,dokter_magang', 'string', 'max:255'],
            'universitas_dokter_magang' => ['nullable', 'required_if:role,dokter_magang', 'string', 'max:255'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'role.required' => 'Role wajib diisi',
            'role.in' => 'Role harus dokter_magang atau pembimbing_lapangan',
            'puskesmas.required' => 'Puskesmas wajib diisi',
            'puskesmas.exists' => 'Puskesmas tidak terdaftar',
            'nip_pembimbing_lapangan.string' => 'NIP harus berupa teks, bukan angka (ubah format sel menjadi Teks di Excel)',
            'nip_pembimbing_lapangan.size' => 'NIP harus 18 digit',
            'nip_pembimbing_lapangan.unique' => 'NIP sudah terdaftar',
            'nik_dokter_magang.string' => 'NIK harus berupa teks, bukan angka (ubah format sel menjadi Teks di Excel)',
            'nik_dokter_magang.size' => 'NIK harus 16 digit',
            'nik_dokter_magang.unique' => 'NIK sudah terdaftar',
            'nim_dokter_magang.string' => 'NIM harus berupa teks, bukan angka (ubah format sel menjadi Teks di Excel)',
            'nim_dokter_magang.unique' => 'NIM sudah terdaftar',
            'nip_pembimbing_lapangan.required_if' => 'NIP wajib diisi untuk pembimbing lapangan',
            'nik_dokter_magang.required_if' => 'NIK wajib diisi untuk dokter magang',
            'nim_dokter_magang.required_if' => 'NIM wajib diisi untuk dokter magang',
            'alamat_dokter_magang.required_if' => 'Alamat wajib diisi untuk dokter magang',
            'universitas_dokter_magang.required_if' => 'Universitas wajib diisi untuk dokter magang',
        ];
    }
}
