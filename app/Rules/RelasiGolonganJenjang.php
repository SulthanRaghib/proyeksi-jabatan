<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Golongan;
use App\Models\Jabatan;

class RelasiGolonganJenjang implements ValidationRule
{
    protected $golonganId;

    public function __construct($golonganId)
    {
        $this->golonganId = $golonganId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $jabatanId = $value;
        $golonganId = $this->golonganId;

        if (!$golonganId || !$jabatanId) {
            return;
        }

        $golongan = Golongan::find($golonganId);
        $jabatan = Jabatan::find($jabatanId);

        if ($golongan && $jabatan) {
            $pangkat = $golongan->pangkat;
            $expectedJenjang = null;

            if (in_array($pangkat, ['III/a', 'III/b'])) {
                $expectedJenjang = 'Pertama';
            } elseif (in_array($pangkat, ['III/c', 'III/d'])) {
                $expectedJenjang = 'Muda';
            } elseif (in_array($pangkat, ['IV/a', 'IV/b', 'IV/c'])) {
                $expectedJenjang = 'Madya';
            } elseif (in_array($pangkat, ['IV/d', 'IV/e'])) {
                $expectedJenjang = 'Utama';
            } elseif (in_array($pangkat, ['II/a'])) {
                $expectedJenjang = 'Pemula';
            } elseif (in_array($pangkat, ['II/b', 'II/c', 'II/d'])) {
                $expectedJenjang = 'Terampil';
            }

            if ($expectedJenjang && $jabatan->jenjang !== $expectedJenjang) {
                $fail("Kombinasi tidak valid: Pegawai dengan Golongan {$pangkat} harus berada di Jenjang {$expectedJenjang}.");
            }
        }
    }
}
