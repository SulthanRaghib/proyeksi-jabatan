<?php

namespace App\Services;

use App\Models\KonversiPredikatKinerja;
use App\Models\Jabatan;

class KonversiPredikatService
{
    /**
     * Batch generate konversi for a specific jabatan.
     * Generates all 5 predikat entries based on koefisien_tahunan × persentase.
     *
     * @param int $jabatanId
     * @return array
     */
    public function generateKonversiForJabatan(int $jabatanId): array
    {
        $jabatan = Jabatan::findOrFail($jabatanId);
        $koefisien = (float) $jabatan->koefisien_tahunan;
        $created = 0;

        foreach (KonversiPredikatKinerja::PREDIKAT_PERSENTASE as $predikat => $persentase) {
            $nilaiAk = KonversiPredikatKinerja::calculateNilaiAk($koefisien, $persentase);

            KonversiPredikatKinerja::updateOrCreate(
                [
                    'jabatan_id' => $jabatan->id,
                    'predikat' => $predikat,
                ],
                [
                    'persentase' => $persentase,
                    'nilai_ak' => $nilaiAk,
                ]
            );
            $created++;
        }

        return [
            'jabatan_nama' => $jabatan->nama_jabatan,
            'jabatan_jenjang' => $jabatan->jenjang,
            'created_count' => $created,
        ];
    }
}
