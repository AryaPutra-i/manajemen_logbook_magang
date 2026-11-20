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

        $currentId = $this->route('id');

        $uniqueNamaRule = Rule::unique('departemens')
            ->where('unit_kerja_id', $this->input('unit_kerja_id', $isUnitAdmin ? $actor->unit_kerja_id : null));

        if ($currentId) {
            $uniqueNamaRule->ignore($currentId);
        }

        return [
            'nama' => ['required', 'string', 'max:255', $uniqueNamaRule],
            'unit_kerja_id' => [
                'required',
                'integer',
                $unitKerjaRule,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.unique' => 'Departemen dengan nama tersebut sudah ada di unit kerja ini.',
        ];
    }
}
