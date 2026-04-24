<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nip' => trim((string) $this->input('nip')),
            'nama_lengkap' => trim((string) $this->input('nama_lengkap')),
            'tmt_jabatan' => $this->input('tmt_jabatan'),
            'tmt_golongan' => $this->input('tmt_golongan'),
            'user_id' => $this->input('user_id') ?: null,
            'unit_kerja_id' => $this->input('unit_kerja_id'),
            'jabatan_id' => $this->input('jabatan_id'),
            'golongan_id' => $this->input('golongan_id'),
            'status_ukom' => (bool) $this->input('status_ukom', false),
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'unit_kerja_id' => ['required', 'exists:unit_kerjas,id'],
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'golongan_id' => ['required', 'exists:golongans,id'],
            'nip' => ['required', 'string', 'unique:pegawais,nip', 'max:255'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tmt_jabatan' => ['required', 'date'],
            'tmt_golongan' => ['required', 'date'],
            'status_ukom' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'User yang dipilih tidak valid.',
            'unit_kerja_id.required' => 'Unit kerja wajib dipilih.',
            'unit_kerja_id.exists' => 'Unit kerja yang dipilih tidak valid.',
            'jabatan_id.required' => 'Jabatan wajib dipilih.',
            'jabatan_id.exists' => 'Jabatan yang dipilih tidak valid.',
            'golongan_id.required' => 'Golongan wajib dipilih.',
            'golongan_id.exists' => 'Golongan yang dipilih tidak valid.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'nip.max' => 'NIP maksimal 255 karakter.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',
            'tmt_jabatan.required' => 'Tanggal TMT Jabatan wajib diisi.',
            'tmt_jabatan.date' => 'Format tanggal TMT Jabatan tidak valid.',
            'tmt_golongan.required' => 'Tanggal TMT Golongan wajib diisi.',
            'tmt_golongan.date' => 'Format tanggal TMT Golongan tidak valid.',
        ];
    }
}
