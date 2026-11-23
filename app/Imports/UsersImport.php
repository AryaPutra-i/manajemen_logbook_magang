<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\User;
use Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements SkipsEmptyRows, ToModel, WithColumnLimit, WithHeadingRow, WithValidation
{
    protected ?User $authUser;

    public function __construct(?User $authUser = null)
    {
        $this->authUser = $authUser;
    }

    public function endColumn(): string
    {
        return 'J';
    }

    public function isEmptyWhen(array $row): bool
    {
        return collect($row)->every(fn (mixed $value): bool => blank($value));
    }

    public function model(array $row): ?Model
    {
        $role = $row['role'];
        $jenisUnitKerja = $row['jenis_unit_kerja'];
        $unitKerjaId = $this->resolveUnitKerjaId($row);
        $departemenNama = $this->nullableValue($row['departemen'] ?? null);
        $departemenId = $this->resolveDepartemenId($departemenNama, $unitKerjaId, $jenisUnitKerja);
        $isMagang = in_array($role, ['dokter_magang', 'mahasiswa_magang'], true);

        return new User([
            'username' => User::generateUniqueUsername($row['nama_lengkap']),
            'password' => Hash::make('password123'),
            'nama' => $row['nama_lengkap'],
            'role' => $role,
            'unit_kerja_id' => $unitKerjaId,
            'departemen_id' => $role === 'dokter_magang' ? null : $departemenId,
            'nip' => $role === 'pembimbing_lapangan'
                ? $this->nullableValue($row['nip_pembimbing_lapangan'] ?? null)
                : null,
            'nik' => $isMagang
                ? $this->nullableValue($row['nik_doktermahasiswa_magang'] ?? null)
                : null,
            'nim' => $isMagang
                ? $this->nullableValue($row['nim_doktermahasiswa_magang'] ?? null)
                : null,
            'alamat' => $isMagang
                ? $this->nullableValue($row['alamat_doktermahasiswa_magang'] ?? null)
                : null,
            'universitas' => $isMagang
                ? $this->nullableValue($row['universitas_doktermahasiswa_magang'] ?? null)
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:dokter_magang,pembimbing_lapangan,mahasiswa_magang'],
            'jenis_unit_kerja' => ['required', Rule::in(['Dinas_Kesehatan', 'Puskesmas'])],
            'puskesmas' => [
                'nullable',
                'required_if:jenis_unit_kerja,Puskesmas',
                'string',
                Rule::exists('unit_kerjas', 'nama')->where('jenis', 'Puskesmas'),
            ],
            'departemen' => ['nullable', 'required_if:role,mahasiswa_magang', 'string', 'max:255'],
            'nip_pembimbing_lapangan' => [
                'nullable',
                'required_if:role,pembimbing_lapangan',
                'string',
                'size:18',
            ],
            'nik_doktermahasiswa_magang' => [
                'nullable',
                'required_if:role,dokter_magang,mahasiswa_magang',
                'string',
                'size:16',
            ],
            'nim_doktermahasiswa_magang' => [
                'nullable',
                'required_if:role,dokter_magang,mahasiswa_magang',
                'string',
                'max:255',
            ],
            'alamat_doktermahasiswa_magang' => [
                'nullable',
                'required_if:role,dokter_magang,mahasiswa_magang',
                'string',
                'max:255',
            ],
            'universitas_doktermahasiswa_magang' => [
                'nullable',
                'required_if:role,dokter_magang,mahasiswa_magang',
                'string',
                'max:255',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            foreach ($validator->getData() as $rowNumber => $data) {
                $field = fn (string $name): string => $rowNumber.'.'.$name;

                if ($validator->errors()->hasAny([
                    $field('role'),
                    $field('jenis_unit_kerja'),
                    $field('puskesmas'),
                ])) {
                    continue;
                }

                $role = $data['role'];
                $jenisUnitKerja = $data['jenis_unit_kerja'];
                $unitKerjaId = $this->resolveUnitKerjaId($data);

                if ($role === 'dokter_magang' && $jenisUnitKerja !== 'Puskesmas') {
                    $validator->errors()->add($field('jenis_unit_kerja'), 'Dokter magang hanya dapat ditempatkan di Puskesmas');
                }

                if ($unitKerjaId === null) {
                    $validator->errors()->add($field('jenis_unit_kerja'), 'Unit kerja tidak ditemukan');

                    continue;
                }

                if (
                    $this->authUser?->role === 'admin_unit_kerja'
                    && ($this->authUser->unit_kerja_id === null || $unitKerjaId !== (int) $this->authUser->unit_kerja_id)
                ) {
                    $scopeField = $jenisUnitKerja === 'Dinas_Kesehatan' ? 'jenis_unit_kerja' : 'puskesmas';
                    $validator->errors()->add($field($scopeField), 'Anda hanya diperbolehkan mengimpor akun untuk unit kerja Anda sendiri');
                }

                $departemenRequired = $role === 'mahasiswa_magang'
                    || ($role === 'pembimbing_lapangan' && $jenisUnitKerja === 'Dinas_Kesehatan');
                $departemenNama = $this->nullableValue($data['departemen'] ?? null);

                if ($departemenRequired && $departemenNama === null && ! $validator->errors()->has($field('departemen'))) {
                    $validator->errors()->add($field('departemen'), 'Departemen wajib diisi untuk role dan unit kerja ini');

                    continue;
                }

                if ($departemenNama !== null && ! $this->departemenExists($departemenNama, $unitKerjaId, $jenisUnitKerja)) {
                    $validator->errors()->add($field('departemen'), 'Departemen tidak terdaftar pada unit kerja yang dipilih');
                }

                foreach ([
                    'nip_pembimbing_lapangan' => 'nip',
                    'nik_doktermahasiswa_magang' => 'nik',
                    'nim_doktermahasiswa_magang' => 'nim',
                ] as $input => $column) {
                    $value = $data[$input] ?? null;

                    if ($value !== null && User::whereNotNull($column)->get([$column])->contains(
                        fn (User $user): bool => $user->{$column} === $value
                    )) {
                        $validator->errors()->add($field($input), ucfirst($column).' sudah terdaftar');
                    }
                }
            }
        });
    }

    private function resolveUnitKerjaId(array $row): ?int
    {
        if ($row['jenis_unit_kerja'] === 'Dinas_Kesehatan') {
            return UnitKerja::where('jenis', 'Dinas_Kesehatan')->value('id');
        }

        return UnitKerja::where('nama', $row['puskesmas'] ?? null)
            ->where('jenis', 'Puskesmas')
            ->value('id');
    }

    private function resolveDepartemenId(?string $nama, ?int $unitKerjaId, string $jenisUnitKerja): ?int
    {
        if ($nama === null || $unitKerjaId === null) {
            return null;
        }

        $query = Departemen::where('nama', $nama);

        if ($jenisUnitKerja === 'Dinas_Kesehatan') {
            $query->where(function ($query) use ($unitKerjaId): void {
                $query->where('unit_kerja_id', $unitKerjaId)->orWhereNull('unit_kerja_id');
            });
        } else {
            $query->where('unit_kerja_id', $unitKerjaId);
        }

        return $query->value('id');
    }

    private function departemenExists(string $nama, int $unitKerjaId, string $jenisUnitKerja): bool
    {
        $query = Departemen::where('nama', $nama);

        if ($jenisUnitKerja === 'Dinas_Kesehatan') {
            $query->where(function ($query) use ($unitKerjaId): void {
                $query->where('unit_kerja_id', $unitKerjaId)->orWhereNull('unit_kerja_id');
            });
        } else {
            $query->where('unit_kerja_id', $unitKerjaId);
        }

        return $query->exists();
    }

    private function nullableValue(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return (string) $value;
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'role.required' => 'Role wajib diisi',
            'role.in' => 'Role tidak valid',
            'jenis_unit_kerja.required' => 'Jenis unit kerja wajib diisi',
            'jenis_unit_kerja.in' => 'Jenis unit kerja tidak valid',
            'puskesmas.required_if' => 'Puskesmas wajib diisi untuk jenis unit kerja Puskesmas',
            'puskesmas.exists' => 'Puskesmas tidak terdaftar',
            'departemen.required_if' => 'Departemen wajib diisi untuk mahasiswa magang',
            'nip_pembimbing_lapangan.required_if' => 'NIP wajib diisi untuk pembimbing lapangan',
            'nip_pembimbing_lapangan.string' => 'NIP harus berupa teks (formatkan sel sebagai Teks di Excel)',
            'nip_pembimbing_lapangan.size' => 'NIP harus 18 digit',
            'nik_doktermahasiswa_magang.required_if' => 'NIK wajib diisi untuk dokter atau mahasiswa magang',
            'nik_doktermahasiswa_magang.string' => 'NIK harus berupa teks (formatkan sel sebagai Teks di Excel)',
            'nik_doktermahasiswa_magang.size' => 'NIK harus 16 digit',
            'nim_doktermahasiswa_magang.required_if' => 'NIM wajib diisi untuk dokter atau mahasiswa magang',
            'nim_doktermahasiswa_magang.string' => 'NIM harus berupa teks',
            'alamat_doktermahasiswa_magang.required_if' => 'Alamat wajib diisi untuk dokter atau mahasiswa magang',
            'universitas_doktermahasiswa_magang.required_if' => 'Universitas wajib diisi untuk dokter atau mahasiswa magang',
        ];
    }
}
