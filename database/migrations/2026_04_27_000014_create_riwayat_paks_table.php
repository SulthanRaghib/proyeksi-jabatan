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
        Schema::create('riwayat_paks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pegawai_id')->constrained()->cascadeOnDelete();
            $table->string('no_pak');
            $table->date('tanggal_pak');
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            $table->decimal('ak_total', 8, 3);
            $table->decimal('ak_tambahan', 8, 3)->default(0);
            $table->boolean('is_konversi_baru')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_paks');
    }
};
