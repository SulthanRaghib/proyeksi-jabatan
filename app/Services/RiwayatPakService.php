<?php

namespace App\Services;

use App\Models\RiwayatPak;
use Illuminate\Support\Facades\DB;
use Exception;

class RiwayatPakService
{
    /**
     * Generate a sequential PAK number for a given year.
     *
     * @param string|null $year
     * @return string
     */
    public function generateNoPak(?string $year = null): string
    {
        $year = $year ?? date('Y');

        $latestPak = RiwayatPak::where('no_pak', 'like', "%/KEP/4028/SK/PAK/$year")
            ->orderByRaw('CAST(SUBSTRING_INDEX(no_pak, "/", 1) AS UNSIGNED) DESC')
            ->first();

        $nextNumber = 1;
        if ($latestPak && preg_match('/^(\d+)\/KEP\/4028\/SK\/PAK\/\d{4}$/', $latestPak->no_pak, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return sprintf("%d/KEP/4028/SK/PAK/%s", $nextNumber, $year);
    }
    /**
     * Store a new Riwayat PAK and recalculate subsequent records.
     *
     * @param array $data
     * @return RiwayatPak
     */
    public function storeRiwayatPak(array $data): RiwayatPak
    {
        return DB::transaction(function () use ($data) {
            // Set a temporary total (will be immediately overwritten by recalculation)
            $data['ak_total'] = $data['ak_tambahan'];
            
            $kinerjaTahunanId = $data['kinerja_tahunan_id'] ?? null;
            $predikat = $data['predikat_kinerja'] ?? null;
            
            // Unset unused form fields
            unset($data['predikat_kinerja']);
            unset($data['kinerja_tahunan_id']);

            $riwayatPak = RiwayatPak::create($data);
            
            $isKonversi = $riwayatPak->is_konversi_baru ?? true;

            // If a specific Kinerja Tahunan was selected, link it
            if ($kinerjaTahunanId && $isKonversi) {
                $kinerja = \App\Models\KinerjaTahunan::find($kinerjaTahunanId);
                if ($kinerja) {
                    $kinerja->update([
                        'pak_id' => $riwayatPak->id,
                        'ak_didapat' => $riwayatPak->ak_tambahan,
                    ]);
                }
            } 
            // Else, if it's a manual input (Case 1) and has a predikat, create KinerjaTahunan
            elseif ($predikat && $isKonversi) {
                $tahun = \Carbon\Carbon::parse($riwayatPak->tanggal_pak)->subYear()->year;
                
                if (!empty($riwayatPak->periode_akhir)) {
                    $tahun = \Carbon\Carbon::parse($riwayatPak->periode_akhir)->year;
                }
                
                $koefisien = $riwayatPak->pegawai->jabatan->koefisien_tahunan ?? null;

                \App\Models\KinerjaTahunan::updateOrCreate([
                        'pegawai_id' => $riwayatPak->pegawai_id,
                        'tahun' => $tahun,
                    ],
                    [
                        'pak_id' => $riwayatPak->id,
                        'predikat' => $predikat,
                        'koefisien_saat_itu' => $koefisien,
                        'ak_didapat' => $riwayatPak->ak_tambahan,
                    ]
                );
            }

            // Recalculate all records sequentially to fix out-of-order inserts
            $this->recalculateSubsequentRecords($riwayatPak);
            
            return $riwayatPak;
        });
    }

    /**
     * Update an existing Riwayat PAK and recalculate subsequent records.
     *
     * @param RiwayatPak $riwayatPak
     * @param array $data
     * @return RiwayatPak
     */
    public function updateRiwayatPak(RiwayatPak $riwayatPak, array $data): RiwayatPak
    {
        return DB::transaction(function () use ($riwayatPak, $data) {
            // Set a temporary total (will be immediately overwritten by recalculation)
            $data['ak_total'] = $data['ak_tambahan'];
            
            $kinerjaTahunanId = $data['kinerja_tahunan_id'] ?? null;
            $predikat = $data['predikat_kinerja'] ?? null;
            
            unset($data['predikat_kinerja']);
            unset($data['kinerja_tahunan_id']);

            $riwayatPak->update($data);

            $isKonversi = $riwayatPak->is_konversi_baru ?? true;

            // Unlink any associated Kinerja if updated to conventional
            if (!$isKonversi) {
                \App\Models\KinerjaTahunan::where('pak_id', $riwayatPak->id)
                    ->update(['pak_id' => null]);
            }

            // Case 2: Link explicit Kinerja
            if ($kinerjaTahunanId && $isKonversi) {
                // First, unlink any currently linked Kinerja
                \App\Models\KinerjaTahunan::where('pak_id', $riwayatPak->id)
                    ->where('id', '!=', $kinerjaTahunanId)
                    ->update(['pak_id' => null]);
                    
                $kinerja = \App\Models\KinerjaTahunan::find($kinerjaTahunanId);
                if ($kinerja) {
                    $kinerja->update([
                        'pak_id' => $riwayatPak->id,
                        'ak_didapat' => $riwayatPak->ak_tambahan,
                    ]);
                }
            } 
            // Case 1: Manual predikat input
            elseif ($predikat && $isKonversi) {
                $tahun = \Carbon\Carbon::parse($riwayatPak->tanggal_pak)->subYear()->year;
                
                if (!empty($riwayatPak->periode_akhir)) {
                    $tahun = \Carbon\Carbon::parse($riwayatPak->periode_akhir)->year;
                }
                
                $koefisien = $riwayatPak->pegawai->jabatan->koefisien_tahunan ?? null;

                \App\Models\KinerjaTahunan::updateOrCreate([
                        'pegawai_id' => $riwayatPak->pegawai_id,
                        'tahun' => $tahun,
                    ],
                    [
                        'pak_id' => $riwayatPak->id,
                        'predikat' => $predikat,
                        'koefisien_saat_itu' => $koefisien,
                        'ak_didapat' => $riwayatPak->ak_tambahan,
                    ]
                );
            }

            // Recalculate all records sequentially to ensure chronological correctness
            $this->recalculateSubsequentRecords($riwayatPak);
            
            return $riwayatPak;
        });
    }

    /**
     * Delete a Riwayat PAK and recalculate subsequent records.
     *
     * @param RiwayatPak $riwayatPak
     * @return void
     */
    public function deleteRiwayatPak(RiwayatPak $riwayatPak): void
    {
        DB::transaction(function () use ($riwayatPak) {
            $pegawaiId = $riwayatPak->pegawai_id;

            $riwayatPak->delete();

            // Recalculate all records after the deleted one
            $subsequentRecords = RiwayatPak::query()
                ->where('pegawai_id', $pegawaiId)
                ->orderBy('tanggal_pak')
                ->orderBy('id')
                ->get();

            $runningTotal = 0;
            foreach ($subsequentRecords as $record) {
                $runningTotal += (float) $record->ak_tambahan;
                if (round((float) $record->ak_total, 3) !== round($runningTotal, 3)) {
                    $record->updateQuietly(['ak_total' => $runningTotal]);
                }
            }
        });
    }

    /**
     * Recalculate ak_total for all records after the given record (for edit scenarios).
     *
     * @param RiwayatPak $fromRecord
     * @return void
     */
    private function recalculateSubsequentRecords(RiwayatPak $fromRecord): void
    {
        $allRecords = RiwayatPak::query()
            ->where('pegawai_id', $fromRecord->pegawai_id)
            ->orderBy('tanggal_pak')
            ->orderBy('id')
            ->get();

        $runningTotal = 0;
        foreach ($allRecords as $record) {
            $runningTotal += (float) $record->ak_tambahan;
            if (round((float) $record->ak_total, 3) !== round($runningTotal, 3)) {
                $record->updateQuietly(['ak_total' => $runningTotal]);
            }
        }
    }
}
