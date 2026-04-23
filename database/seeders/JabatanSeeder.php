<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [
            [
                'nama_jabatan' => 'Pengawas Radiasi',
                'jenjang' => 'Pertama',
                'koefisien_tahunan' => 12.50,
                'target_ak_kenaikan_pangkat' => 50,
                'target_ak_kenaikan_jenjang' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_jabatan' => 'Pengawas Radiasi',
                'jenjang' => 'Muda',
                'koefisien_tahunan' => 25.00,
                'target_ak_kenaikan_pangkat' => 100,
                'target_ak_kenaikan_jenjang' => 200,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_jabatan' => 'Pengawas Radiasi',
                'jenjang' => 'Madya',
                'koefisien_tahunan' => 37.50,
                'target_ak_kenaikan_pangkat' => 150,
                'target_ak_kenaikan_jenjang' => 450,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_jabatan' => 'Pengawas Radiasi',
                'jenjang' => 'Utama',
                'koefisien_tahunan' => 50.00,
                'target_ak_kenaikan_pangkat' => 200,
                'target_ak_kenaikan_jenjang' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('jabatans')->upsert(
            $rows,
            ['nama_jabatan', 'jenjang'],
            [
                'koefisien_tahunan',
                'target_ak_kenaikan_pangkat',
                'target_ak_kenaikan_jenjang',
                'updated_at',
            ]
        );
    }
}
