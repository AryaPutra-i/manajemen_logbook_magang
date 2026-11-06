<?php

namespace App\Http\Requests;

use App\UserRole;
use App\Models\UnitKerja;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class TambahAkunRequest extends FormRequest
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
        $requiresPlacement = [
            UserRole::DokterMagang->value,
            UserRole::PembimbingLapangan->value,
            UserRole::MahasiswaMagang->value,
        ];

        return [
            'role' => ['required', new Enum(UserRole::class)],
            'nama' => ['required', 'string', 'max:255'],
            'namaUnitKerja' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => in_array($this->input('role'), $requiresPlacement, true)),
                Rule::exists('unit_kerjas', 'nama'),
            ],
            'namaDepartemen' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => in_array($this->input('role'), $requiresPlacement, true)),
                Rule::exists('departemens', 'nama')->where(function ($query) {
                    $query->whereIn(
                        'unit_kerja_id',
                        UnitKerja::where('nama', $this->input('namaUnitKerja'))->select('id')
                    );
                }),
            ],
            'nip' => ['nullable', 'string', 'size:18', 'unique:users,nip'],
            'nik' => ['nullable', 'string', 'size:16', 'unique:users,nik'],
            'nim' => ['nullable', 'string', 'max:255', 'unique:users,nim'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'universitas' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'role' => 'Role tidak valid',
            'nama' => 'Nama tidak valid',
            'namaUnitKerja.required' => 'Unit kerja wajib diisi untuk role ini',
            'namaUnitKerja.exists' => 'Tidak ada unit kerja dengan nama tersebut',
            'namaDepartemen.required' => 'Departemen wajib diisi untuk role ini',
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
