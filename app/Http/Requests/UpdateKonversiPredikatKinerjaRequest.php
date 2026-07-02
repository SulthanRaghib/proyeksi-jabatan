<?php

namespace App\Http\Requests;

use App\Models\KonversiPredikatKinerja;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKonversiPredikatKinerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var KonversiPredikatKinerja $konversi */
        $konversi = $this->route('konversi_predikat');

        return [
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'predikat' => [
                'required',
                Rule::in(KonversiPredikatKinerja::PREDIKAT_OPTIONS),
                Rule::unique('konversi_predikat_kinerjas')
                    ->where('jabatan_id', $this->input('jabatan_id'))
                    ->ignore($konversi?->id),
            ],
            'persentase' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'nilai_ak' => ['required', 'numeric', 'min:0', 'max:99999.999'],
        ];
    }

    public function messages(): array
    {
        return [
            'predikat.unique' => 'Kombinasi jabatan dan predikat ini sudah ada.',
            'jabatan_id.required' => 'Jabatan wajib dipilih.',
            'jabatan_id.exists' => 'Jabatan yang dipilih tidak valid.',
            'predikat.required' => 'Predikat wajib dipilih.',
            'predikat.in' => 'Predikat yang dipilih tidak valid.',
            'persentase.required' => 'Persentase koefisien wajib diisi.',
            'persentase.numeric' => 'Persentase harus berupa angka.',
            'nilai_ak.required' => 'Nilai AK wajib diisi.',
            'nilai_ak.numeric' => 'Nilai AK harus berupa angka.',
        ];
    }
}
