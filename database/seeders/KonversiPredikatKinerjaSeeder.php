<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\KonversiPredikatKinerja;
use Illuminate\Database\Seeder;

/**
 * Seeds the konversi predikat kinerja table with official values
 * from Permen PANRB No. 1 Tahun 2023 — Tabel B.
 *
 * For each jabatan, creates 5 rows (one per predikat) with the
 * calculated AK values based on koefisien_tahunan × persentase.
 */
class KonversiPredikatKinerjaSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = Jabatan::all();

        if ($jabatans->isEmpty()) {
            $this->command?->warn('No jabatan found. Run JabatanSeeder first.');
            return;
        }

        $rows = [];
        $now = now();

        foreach ($jabatans as $jabatan) {
            $koefisien = (float) $jabatan->koefisien_tahunan;

            foreach (KonversiPredikatKinerja::PREDIKAT_PERSENTASE as $predikat => $persentase) {
                $nilaiAk = KonversiPredikatKinerja::calculateNilaiAk($koefisien, $persentase);

                $rows[] = [
                    'jabatan_id' => $jabatan->id,
                    'predikat' => $predikat,
                    'persentase' => $persentase,
                    'nilai_ak' => $nilaiAk,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Upsert to avoid duplicates on re-seed
        foreach ($rows as $row) {
            KonversiPredikatKinerja::query()->updateOrCreate(
                [
                    'jabatan_id' => $row['jabatan_id'],
                    'predikat' => $row['predikat'],
                ],
                [
                    'persentase' => $row['persentase'],
                    'nilai_ak' => $row['nilai_ak'],
                ]
            );
        }

        $this->command?->info('Seeded ' . count($rows) . ' konversi predikat kinerja records.');
    }
}
