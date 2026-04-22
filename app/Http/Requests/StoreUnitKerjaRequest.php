<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_unit' => trim((string) $this->input('nama_unit')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_unit' => ['required', 'string', 'max:255', 'unique:unit_kerjas,nama_unit'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_unit.required' => 'Nama unit wajib diisi.',
            'nama_unit.unique' => 'Nama unit sudah terdaftar.',
            'nama_unit.max' => 'Nama unit maksimal 255 karakter.',
        ];
    }
}
