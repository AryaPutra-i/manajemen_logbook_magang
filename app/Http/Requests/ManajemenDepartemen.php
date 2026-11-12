<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManajemenDepartemen extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',

            'unit_kerja_id' => [
                'required',
                'integer',
                Rule::exists('unit_kerjas', 'id'),
            ],
        ];
    }
}
