<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->enum('jenjang', ['Pertama', 'Muda', 'Madya', 'Utama']);
            $table->decimal('koefisien_tahunan', 8, 2);
            $table->unsignedInteger('target_ak_kenaikan_pangkat');
            $table->unsignedInteger('target_ak_kenaikan_jenjang');
            $table->timestamps();

            $table->unique(['nama_jabatan', 'jenjang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};
