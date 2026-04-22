<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGolonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_golongan' => trim((string) $this->input('nama_golongan')),
            'pangkat' => trim((string) $this->input('pangkat')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_golongan' => ['required', 'string', 'max:255', 'unique:golongans,nama_golongan'],
            'pangkat' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_golongan.required' => 'Nama golongan wajib diisi.',
            'nama_golongan.unique' => 'Nama golongan sudah terdaftar.',
            'nama_golongan.max' => 'Nama golongan maksimal 255 karakter.',
            'pangkat.required' => 'Pangkat wajib diisi.',
            'pangkat.max' => 'Pangkat maksimal 255 karakter.',
        ];
    }
}
