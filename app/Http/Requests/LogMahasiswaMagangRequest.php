<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LogMahasiswaMagangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->user()->role === 'mahasiswa_magang';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'namaKegiatan' => ['required', 'string'],
            'tanggal' => ['required', 'date', Rule::date()->afterOrEqual(today()->subDays(6)), 'before_or_equal:today'],
            'deskripsi' => ['required', 'string'],
        ];

        if (! $this->isMethod('PUT')) {
            $rules['dokumentasi'] = ['required', 'mimes:pdf,jpg,jpeg,png,webp', 'max:2048'];
        } else {
            $rules['dokumentasi'] = ['nullable', 'mimes:pdf,jpg,jpeg,png,webp', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'namaKegiatan.required' => 'Nama kegiatan wajib diisi.',
            'namaKegiatan.string' => 'Nama kegiatan harus berupa teks.',

            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Tanggal harus berupa tanggal yang valid.',
            'tanggal.after_or_equal' => 'Tanggal hanya boleh dalam rentang 7 hari (termasuk hari ini).',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',

            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',

            'dokumentasi.required' => 'Dokumentasi wajib diunggah.',
            'dokumentasi.mimes' => 'Dokumentasi harus berformat: pdf, jpg, jpeg, png, atau webp.',
            'dokumentasi.max' => 'Ukuran dokumentasi tidak boleh lebih dari 2 MB.',
        ];
    }
}
