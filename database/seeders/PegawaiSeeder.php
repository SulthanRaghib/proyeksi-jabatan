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
        // 1. Clean old records
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\RiwayatPak::truncate();
        \App\Models\KinerjaTahunan::truncate();
        \App\Models\UsulanKenaikanPangkat::truncate();
        \App\Models\Pegawai::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $pegawaiRows = [
            // 1. Andi Pratama (Pengawas Radiasi Madya, IV/a)
            // TMT 2022. Target IV/b (150 AK).
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
                    ['tanggal' => '2022-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 400, 'predikat' => null], // Integrasi
                    ['tanggal' => '2023-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 37.5, 'predikat' => 'baik'], // PAK Konversi 2022
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 37.5, 'predikat' => 'baik'], // PAK Konversi 2023
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 37.5, 'predikat' => 'baik'], // PAK Konversi 2024
                ],
                'kinerja' => [
                    ['tahun' => 2025, 'predikat' => 'baik', 'ak' => 37.5], 
                ]
            ],
            // 2. Budi Santoso (Muda, III/c - Baru Naik Pangkat)
            // TMT 2026. Target III/d (100 AK).
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
                    ['tanggal' => '2026-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 200, 'predikat' => null], // Integrasi saat naik pangkat
                ],
                'kinerja' => [] 
            ],
            // 3. Dewi Puspita (Muda, III/d - Pangkat Puncak, Siap Naik Jenjang)
            // TMT Jabatan 2020. TMT Golongan 2022. Target IV/a (100 AK).
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
                    ['tanggal' => '2022-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 300, 'predikat' => null], // Integrasi
                    ['tanggal' => '2023-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25, 'predikat' => 'baik'], // PAK Konversi 2022
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25, 'predikat' => 'baik'], // PAK Konversi 2023
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25, 'predikat' => 'baik'], // PAK Konversi 2024
                    ['tanggal' => '2026-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25, 'predikat' => 'baik'], // PAK Konversi 2025
                ],
                'kinerja' => []
            ],
            // 4. Lina Kartika (Pertama, III/a - Kinerja Sangat Baik)
            // TMT 2024. Target III/b (50 AK).
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
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 100, 'predikat' => null], // Integrasi awal
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 18.75, 'predikat' => 'sangat_baik'], // PAK Konversi 2024 (Sangat Baik = 150% dari 12.5)
                ],
                'kinerja' => [
                    ['tahun' => 2025, 'predikat' => 'sangat_baik', 'ak' => 18.75], // Kinerja 2025 (belum PAK)
                ]
            ],
            // 5. Maya Sari (Madya, IV/b)
            // TMT 2021, TMT Golongan 2024. Target IV/c (150 AK).
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
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 550, 'predikat' => null], // Integrasi
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 37.5, 'predikat' => 'baik'], // PAK Konversi 2024
                ],
                'kinerja' => [
                    ['tahun' => 2025, 'predikat' => 'baik', 'ak' => 37.5], // Kinerja 2025 (belum PAK)
                ]
            ],
            // 6. Sulthan Raghib (Pertama, III/a - Real Case Penilaian Periodik Triwulanan BAPETEN)
            // TMT Jabatan & Golongan: 2024-01-01. Target III/b (50 AK).
            // Mendemonstrasikan bagaimana triwulanan mengejar target AK yang sedikit lagi.
            [
                'user' => ['name' => 'Sulthan Raghib', 'email' => 'sulthan.raghib@bapeten.go.id'],
                'nip' => '199812152023011001',
                'nama_lengkap' => 'Sulthan Raghib',
                'unit_nama' => 'BOU',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Pertama',
                'golongan_nama' => 'III/a',
                'tmt_jabatan' => '2024-01-01',
                'tmt_golongan' => '2024-01-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    // 1. Integrasi awal
                    [
                        'tanggal' => '2024-01-01', 
                        'is_konversi_baru' => false, 
                        'ak_tambahan' => 23, 
                        'predikat' => null,
                        'periode_awal' => null,
                        'periode_akhir' => null
                    ], 
                    // 2. PAK Konversi Tahunan 2024 (Baik = 12.5 AK)
                    [
                        'tanggal' => '2025-01-15', 
                        'is_konversi_baru' => true, 
                        'ak_tambahan' => 12.5, 
                        'predikat' => 'baik',
                        'periode_awal' => '2024-01-01',
                        'periode_akhir' => '2024-12-31'
                    ],
                    // 3. PAK Konversi Tahunan 2025 (Baik = 12.5 AK) - Total AK = 48.0 (Kurang 2.0 AK lagi)
                    [
                        'tanggal' => '2026-01-15', 
                        'is_konversi_baru' => true, 
                        'ak_tambahan' => 12.5, 
                        'predikat' => 'baik',
                        'periode_awal' => '2025-01-01',
                        'periode_akhir' => '2025-12-31'
                    ],
                    // 4. PAK Periodik Triwulan I 2026 (Jan - Mar 2026) -> (3/12) * 12.5 = 3.125 AK -> Total AK = 51.125 (Target tercapai!)
                    [
                        'tanggal' => '2026-04-10', 
                        'is_konversi_baru' => true, 
                        'ak_tambahan' => 3.125, 
                        'predikat' => 'baik',
                        'periode_awal' => '2026-01-01',
                        'periode_akhir' => '2026-03-31'
                    ],
                ],
                'kinerja' => []
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
            $pakService = app(\App\Services\RiwayatPakService::class);
            $currentTotal = 0;
            foreach ($row['riwayat_pak'] as $pak) {
                $tanggalPak = \Carbon\Carbon::parse($pak['tanggal']);
                $currentTotal += $pak['ak_tambahan'];
                
                $periodeAwal = $pak['periode_awal'] ?? ($pak['is_konversi_baru'] ? $tanggalPak->copy()->subYear()->startOfYear()->format('Y-m-d') : null);
                $periodeAkhir = $pak['periode_akhir'] ?? ($pak['is_konversi_baru'] ? $tanggalPak->copy()->subYear()->endOfYear()->format('Y-m-d') : null);

                $createdPak = \App\Models\RiwayatPak::create([
                    'pegawai_id' => $pegawai->id,
                    'no_pak' => $pakService->generateNoPak($tanggalPak->year),
                    'tanggal_pak' => $pak['tanggal'],
                    'periode_awal' => $periodeAwal,
                    'periode_akhir' => $periodeAkhir,
                    'ak_tambahan' => $pak['ak_tambahan'],
                    'ak_total' => $currentTotal,
                    'is_konversi_baru' => $pak['is_konversi_baru'],
                ]);
                
                if ($pak['predikat'] && $pak['is_konversi_baru']) {
                    $tahunKinerja = $periodeAkhir ? \Carbon\Carbon::parse($periodeAkhir)->year : $tanggalPak->copy()->subYear()->year;
                    
                    \App\Models\KinerjaTahunan::create([
                        'pegawai_id' => $pegawai->id,
                        'pak_id' => $createdPak->id,
                        'tahun' => $tahunKinerja,
                        'predikat' => $pak['predikat'],
                        'ak_didapat' => $pak['ak_tambahan'],
                    ]);
                }
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
