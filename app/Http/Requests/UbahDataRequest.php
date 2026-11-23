<?php

namespace App\Http\Requests;

use App\Rules\UniqueEncryptedUserIdentifier;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UbahDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('user')->id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
        ];

        if (in_array($user->role, ['dokter_magang', 'mahasiswa_magang'], true)) {
            $rules['nik'] = ['nullable', 'string', 'size:16', new UniqueEncryptedUserIdentifier('nik', $user->id)];
            $rules['nim'] = ['nullable', 'string', 'max:255', new UniqueEncryptedUserIdentifier('nim', $user->id)];
            $rules['alamat'] = ['nullable', 'string', 'max:255'];
            $rules['universitas'] = ['nullable', 'string', 'max:255'];
        } elseif (in_array($user->role, ['pembimbing_lapangan', 'admin_unit_kerja'], true)) {
            $rules['nip'] = ['nullable', 'string', 'size:18', new UniqueEncryptedUserIdentifier('nip', $user->id)];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nama' => 'Nama tidak valid',
            'nik' => 'NIK tidak valid',
            'nim' => 'NIM tidak valid',
            'alamat' => 'Alamat tidak valid',
            'universitas' => 'Universitas tidak valid',
        ];
    }
}
