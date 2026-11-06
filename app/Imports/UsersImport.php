<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\User;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Model
    {
        $role = $row['role'];

        $unitKerjaId = UnitKerja::where('nama', $row['unit_kerja'])->value('id');
        $departemenId = Departemen::where('nama', $row['departemen'])
            ->where('unit_kerja_id', $unitKerjaId)
            ->value('id');

        $isMagang = in_array($role, ['dokter_magang', 'mahasiswa_magang'], true);

        return new User([
            'username' => User::generateUniqueUsername($row['nama_lengkap']),
            'password' => Hash::make('password123'),
            'nama' => $row['nama_lengkap'],
            'role' => $role,
            'unit_kerja_id' => $unitKerjaId,
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
            'role' => ['required', 'in:dokter_magang,pembimbing_lapangan,mahasiswa_magang'],
            'unit_kerja' => ['required', 'string', Rule::exists('unit_kerjas', 'nama')],
            'departemen' => ['required', 'string', Rule::exists('departemens', 'nama')],
            'nip_pembimbing_lapangan' => ['nullable', 'required_if:role,pembimbing_lapangan', 'string', 'size:18', 'unique:users,nip'],
            'nik_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'size:16', 'unique:users,nik'],
            'nim_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'max:255', 'unique:users,nim'],
            'alamat_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'max:255'],
            'universitas_magang' => ['nullable', 'required_if:role,dokter_magang,mahasiswa_magang', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $data = $validator->getData();
            $unitKerjaId = UnitKerja::where('nama', $data['unit_kerja'] ?? null)->value('id');

            if (
                $unitKerjaId === null ||
                ! Departemen::where('nama', $data['departemen'] ?? null)
                    ->where('unit_kerja_id', $unitKerjaId)
                    ->exists()
            ) {
                $validator->errors()->add('departemen', 'Departemen tidak terdaftar pada unit kerja yang dipilih');
            }

            foreach ([
                'nip_pembimbing_lapangan' => 'nip',
                'nik_magang' => 'nik',
                'nim_magang' => 'nim',
            ] as $input => $column) {
                $value = $data[$input] ?? null;

                if ($value !== null && User::whereNotNull($column)->get([$column])->contains(
                    fn (User $user): bool => $user->{$column} === $value
                )) {
                    $validator->errors()->add($input, ucfirst($column).' sudah terdaftar');
                }
            }
        });
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'role.required' => 'Role wajib diisi',
            'role.in' => 'Role tidak valid',
            'unit_kerja.required' => 'Unit kerja wajib diisi',
            'unit_kerja.exists' => 'Unit kerja tidak terdaftar',
            'departemen.required' => 'Departemen wajib diisi',
            'departemen.exists' => 'Departemen tidak terdaftar pada unit kerja yang dipilih',
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
