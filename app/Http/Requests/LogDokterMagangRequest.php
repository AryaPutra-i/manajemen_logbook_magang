<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LogDokterMagangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return request()->user()->role === 'dokter_magang';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'kategoriPelayananKesehatan' => ['required', 'array', 'min:1'],
            'kategoriPelayananKesehatan.*' => ['required', 'string', 'distinct'],
            'jumlahPasien' => ['required', 'array', 'min:1'],
            'jumlahPasien.*' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date', Rule::date()->afterOrEqual(today()->subDays(6)), 'before_or_equal:today'],
            'deskripsi' => ['required', 'string'],
            'hambatan' => ['required', 'string'],
        ];

        if (! $this->isMethod('PUT')) {
            $rules['dokumentasi'] = ['required', 'mimes:pdf,jpg,jpeg,png,webp', 'max:2048'];
        } else {
            $rules['dokumentasi'] = ['nullable', 'mimes:pdf,jpg,jpeg,png,webp', 'max:2048'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (count($this->input('kategoriPelayananKesehatan', [])) !== count($this->input('jumlahPasien', []))) {
                $validator->errors()->add('jumlahPasien', 'Jumlah kategori dan jumlah pasien harus sama banyak.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'kategoriPelayananKesehatan.required' => 'Kategori pelayanan kesehatan wajib diisi.',
            'kategoriPelayananKesehatan.array' => 'Kategori pelayanan kesehatan harus berupa array.',
            'kategoriPelayananKesehatan.*.required' => 'Setiap kategori pelayanan kesehatan wajib diisi.',
            'kategoriPelayananKesehatan.*.string' => 'Setiap kategori pelayanan kesehatan harus berupa teks.',

            'jumlahPasien.required' => 'Jumlah pasien wajib diisi.',
            'jumlahPasien.array' => 'Jumlah pasien harus berupa array.',
            'jumlahPasien.*.required' => 'Setiap jumlah pasien wajib diisi.',
            'jumlahPasien.*.numeric' => 'Setiap jumlah pasien harus berupa angka.',

            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Tanggal harus berupa tanggal yang valid.',
            'tanggal.after_or_equal' => 'Tanggal hanya boleh dalam rentang 7 hari (termasuk hari ini).',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',

            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',

            'hambatan.required' => 'Hambatan wajib diisi.',
            'hambatan.string' => 'Hambatan harus berupa teks.',

            'dokumentasi.required' => 'Dokumentasi wajib diunggah.',
            'dokumentasi.mimes' => 'Dokumentasi harus berformat: pdf, jpg, jpeg, png, atau webp.',
            'dokumentasi.max' => 'Ukuran dokumentasi tidak boleh lebih dari 2 MB.',
        ];
    }
}
