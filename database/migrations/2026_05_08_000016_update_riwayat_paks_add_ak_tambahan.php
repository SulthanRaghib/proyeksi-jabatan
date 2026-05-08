<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds ak_tambahan column to track the delta AK for each record.
     * Removes is_latest column — latest record is now auto-detected by tanggal_pak DESC.
     * Backfills ak_tambahan for existing records by calculating difference from previous record.
     */
    public function up(): void
    {
        Schema::table('riwayat_paks', function (Blueprint $table): void {
            $table->decimal('ak_tambahan', 8, 3)->default(0)->after('ak_total');
        });

        // Backfill ak_tambahan for existing data
        $pegawaiIds = DB::table('riwayat_paks')->distinct()->pluck('pegawai_id');

        foreach ($pegawaiIds as $pegawaiId) {
            $records = DB::table('riwayat_paks')
                ->where('pegawai_id', $pegawaiId)
                ->orderBy('tanggal_pak', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $previousAk = 0.0;
            foreach ($records as $record) {
                $tambahan = max(0, (float) $record->ak_total - $previousAk);
                DB::table('riwayat_paks')
                    ->where('id', $record->id)
                    ->update(['ak_tambahan' => $tambahan]);
                $previousAk = (float) $record->ak_total;
            }
        }

        Schema::table('riwayat_paks', function (Blueprint $table): void {
            $table->dropColumn('is_latest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_paks', function (Blueprint $table): void {
            $table->boolean('is_latest')->default(false)->after('ak_total');
        });

        // Restore is_latest flags — mark the latest record per pegawai
        $pegawaiIds = DB::table('riwayat_paks')->distinct()->pluck('pegawai_id');

        foreach ($pegawaiIds as $pegawaiId) {
            $latestId = DB::table('riwayat_paks')
                ->where('pegawai_id', $pegawaiId)
                ->orderByDesc('tanggal_pak')
                ->orderByDesc('id')
                ->value('id');

            if ($latestId) {
                DB::table('riwayat_paks')
                    ->where('id', $latestId)
                    ->update(['is_latest' => true]);
            }
        }

        Schema::table('riwayat_paks', function (Blueprint $table): void {
            $table->dropColumn('ak_tambahan');
        });
    }
};
