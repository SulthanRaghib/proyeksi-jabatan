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
        $isSubmit = $this->input('action_type') === 'submit';
        $isLintasJenjang = (bool) $this->input('is_lintas_jenjang');

        $baseFileRule = $isSubmit ? 'required|file|mimes:pdf|max:5120' : 'nullable|file|mimes:pdf|max:5120';
        $lintasJenjangRule = ($isSubmit && $isLintasJenjang) ? 'required|file|mimes:pdf|max:5120' : 'nullable|file|mimes:pdf|max:5120';

        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'golongan_baru_id' => 'required|exists:golongans,id',
            'saldo_ak_awal' => 'required|numeric',
            'potongan_ak' => 'required|numeric',
            'sisa_ak' => 'required|numeric',
            'is_lintas_jenjang' => 'required|boolean',
            'action_type' => 'required|in:draft,submit',
            'sk_pangkat' => $baseFileRule,
            'sk_jabatan' => $baseFileRule,
            'pak_konversi' => $baseFileRule,
            'skp' => $baseFileRule,
            'ukom' => $lintasJenjangRule,
            'formasi' => $lintasJenjangRule,
        ];
    }
}
