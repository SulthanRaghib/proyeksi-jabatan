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
        Schema::create('dokumen_usulans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usulan_kenaikan_pangkat_id')->constrained()->cascadeOnDelete();
            
            // e.g., 'sk_pangkat', 'sk_jabatan', 'pak_konversi', 'skp', 'ukom', 'formasi'
            $table->string('jenis_dokumen');
            
            $table->string('file_path');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_usulans');
    }
};
