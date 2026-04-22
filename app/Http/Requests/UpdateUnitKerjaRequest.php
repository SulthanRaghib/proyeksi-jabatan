<?php

namespace App\Http\Requests;

use App\Models\UnitKerja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitKerjaRequest extends FormRequest
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
        /** @var UnitKerja|null $unitKerja */
        $unitKerja = $this->route('unit_kerja');

        return [
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                Rule::unique('unit_kerjas', 'nama_unit')->ignore($unitKerja?->id),
            ],
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
