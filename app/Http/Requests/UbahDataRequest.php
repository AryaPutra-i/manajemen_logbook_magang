<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            $rules['nik'] = ['nullable', 'string', 'size:16', Rule::unique('users')->ignore($user->id)];
            $rules['nim'] = ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)];
            $rules['alamat'] = ['nullable', 'string', 'max:255'];
            $rules['universitas'] = ['nullable', 'string', 'max:255'];
        } elseif ($user->role === 'pembimbing_lapangan') {
            $rules['nip'] = ['nullable', 'string', 'size:18', Rule::unique('users')->ignore($user->id)];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nama' => 'Nama tidak valid',
            'nik' => 'NIK tidak valid',
            'nik.unique' => 'NIK sudah terdaftar',
            'nim' => 'NIM tidak valid',
            'nim.unique' => 'NIM sudah terdaftar',
            'alamat' => 'Alamat tidak valid',
            'universitas' => 'Universitas tidak valid',
        ];
    }
}
