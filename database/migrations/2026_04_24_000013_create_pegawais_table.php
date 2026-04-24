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
        Schema::create('pegawais', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('unit_kerja_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('golongan_id')->constrained()->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->string('nama_lengkap');
            $table->date('tmt_jabatan');
            $table->date('tmt_golongan');
            $table->boolean('status_ukom')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
