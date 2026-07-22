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
        Schema::table('usulan_kenaikan_pangkats', function (Blueprint $table) {
            $table->string('no_sertifikat_ukom')->nullable()->after('tmt_golongan_baru');
            $table->date('tgl_lulus_ukom')->nullable()->after('no_sertifikat_ukom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usulan_kenaikan_pangkats', function (Blueprint $table) {
            $table->dropColumn(['no_sertifikat_ukom', 'tgl_lulus_ukom']);
        });
    }
};
