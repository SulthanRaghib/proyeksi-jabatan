<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 'kategori' column to jabatans table.
     *
     * Jabatan Fungsional (JF) is divided into two categories:
     * - keahlian: Ahli Pertama, Ahli Muda, Ahli Madya, Ahli Utama
     * - keterampilan: Pemula, Terampil, Mahir, Penyelia
     *
     * Each category has different AK coefficients and conversion tables.
     */
    public function up(): void
    {
        Schema::table('jabatans', function (Blueprint $table): void {
            $table->enum('kategori', ['keahlian', 'keterampilan'])
                ->default('keahlian')
                ->after('nama_jabatan');
        });

        // Backfill: existing jabatan with jenjang in keahlian set
        // (Pertama, Muda, Madya, Utama) → keahlian; others → keterampilan
        DB::table('jabatans')
            ->whereIn('jenjang', ['Pertama', 'Muda', 'Madya', 'Utama'])
            ->update(['kategori' => 'keahlian']);

        DB::table('jabatans')
            ->whereIn('jenjang', ['Pemula', 'Terampil', 'Mahir', 'Penyelia'])
            ->update(['kategori' => 'keterampilan']);
    }

    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table): void {
            $table->dropColumn('kategori');
        });
    }
};
