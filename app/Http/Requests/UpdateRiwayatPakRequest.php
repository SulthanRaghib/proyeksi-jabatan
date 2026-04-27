<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRiwayatPakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'pegawai_id' => $this->input('pegawai_id'),
            'no_pak' => trim((string) $this->input('no_pak')),
            'tanggal_pak' => $this->input('tanggal_pak'),
            'ak_total' => $this->input('ak_total'),
            'is_latest' => $this->boolean('is_latest'),
        ]);
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'no_pak' => ['required', 'string', 'max:255'],
            'tanggal_pak' => ['required', 'date'],
            'ak_total' => ['required', 'numeric', 'min:0', 'max:99999.999'],
            'is_latest' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'pegawai_id.required' => 'Pegawai wajib dipilih.',
            'pegawai_id.exists' => 'Pegawai yang dipilih tidak valid.',
            'no_pak.required' => 'Nomor PAK wajib diisi.',
            'no_pak.max' => 'Nomor PAK maksimal 255 karakter.',
            'tanggal_pak.required' => 'Tanggal PAK wajib diisi.',
            'tanggal_pak.date' => 'Format tanggal PAK tidak valid.',
            'ak_total.required' => 'AK total wajib diisi.',
            'ak_total.numeric' => 'AK total harus berupa angka.',
            'ak_total.min' => 'AK total tidak boleh kurang dari 0.',
            'ak_total.max' => 'AK total melebihi batas yang diizinkan.',
            'is_latest.boolean' => 'Status data terbaru tidak valid.',
        ];
    }
}
