<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Dapatkan pegawai_id dari PAK yang terpengaruh
        $affectedPegawaiIds = \Illuminate\Support\Facades\DB::table('riwayat_paks')
            ->where('no_pak', 'like', 'DASAR-KJ-%')
            ->pluck('pegawai_id')
            ->unique();

        // 2. Hapus data PAK DASAR-KJ-
        \Illuminate\Support\Facades\DB::table('riwayat_paks')
            ->where('no_pak', 'like', 'DASAR-KJ-%')
            ->delete();

        // 3. Hitung ulang running total ak_total secara kronologis untuk pegawai yang terdampak
        foreach ($affectedPegawaiIds as $pegawaiId) {
            $records = \Illuminate\Support\Facades\DB::table('riwayat_paks')
                ->where('pegawai_id', $pegawaiId)
                ->orderBy('tanggal_pak')
                ->orderBy('id')
                ->get();

            $runningTotal = 0.0;
            foreach ($records as $record) {
                $runningTotal += (float) $record->ak_tambahan;
                \Illuminate\Support\Facades\DB::table('riwayat_paks')
                    ->where('id', $record->id)
                    ->update(['ak_total' => $runningTotal]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data fixes
    }
};
