<?php

namespace App\Services;

use App\Models\RiwayatPak;
use Illuminate\Support\Facades\DB;
use Exception;

class RiwayatPakService
{
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

            $riwayatPak = RiwayatPak::create($data);
            
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

            $riwayatPak->update($data);

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
