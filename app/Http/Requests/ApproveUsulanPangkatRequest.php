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
        return [
            'nomor_sk_baru' => 'required|string|max:255',
            'tmt_golongan_baru' => 'required|date',
        ];
    }
}
