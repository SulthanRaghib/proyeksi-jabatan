<?php

namespace App\Http\Requests;

use App\Models\KonversiPredikatKinerja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'ak_tambahan' => $this->input('ak_tambahan'),
        ]);
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'no_pak' => ['required', 'string', 'max:255'],
            'tanggal_pak' => ['required', 'date'],
            'ak_tambahan' => ['required', 'numeric', 'min:0', 'max:99999.999'],
            'predikat_kinerja' => ['nullable', Rule::in(KonversiPredikatKinerja::PREDIKAT_OPTIONS)],
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
            'ak_tambahan.required' => 'AK tambahan wajib diisi.',
            'ak_tambahan.numeric' => 'AK tambahan harus berupa angka.',
            'ak_tambahan.min' => 'AK tambahan tidak boleh kurang dari 0.',
            'ak_tambahan.max' => 'AK tambahan melebihi batas yang diizinkan.',
            'predikat_kinerja.in' => 'Predikat kinerja yang dipilih tidak valid.',
        ];
    }
}
