<?php

namespace App\Http\Requests;

use App\Models\UnitKerja;
use App\Models\User;
use App\Rules\UniqueEncryptedUserIdentifier;
use App\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $actor = $this->user();
        $target = $this->route('user');

        if ($actor?->role === UserRole::Admin->value) {
            return true;
        }

        if ($actor?->role !== UserRole::AdminUnitKerja->value || ! $target instanceof User) {
            return false;
        }

        return $actor->unit_kerja_id !== null
            && ! in_array($target->role, [UserRole::Admin->value, UserRole::AdminUnitKerja->value], true)
            && $target->unit_kerja_id !== null
            && (int) $target->unit_kerja_id === (int) $actor->unit_kerja_id;
    }

    protected function prepareForValidation(): void
    {
        $actor = $this->user();

        if ($actor?->role !== UserRole::AdminUnitKerja->value || $actor->unit_kerja_id === null) {
            return;
        }

        $unitKerja = UnitKerja::query()->find($actor->unit_kerja_id);

        if ($unitKerja === null) {
            return;
        }

        $this->merge([
            'jenisUnitKerja' => $unitKerja->jenis,
            'namaUnitKerja' => $unitKerja->nama,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;
        $role = $this->input('role', $this->route('user')->role);
        $requiresPlacement = in_array($role, [
            UserRole::AdminUnitKerja->value,
            UserRole::DokterMagang->value,
            UserRole::PembimbingLapangan->value,
            UserRole::MahasiswaMagang->value,
        ], true);
        $isUnitAdmin = $this->user()?->role === UserRole::AdminUnitKerja->value;
        $allowedRoles = $isUnitAdmin
            ? [
                UserRole::DokterMagang->value,
                UserRole::PembimbingLapangan->value,
                UserRole::MahasiswaMagang->value,
            ]
            : [
                UserRole::AdminUnitKerja->value,
                UserRole::DokterMagang->value,
                UserRole::PembimbingLapangan->value,
                UserRole::MahasiswaMagang->value,
            ];

        $allowedUnitTypes = $role === UserRole::DokterMagang->value
            ? ['Puskesmas']
            : ['Dinas_Kesehatan', 'Puskesmas'];

        $currentJenis = $this->route('user')->unitKerja?->jenis
            ?? ($this->route('user')->departemen_id !== null && $this->route('user')->departemen?->unit_kerja_id === null ? 'Dinas_Kesehatan' : null);
        $jenisUnitKerja = $this->input('jenisUnitKerja', $currentJenis);
        $isPuskesmas = $jenisUnitKerja === 'Puskesmas';
        $isDinasKesehatan = $jenisUnitKerja === 'Dinas_Kesehatan';

        $requiresDepartemen = $role === UserRole::MahasiswaMagang->value
            || ($role === UserRole::PembimbingLapangan->value && $isDinasKesehatan);

        $departemenRule = Rule::exists('departemens', 'nama');
        if ($isUnitAdmin) {
            $departemenRule->where('unit_kerja_id', $this->user()->unit_kerja_id);
        } elseif ($isDinasKesehatan) {
            $dinkesId = UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
            $departemenRule->where(function ($query) use ($dinkesId) {
                $query->where('unit_kerja_id', $dinkesId)->orWhereNull('unit_kerja_id');
            });
        } else {
            $departemenRule->where(function ($query) use ($jenisUnitKerja) {
                $unitKerjaName = $this->input('namaUnitKerja', $this->route('user')->unitKerja?->nama);
                $query->whereIn(
                    'unit_kerja_id',
                    UnitKerja::where('nama', $unitKerjaName)
                        ->where('jenis', $jenisUnitKerja)
                        ->select('id')
                );
            });
        }

        return [
            'role' => ['nullable', Rule::in($allowedRoles)],
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
                Rule::exists('unit_kerjas', 'nama')->where(function ($query) use ($isUnitAdmin) {
                    if ($isUnitAdmin) {
                        $query->where('id', $this->user()->unit_kerja_id);

                        return;
                    }

                    $query->where('jenis', $this->input('jenisUnitKerja'));
                }),
            ],
            'namaDepartemen' => [
                'nullable',
                Rule::requiredIf($requiresDepartemen),
                'string',
                'max:255',
                $departemenRule,
            ],
            'nip' => ['nullable', 'string', 'size:18', new UniqueEncryptedUserIdentifier('nip', $userId)],
            'nik' => ['nullable', 'string', 'size:16', new UniqueEncryptedUserIdentifier('nik', $userId)],
            'nim' => ['nullable', 'string', 'max:255', new UniqueEncryptedUserIdentifier('nim', $userId)],
            'alamat' => ['nullable', 'string', 'max:255'],
            'universitas' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        $isUnitAdmin = $this->user()?->role === UserRole::AdminUnitKerja->value;

        return [
            'role.in' => $isUnitAdmin
                ? 'Admin unit kerja hanya diperbolehkan menetapkan role dokter magang, pembimbing lapangan, atau mahasiswa magang'
                : 'Role yang dipilih tidak dapat ditetapkan',
            'role' => 'Role tidak valid',
            'nama' => 'Nama tidak valid',
            'jenisUnitKerja.required' => 'Jenis unit kerja wajib dipilih untuk role ini',
            'jenisUnitKerja.in' => 'Jenis unit kerja tidak sesuai dengan role',
            'namaUnitKerja.required' => 'Unit kerja wajib diisi untuk role ini',
            'namaUnitKerja.exists' => 'Unit kerja tidak sesuai dengan jenis yang dipilih',
            'namaDepartemen.required' => 'Departemen wajib dipilih',
            'namaDepartemen.exists' => 'Departemen tidak sesuai dengan unit kerja yang dipilih',
            'nip' => 'NIP tidak valid',
            'nik' => 'NIK tidak valid',
            'nim' => 'NIM tidak valid',
            'alamat' => 'Alamat tidak valid',
            'universitas' => 'Universitas tidak valid',
        ];
    }
}
