<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsulanPangkatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'golongan_baru_id' => 'required|exists:golongans,id',
            'saldo_ak_awal' => 'required|numeric',
            'potongan_ak' => 'required|numeric',
            'sisa_ak' => 'required|numeric',
            'is_lintas_jenjang' => 'required|boolean',
            'action_type' => 'required|in:draft,submit',
            'sk_pangkat' => 'nullable|file|mimes:pdf|max:5120',
            'sk_jabatan' => 'nullable|file|mimes:pdf|max:5120',
            'pak_konversi' => 'nullable|file|mimes:pdf|max:5120',
            'skp' => 'nullable|file|mimes:pdf|max:5120',
            'ukom' => 'nullable|file|mimes:pdf|max:5120',
            'formasi' => 'nullable|file|mimes:pdf|max:5120',
        ];
    }
}
