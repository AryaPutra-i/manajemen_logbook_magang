<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GantiPasswordRequest extends FormRequest
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
        return [
            'passwordLama' => ['required', 'string'],
            'passwordBaru' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'passwordLama.required' => 'Password lama tidak boleh kosong',
            'passwordBaru.required' => 'Password baru tidak boleh kosong',
        ];
    }
}
