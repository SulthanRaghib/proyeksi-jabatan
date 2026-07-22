<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveUsulanPangkatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usulan = $this->route('usulan');
        $isLintasJenjang = $usulan ? (bool)$usulan->is_lintas_jenjang : false;

        $rules = [
            'nomor_sk_baru' => 'required|string|max:255',
            'tmt_golongan_baru' => 'required|date',
        ];

        if ($isLintasJenjang) {
            $rules['no_sertifikat_ukom'] = 'required|string|max:255';
            $rules['tgl_lulus_ukom'] = 'required|date';
        }

        return $rules;
    }
}
