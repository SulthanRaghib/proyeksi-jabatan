<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsulanPangkatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usulan = $this->route('usulan');
        $isLintasJenjang = $usulan ? (bool) $usulan->is_lintas_jenjang : false;

        $baseFileRule = 'required|file|mimes:pdf|max:5120';
        $lintasJenjangRule = $isLintasJenjang ? 'required|file|mimes:pdf|max:5120' : 'nullable|file|mimes:pdf|max:5120';

        return [
            'sk_pangkat' => $baseFileRule,
            'sk_jabatan' => $baseFileRule,
            'pak_konversi' => $baseFileRule,
            'skp' => $baseFileRule,
            'ukom' => $lintasJenjangRule,
            'formasi' => $lintasJenjangRule,
        ];
    }
}
