<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add predikat_kinerja column to riwayat_paks table.
     *
     * Tracks which performance predicate was associated with each AK record.
     * This enables:
     * - Historical audit trail of performance ratings
     * - Auto-calculation of AK tambahan based on predikat + jabatan konversi table
     * - More accurate projection analytics
     *
     * Column is nullable to maintain backward compatibility with existing records.
     */
    public function up(): void
    {
        Schema::table('riwayat_paks', function (Blueprint $table): void {
            $table->enum('predikat_kinerja', [
                'sangat_baik',
                'baik',
                'butuh_perbaikan',
                'kurang',
                'sangat_kurang',
            ])->nullable()->after('ak_tambahan');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_paks', function (Blueprint $table): void {
            $table->dropColumn('predikat_kinerja');
        });
    }
};
