<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed master data for unit kerja and golongan.
     */
    public function run(): void
    {
        $unitKerjas = [
            ['nama_unit' => 'INSP'],
            ['nama_unit' => 'BHKK'],
            ['nama_unit' => 'BPIK'],
            ['nama_unit' => 'BOU'],
            ['nama_unit' => 'BDL'],
            ['nama_unit' => 'P2STPIBN'],
            ['nama_unit' => 'P2STPFRZR'],
            ['nama_unit' => 'DPIBN'],
            ['nama_unit' => 'DPFRZR'],
            ['nama_unit' => 'DP2IBN'],
            ['nama_unit' => 'DP2FRZR'],
            ['nama_unit' => 'DKKN'],
            ['nama_unit' => 'DIIBN'],
            ['nama_unit' => 'DIFRZR'],
        ];

        $golongans = [
            ['nama_golongan' => 'III/a', 'pangkat' => 'Penata Muda'],
            ['nama_golongan' => 'III/b', 'pangkat' => 'Penata Muda Tingkat I'],
            ['nama_golongan' => 'III/c', 'pangkat' => 'Penata'],
            ['nama_golongan' => 'III/d', 'pangkat' => 'Penata Tingkat I'],
            ['nama_golongan' => 'IV/a', 'pangkat' => 'Pembina'],
            ['nama_golongan' => 'IV/b', 'pangkat' => 'Pembina Tingkat I'],
            ['nama_golongan' => 'IV/c', 'pangkat' => 'Pembina Utama Muda'],
            ['nama_golongan' => 'IV/d', 'pangkat' => 'Pembina Utama Madya'],
            ['nama_golongan' => 'IV/e', 'pangkat' => 'Pembina Utama'],
        ];

        DB::table('unit_kerjas')->upsert(
            $unitKerjas,
            ['nama_unit'],
            ['nama_unit']
        );

        DB::table('golongans')->upsert(
            $golongans,
            ['nama_golongan'],
            ['pangkat']
        );
    }
}
