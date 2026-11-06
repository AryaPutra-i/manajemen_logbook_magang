<?php

namespace App\Http\Requests;

use App\UserRole;
use App\Models\UnitKerja;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'role' => ['nullable', new Enum(UserRole::class)],
            'nama' => ['required', 'string', 'max:255'],
            'namaUnitKerja' => ['nullable', 'string', 'max:255', Rule::exists('unit_kerjas', 'nama')],
            'namaDepartemen' => [
                'nullable',
                'string',
                'max:255',
                Rule::exists('departemens', 'nama')->where(function ($query) {
                    $unitKerjaName = $this->input('namaUnitKerja', $this->route('user')->unitKerja?->nama);

                    $query->whereIn(
                        'unit_kerja_id',
                        UnitKerja::where('nama', $unitKerjaName)->select('id')
                    );
                }),
            ],
            'nip' => ['nullable', 'string', 'size:18', Rule::unique('users')->ignore($userId)],
            'nik' => ['nullable', 'string', 'size:16', Rule::unique('users')->ignore($userId)],
            'nim' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'alamat' => ['nullable', 'string', 'max:255'],
            'universitas' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $user = $this->route('user');
            $role = $this->input('role', $user->role);
            $requiresPlacement = in_array($role, [
                UserRole::DokterMagang->value,
                UserRole::PembimbingLapangan->value,
                UserRole::MahasiswaMagang->value,
            ], true);

            $unitKerjaName = $this->input('namaUnitKerja', $user->unitKerja?->nama);
            $departemenName = $this->input('namaDepartemen', $user->departemen?->nama);

            if ($requiresPlacement && blank($unitKerjaName)) {
                $validator->errors()->add('namaUnitKerja', 'Unit kerja wajib diisi untuk role ini');
            }

            if ($requiresPlacement && blank($departemenName)) {
                $validator->errors()->add('namaDepartemen', 'Departemen wajib diisi untuk role ini');
            }
        });
    }

    public function messages(): array
    {
        return [
            'role' => 'Role tidak valid',
            'nama' => 'Nama tidak valid',
            'namaUnitKerja.exists' => 'Tidak ada unit kerja dengan nama tersebut',
            'namaDepartemen.exists' => 'Departemen tidak sesuai dengan unit kerja yang dipilih',
            'nip' => 'NIP tidak valid',
            'nip.unique' => 'NIP sudah terdaftar',
            'nik' => 'NIK tidak valid',
            'nik.unique' => 'NIK sudah terdaftar',
            'nim' => 'NIM tidak valid',
            'nim.unique' => 'NIM sudah terdaftar',
            'alamat' => 'Alamat tidak valid',
            'universitas' => 'Universitas tidak valid',
        ];
    }
}
