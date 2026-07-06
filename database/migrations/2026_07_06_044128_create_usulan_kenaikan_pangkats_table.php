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
        Schema::create('usulan_kenaikan_pangkats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained()->cascadeOnDelete();
            $table->foreignId('golongan_lama_id')->constrained('golongans');
            $table->foreignId('golongan_baru_id')->constrained('golongans');
            
            // draft, sedang_diproses, selesai, ditolak
            $table->string('status')->default('draft');
            
            // Financials of the AK
            $table->decimal('saldo_ak_awal', 8, 3);
            $table->decimal('potongan_ak', 8, 3);
            $table->decimal('sisa_ak', 8, 3);
            
            // Flags
            $table->boolean('is_lintas_jenjang')->default(false);
            
            // Resolution fields (when approved by BKN)
            $table->string('nomor_sk_baru')->nullable();
            $table->date('tmt_golongan_baru')->nullable();
            $table->text('keterangan_admin')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan_kenaikan_pangkats');
    }
};
