<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\KonversiPredikatKinerja;

class UpdateKinerjaTahunanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun' => 'required|integer|min:2000|max:2099',
            'predikat' => 'required|string|in:' . implode(',', KonversiPredikatKinerja::PREDIKAT_OPTIONS),
        ];
    }
}
