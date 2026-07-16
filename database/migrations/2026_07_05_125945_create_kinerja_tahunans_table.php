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
        Schema::create('kinerja_tahunans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained()->cascadeOnDelete();
            // Link directly with unique constraint on pak_id (allowing multiple triwulanan records per year)
            $table->foreignId('pak_id')->nullable()->unique()->constrained('riwayat_paks')->nullOnDelete();
            $table->integer('tahun');
            $table->string('predikat', 50);
            $table->decimal('koefisien_saat_itu', 8, 3)->nullable();
            $table->decimal('ak_didapat', 8, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kinerja_tahunans');
    }
};
