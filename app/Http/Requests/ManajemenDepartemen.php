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

    protected function prepareForValidation(): void
    {
        $actor = $this->user();
        if ($actor?->role === 'admin_unit_kerja' && $actor->unit_kerja_id !== null) {
            $this->merge([
                'unit_kerja_id' => $actor->unit_kerja_id,
            ]);
        }
    }

    public function rules(): array
    {
        $actor = $this->user();
        $isUnitAdmin = $actor?->role === 'admin_unit_kerja';

        $unitKerjaRule = Rule::exists('unit_kerjas', 'id');
        if ($isUnitAdmin) {
            $unitKerjaRule->where('id', $actor->unit_kerja_id);
        }

        return [
            'nama' => ['required', 'string', 'max:255'],
            'unit_kerja_id' => [
                'required',
                'integer',
                $unitKerjaRule,
            ],
        ];
    }
}
