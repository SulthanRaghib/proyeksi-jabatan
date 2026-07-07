<?php

namespace Database\Seeders;

use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama agar fresh
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\RiwayatPak::truncate();
        \App\Models\KinerjaTahunan::truncate();
        \App\Models\UsulanKenaikanPangkat::truncate();
        \App\Models\Pegawai::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $pegawaiRows = [
            // 1. Andi Pratama (Progres Normal)
            [
                'user' => ['name' => 'Andi Pratama', 'email' => 'andi.pratama@bapeten.go.id'],
                'nip' => '199002212012011003',
                'nama_lengkap' => 'Andi Pratama',
                'unit_nama' => 'P2STPIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Madya',
                'golongan_nama' => 'IV/a',
                'tmt_jabatan' => '2022-04-01',
                'tmt_golongan' => '2022-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    ['no_pak' => 'PAK-INT/2022/04/01', 'tanggal' => '2022-04-01', 'ak_total' => 400], 
                ],
                'kinerja' => [
                    ['tahun' => 2023, 'predikat' => 'baik', 'ak' => 37.5],
                    ['tahun' => 2024, 'predikat' => 'baik', 'ak' => 37.5],
                    ['tahun' => 2025, 'predikat' => 'baik', 'ak' => 37.5],
                ]
            ],
            // 2. Budi Santoso (Baru Naik Pangkat - Progress 0%)
            [
                'user' => ['name' => 'Budi Santoso', 'email' => 'budi.santoso@bapeten.go.id'],
                'nip' => '198801012010011001',
                'nama_lengkap' => 'Budi Santoso',
                'unit_nama' => 'INSP',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Muda',
                'golongan_nama' => 'III/c',
                'tmt_jabatan' => '2026-04-01',
                'tmt_golongan' => '2026-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    ['no_pak' => 'PAK-KP/2026/04/01', 'tanggal' => '2026-04-01', 'ak_total' => 200], 
                ],
                'kinerja' => [] 
            ],
            // 3. Dewi Puspita (Pangkat Puncak)
            [
                'user' => ['name' => 'Dewi Puspita', 'email' => 'dewi.puspita@bapeten.go.id'],
                'nip' => '198912302011022006',
                'nama_lengkap' => 'Dewi Puspita',
                'unit_nama' => 'BOU',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Muda',
                'golongan_nama' => 'III/d',
                'tmt_jabatan' => '2020-04-01',
                'tmt_golongan' => '2022-04-01',
                'status_ukom' => false, 
                'riwayat_pak' => [
                    ['no_pak' => 'PAK-KP/2022/04/01', 'tanggal' => '2022-04-01', 'ak_total' => 300], 
                ],
                'kinerja' => [
                    ['tahun' => 2023, 'predikat' => 'baik', 'ak' => 25],
                    ['tahun' => 2024, 'predikat' => 'baik', 'ak' => 25],
                    ['tahun' => 2025, 'predikat' => 'baik', 'ak' => 25],
                    ['tahun' => 2026, 'predikat' => 'baik', 'ak' => 25],
                ]
            ],
            // 4. Lina Kartika (Kinerja Sangat Baik / Akselerasi)
            [
                'user' => ['name' => 'Lina Kartika', 'email' => 'lina.kartika@bapeten.go.id'],
                'nip' => '198911082013012010',
                'nama_lengkap' => 'Lina Kartika',
                'unit_nama' => 'DIIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Pertama',
                'golongan_nama' => 'III/a',
                'tmt_jabatan' => '2024-04-01',
                'tmt_golongan' => '2024-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    ['no_pak' => 'PAK-AWAL/2024/04/01', 'tanggal' => '2024-04-01', 'ak_total' => 100], 
                ],
                'kinerja' => [
                    ['tahun' => 2025, 'predikat' => 'sangat baik', 'ak' => 18.75], 
                    ['tahun' => 2026, 'predikat' => 'sangat baik', 'ak' => 18.75], 
                ]
            ],
            // 5. Maya Sari (IV/b Madya - History Ada)
            [
                'user' => ['name' => 'Maya Sari', 'email' => 'maya.sari@bapeten.go.id'],
                'nip' => '198803112010042008',
                'nama_lengkap' => 'Maya Sari',
                'unit_nama' => 'DPIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Madya',
                'golongan_nama' => 'IV/b',
                'tmt_jabatan' => '2021-04-01',
                'tmt_golongan' => '2024-04-01', 
                'status_ukom' => true,
                'riwayat_pak' => [
                    ['no_pak' => 'PAK-KP/2024/04/01', 'tanggal' => '2024-04-01', 'ak_total' => 550], 
                ],
                'kinerja' => [
                    ['tahun' => 2025, 'predikat' => 'baik', 'ak' => 37.5],
                    ['tahun' => 2026, 'predikat' => 'baik', 'ak' => 37.5],
                ]
            ],
        ];

        foreach ($pegawaiRows as $row) {
            $user = User::query()->updateOrCreate(
                ['email' => $row['user']['email']],
                [
                    'name' => $row['user']['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $unitKerjaId = UnitKerja::query()
                ->where('nama_unit', $row['unit_nama'])
                ->value('id');

            $jabatanId = Jabatan::query()
                ->where('nama_jabatan', $row['jabatan_nama'])
                ->where('jenjang', $row['jabatan_jenjang'])
                ->value('id');

            $golonganId = Golongan::query()
                ->where('nama_golongan', $row['golongan_nama'])
                ->value('id');

            $pegawai = Pegawai::create([
                'nip' => $row['nip'],
                'user_id' => $user->id,
                'unit_kerja_id' => $unitKerjaId,
                'jabatan_id' => $jabatanId,
                'golongan_id' => $golonganId,
                'nama_lengkap' => $row['nama_lengkap'],
                'tmt_jabatan' => $row['tmt_jabatan'],
                'tmt_golongan' => $row['tmt_golongan'],
                'status_ukom' => $row['status_ukom'],
            ]);

            // Seeding Riwayat PAK
            foreach ($row['riwayat_pak'] as $pak) {
                \App\Models\RiwayatPak::create([
                    'pegawai_id' => $pegawai->id,
                    'no_pak' => $pak['no_pak'],
                    'tanggal_pak' => $pak['tanggal'],
                    'ak_tambahan' => $pak['ak_total'], // Modal awal, terekam tapi diabaikan di Proyeksi jika <= TMT Golongan
                    'ak_total' => $pak['ak_total'],
                ]);
            }

            // Seeding Kinerja Tahunan
            foreach ($row['kinerja'] as $kinerja) {
                \App\Models\KinerjaTahunan::create([
                    'pegawai_id' => $pegawai->id,
                    'tahun' => $kinerja['tahun'],
                    'predikat' => $kinerja['predikat'],
                    'ak_didapat' => $kinerja['ak'],
                ]);
            }
        }
    }
}
