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
        Schema::table('pegawais', function (Blueprint $table) {
            $table->boolean('sedang_hukuman_disiplin')->default(false)->after('unit_kerja_id');
            $table->boolean('is_locked_usulan')->default(false)->after('sedang_hukuman_disiplin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['sedang_hukuman_disiplin', 'is_locked_usulan']);
        });
    }
};
