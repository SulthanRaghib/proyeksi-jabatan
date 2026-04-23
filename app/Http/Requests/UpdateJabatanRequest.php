<?php

namespace App\Http\Requests;

use App\Models\Jabatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_jabatan' => trim((string) $this->input('nama_jabatan')),
            'jenjang' => trim((string) $this->input('jenjang')),
            'koefisien_tahunan' => $this->input('koefisien_tahunan'),
            'target_ak_kenaikan_pangkat' => $this->input('target_ak_kenaikan_pangkat'),
            'target_ak_kenaikan_jenjang' => $this->input('target_ak_kenaikan_jenjang'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_jabatan' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', Rule::in(Jabatan::JENJANG_OPTIONS)],
            'koefisien_tahunan' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'target_ak_kenaikan_pangkat' => ['required', 'integer', 'min:0'],
            'target_ak_kenaikan_jenjang' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Jabatan|null $jabatan */
            $jabatan = $this->route('jabatan');

            $exists = Jabatan::query()
                ->where('nama_jabatan', $this->input('nama_jabatan'))
                ->where('jenjang', $this->input('jenjang'))
                ->when($jabatan, fn($query) => $query->whereKeyNot($jabatan->id))
                ->exists();

            if ($exists) {
                $validator->errors()->add('jenjang', 'Kombinasi nama jabatan dan jenjang sudah terdaftar.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.max' => 'Nama jabatan maksimal 255 karakter.',
            'jenjang.required' => 'Jenjang wajib dipilih.',
            'jenjang.in' => 'Jenjang tidak valid.',
            'koefisien_tahunan.required' => 'Koefisien tahunan wajib diisi.',
            'koefisien_tahunan.numeric' => 'Koefisien tahunan harus berupa angka.',
            'koefisien_tahunan.min' => 'Koefisien tahunan tidak boleh kurang dari 0.',
            'koefisien_tahunan.max' => 'Koefisien tahunan melebihi batas yang diizinkan.',
            'target_ak_kenaikan_pangkat.required' => 'Target AK kenaikan pangkat wajib diisi.',
            'target_ak_kenaikan_pangkat.integer' => 'Target AK kenaikan pangkat harus berupa bilangan bulat.',
            'target_ak_kenaikan_pangkat.min' => 'Target AK kenaikan pangkat tidak boleh kurang dari 0.',
            'target_ak_kenaikan_jenjang.required' => 'Target AK kenaikan jenjang wajib diisi.',
            'target_ak_kenaikan_jenjang.integer' => 'Target AK kenaikan jenjang harus berupa bilangan bulat.',
            'target_ak_kenaikan_jenjang.min' => 'Target AK kenaikan jenjang tidak boleh kurang dari 0.',
        ];
    }
}
