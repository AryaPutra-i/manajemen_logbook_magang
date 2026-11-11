<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ManajemenDepartemen extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kepala_upt' => 'required|string|max:255',
            'nomor_telepon' => 'required|integer|max:20',
            'email' => 'required|email|max:255',
            'kode_puskesmas' => 'required|string|max:255',
            'jenis' => ['required', Rule::in(['dinas_kesehatan', 'puskesmas'])],
        ];
    }
}
