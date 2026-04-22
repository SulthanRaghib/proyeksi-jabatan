<?php

namespace App\Http\Requests;

use App\Models\Golongan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GolonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Golongan|null $golongan */
        $golongan = $this->route('golongan');

        return [
            'nama_golongan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('golongans', 'nama_golongan')->ignore($golongan?->id),
            ],
            'pangkat' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_golongan' => trim((string) $this->input('nama_golongan')),
            'pangkat' => trim((string) $this->input('pangkat')),
        ]);
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
