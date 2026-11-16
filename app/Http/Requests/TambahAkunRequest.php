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
        $actor = $this->user();

        if ($actor?->role === UserRole::Admin->value) {
            return true;
        }

        return $actor?->role === UserRole::AdminUnitKerja->value && $actor->unit_kerja_id !== null;
    }

    protected function prepareForValidation(): void
    {
        $actor = $this->user();

        if ($actor?->role === UserRole::AdminUnitKerja->value && $actor->unit_kerja_id !== null) {
            $unitKerja = UnitKerja::query()->find($actor->unit_kerja_id);

            if ($unitKerja !== null) {
                $this->merge([
                    'jenisUnitKerja' => 'Puskesmas',
                    'namaUnitKerja' => $unitKerja->nama,
                ]);
            }
        } elseif (! $this->has('jenisUnitKerja') || empty($this->input('jenisUnitKerja'))) {
            $this->merge([
                'jenisUnitKerja' => 'Puskesmas',
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $role = $this->input('role');
        $allowedUnitTypes = ['Puskesmas'];
        $isUnitAdmin = $this->user()?->role === UserRole::AdminUnitKerja->value;

        $requiresDepartemen = $role === UserRole::MahasiswaMagang->value;

        $departemenRule = Rule::exists('departemens', 'nama');
        if ($isUnitAdmin) {
            $departemenRule->where('unit_kerja_id', $this->user()->unit_kerja_id);
        } else {
            $departemenRule->where(function ($query) {
                $query->whereIn(
                    'unit_kerja_id',
                    UnitKerja::where('nama', $this->input('namaUnitKerja'))
                        ->where('jenis', 'Puskesmas')
                        ->select('id')
                );
            });
        }

        $allowedRoles = [
            UserRole::DokterMagang->value,
            UserRole::PembimbingLapangan->value,
            UserRole::MahasiswaMagang->value,
        ];

        return [
            'role' => ['required', Rule::in($allowedRoles)],
            'nama' => ['required', 'string', 'max:255'],
            'jenisUnitKerja' => [
                'required',
                'string',
                Rule::in($allowedUnitTypes),
            ],
            'namaUnitKerja' => [
                'required',
                'string',
                'max:255',
                Rule::exists('unit_kerjas', 'nama')->where(function ($query) use ($isUnitAdmin) {
                    if ($isUnitAdmin) {
                        $query->where('id', $this->user()->unit_kerja_id);

                        return;
                    }

                    $query->where('jenis', 'Puskesmas');
                }),
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
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role yang dapat ditambahkan hanya dokter magang, pembimbing lapangan, atau mahasiswa magang',
            'role' => 'Role tidak valid',
            'nama' => 'Nama tidak valid',
            'jenisUnitKerja.required' => 'Jenis unit kerja wajib dipilih untuk role ini',
            'jenisUnitKerja.in' => 'Jenis unit kerja hanya bisa puskesmas',
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
