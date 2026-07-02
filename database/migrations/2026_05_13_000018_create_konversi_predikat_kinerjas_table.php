<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the konversi_predikat_kinerjas table.
     *
     * This table stores the official AK (Angka Kredit) conversion values for
     * each Jabatan Fungsional × Predikat Kinerja combination, as defined in
     * Permen PANRB No. 1 Tahun 2023.
     *
     * Each row represents: "For jabatan X with predikat Y, the annual AK value is Z"
     *
     * Formula: nilai_ak = koefisien_tahunan × (persentase / 100)
     * Example: Ahli Pertama (koef 12.5) × Sangat Baik (150%) = 18.75
     */
    public function up(): void
    {
        Schema::create('konversi_predikat_kinerjas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('jabatan_id')->constrained()->cascadeOnDelete();
            $table->enum('predikat', [
                'sangat_baik',
                'baik',
                'butuh_perbaikan',
                'kurang',
                'sangat_kurang',
            ]);
            $table->decimal('persentase', 5, 2)->comment('Percentage of koefisien (e.g. 150.00, 100.00)');
            $table->decimal('nilai_ak', 8, 3)->comment('Resulting annual AK value');
            $table->timestamps();

            $table->unique(['jabatan_id', 'predikat'], 'konversi_jabatan_predikat_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi_predikat_kinerjas');
    }
};
