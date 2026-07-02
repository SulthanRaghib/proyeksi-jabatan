<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Expand the jenjang enum on jabatans table to include Keterampilan values.
     *
     * Original values: Pertama, Muda, Madya, Utama (Keahlian only)
     * New values: + Pemula, Terampil, Mahir, Penyelia (Keterampilan)
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE jabatans MODIFY COLUMN jenjang ENUM('Pertama', 'Muda', 'Madya', 'Utama', 'Pemula', 'Terampil', 'Mahir', 'Penyelia') NOT NULL");
    }

    public function down(): void
    {
        // Remove Keterampilan rows first to avoid constraint violation
        DB::table('jabatans')
            ->whereIn('jenjang', ['Pemula', 'Terampil', 'Mahir', 'Penyelia'])
            ->delete();

        DB::statement("ALTER TABLE jabatans MODIFY COLUMN jenjang ENUM('Pertama', 'Muda', 'Madya', 'Utama') NOT NULL");
    }
};
