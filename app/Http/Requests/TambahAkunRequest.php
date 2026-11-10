<?php

namespace App\Http\Requests;

use App\Models\UnitKerja;
use App\UserRole;
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
        $role = $this->input('role');
        $requiresPlacement = in_array($role, [
            UserRole::DokterMagang->value,
            UserRole::PembimbingLapangan->value,
            UserRole::MahasiswaMagang->value,
        ], true);
        $allowedUnitTypes = $role === UserRole::DokterMagang->value
            ? ['Puskesmas']
            : ['Dinas_Kesehatan', 'Puskesmas'];

        $jenisUnitKerja = $this->input('jenisUnitKerja');
        $isPuskesmas = $jenisUnitKerja === 'Puskesmas';
        $isDinasKesehatan = $jenisUnitKerja === 'Dinas_Kesehatan';

        $requiresDepartemen = $role === UserRole::MahasiswaMagang->value
            || ($role === UserRole::PembimbingLapangan->value && $isDinasKesehatan);

        $departemenRule = Rule::exists('departemens', 'nama');
        if ($isDinasKesehatan) {
            $dinkesId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
            $departemenRule->where(function ($query) use ($dinkesId) {
                $query->where('unit_kerja_id', $dinkesId)->orWhereNull('unit_kerja_id');
            });
        } else {
            $departemenRule->where(function ($query) {
                $query->whereIn(
                    'unit_kerja_id',
                    UnitKerja::where('nama', $this->input('namaUnitKerja'))
                        ->where('jenis', $this->input('jenisUnitKerja'))
                        ->select('id')
                );
            });
        }

        return [
            'role' => ['required', new Enum(UserRole::class)],
            'nama' => ['required', 'string', 'max:255'],
            'jenisUnitKerja' => [
                'nullable',
                'string',
                Rule::requiredIf($requiresPlacement),
                Rule::in($allowedUnitTypes),
            ],
            'namaUnitKerja' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($requiresPlacement && $isPuskesmas),
                Rule::exists('unit_kerjas', 'nama')->where(
                    fn ($query) => $query->where('jenis', $this->input('jenisUnitKerja'))
                ),
            ],
            'namaDepartemen' => [
                'nullable',
                Rule::requiredIf($requiresDepartemen),
                'string',
                'max:255',
                $departemenRule,
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
            'jenisUnitKerja.required' => 'Jenis unit kerja wajib dipilih untuk role ini',
            'jenisUnitKerja.in' => 'Jenis unit kerja tidak sesuai dengan role',
            'namaUnitKerja.required' => 'Unit kerja wajib diisi untuk role ini',
            'namaUnitKerja.exists' => 'Unit kerja tidak sesuai dengan jenis yang dipilih',
            'namaDepartemen.required' => 'Departemen wajib dipilih',
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
