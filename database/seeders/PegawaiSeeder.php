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
            // ================== KATEGORI KEAHLIAN ==================
            
            // 1. Andi Pratama (Ahli Madya, IV/a) - Normal Keahlian
            // Target Pangkat: IV/b (150 AK). TMT 2022.
            // AK Integrasi = 15.0 AK (setelah dikurangi Nilai Dasar Madya IV/a 400 AK).
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
                    ['tanggal' => '2022-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 15.0, 'predikat' => null], // Baseline AK Integrasi (Surplus Integrasi)
                    ['tanggal' => '2023-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 37.5, 'predikat' => 'baik'], 
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 37.5, 'predikat' => 'baik'], 
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 37.5, 'predikat' => 'baik'], 
                ],
                'kinerja' => [
                    ['tahun' => 2025, 'predikat' => 'baik', 'ak' => 37.5], 
                ]
            ],
            
            // 2. Budi Santoso (Ahli Muda, III/c) - Normal Keahlian
            // Target Pangkat: III/d (100 AK). TMT 2024.
            // AK Integrasi = 10.0 AK (setelah dikurangi Nilai Dasar Muda III/c 200 AK).
            [
                'user' => ['name' => 'Budi Santoso', 'email' => 'budi.santoso@bapeten.go.id'],
                'nip' => '198801012010011001',
                'nama_lengkap' => 'Budi Santoso',
                'unit_nama' => 'INSP',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Muda',
                'golongan_nama' => 'III/c',
                'tmt_jabatan' => '2024-04-01',
                'tmt_golongan' => '2024-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 10.0, 'predikat' => null], // Baseline AK Integrasi
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
                ],
                'kinerja' => [] 
            ],
            
            // 3. Dewi Puspita (Ahli Muda, III/d) - PANGKAT PUNCAK (Terkunci Jenjang)
            // Target Pangkat: Terkunci (Muda mentok di III/d). Wajib usul Kenaikan Jenjang ke Madya.
            [
                'user' => ['name' => 'Dewi Puspita', 'email' => 'dewi.puspita@bapeten.go.id'],
                'nip' => '198912302011022006',
                'nama_lengkap' => 'Dewi Puspita',
                'unit_nama' => 'BOU',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Muda',
                'golongan_nama' => 'III/d',
                'tmt_jabatan' => '2021-04-01',
                'tmt_golongan' => '2023-04-01',
                'status_ukom' => false, // Belum lulus Ukom, tidak bisa naik jenjang
                'riwayat_pak' => [
                    ['tanggal' => '2023-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 12.0, 'predikat' => null], // Baseline surplus integrasi III/d
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
                    ['tanggal' => '2026-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
                ],
                'kinerja' => []
            ],
            
            // 4. Eko Prasetyo (Ahli Pertama, III/b) - PANGKAT PUNCAK (Siap Naik Jenjang)
            // Target: Kenaikan Jenjang ke Ahli Muda (kebutuhan 50 AK). Sudah lulus Ukom.
            // AK Integrasi = 22.0 AK. Total AK = 22.0 + 12.5 + 18.75 = 53.25 AK (melebihi target 50 AK).
            [
                'user' => ['name' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@bapeten.go.id'],
                'nip' => '199201012015021005',
                'nama_lengkap' => 'Eko Prasetyo',
                'unit_nama' => 'DIIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Pertama',
                'golongan_nama' => 'III/b',
                'tmt_jabatan' => '2021-04-01',
                'tmt_golongan' => '2024-04-01',
                'status_ukom' => true, // LULUS UKOM
                'riwayat_pak' => [
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 22.0, 'predikat' => null], // Baseline AK Integrasi
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 12.5, 'predikat' => 'baik'],
                    ['tanggal' => '2026-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 18.75, 'predikat' => 'sangat_baik'],
                ],
                'kinerja' => []
            ],
            
            // 5. Fajar Maulana (Ahli Utama, IV/e) - MAKSIMAL KEAHLIAN
            // Target Pangkat: Maksimal (Mentok). Target Jenjang: Maksimal.
            [
                'user' => ['name' => 'Fajar Maulana', 'email' => 'fajar.maulana@bapeten.go.id'],
                'nip' => '197005121995031001',
                'nama_lengkap' => 'Fajar Maulana',
                'unit_nama' => 'DPIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Utama',
                'golongan_nama' => 'IV/e',
                'tmt_jabatan' => '2019-04-01',
                'tmt_golongan' => '2022-04-01',
                'status_ukom' => true,
                'riwayat_pak' => [
                    ['tanggal' => '2022-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 50.0, 'predikat' => null], // Baseline
                    ['tanggal' => '2023-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 50.0, 'predikat' => 'baik'],
                ],
                'kinerja' => []
            ],

            // ================== KATEGORI KETERAMPILAN ==================

            // 6. Lina Kartika (Terampil, II/c) - KASUS PRORATA/TRIWULAN & SURPLUS CARRY-OVER
            // Target Pangkat: II/d (20 AK).
            // PAK 1: Integrasi (2024-04-01) = 10.0 AK.
            // PAK 2: Konversi Periodik (Triwulan IV - 2024-12-31, 9 bulan efektif) = 5.0 * (9/12) = 3.75 AK (Prorata).
            // PAK 3: Konversi Tahunan (2025-12-31, 12 bulan, Sangat Baik) = 7.5 AK.
            // Total AK = 10.0 + 3.75 + 7.5 = 21.25 AK (melebihi target 20 AK).
            [
                'user' => ['name' => 'Lina Kartika', 'email' => 'lina.kartika@bapeten.go.id'],
                'nip' => '199611082019012010',
                'nama_lengkap' => 'Lina Kartika',
                'unit_nama' => 'INSP',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Terampil',
                'golongan_nama' => 'II/c',
                'tmt_jabatan' => '2024-04-01',
                'tmt_golongan' => '2024-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    [
                        'tanggal' => '2024-04-01', 
                        'is_konversi_baru' => false, 
                        'ak_tambahan' => 10.0, 
                        'predikat' => null
                    ], 
                    [
                        'tanggal' => '2024-12-31', 
                        'is_konversi_baru' => true, 
                        'ak_tambahan' => 3.75, 
                        'predikat' => 'baik',
                        'periode_awal' => '2024-04-01',
                        'periode_akhir' => '2024-12-31'
                    ], 
                    [
                        'tanggal' => '2025-12-31', 
                        'is_konversi_baru' => true, 
                        'ak_tambahan' => 7.5, 
                        'predikat' => 'sangat_baik',
                        'periode_awal' => '2025-01-01',
                        'periode_akhir' => '2025-12-31'
                    ], 
                ],
                'kinerja' => []
            ],
            
            // 7. Rizky Ramadhan (Terampil, II/d) - PANGKAT PUNCAK (Terkunci Jenjang)
            // Target Pangkat: Terkunci (Terampil mentok di II/d). Wajib usul Kenaikan Jenjang ke Mahir.
            // AK Integrasi = 5.0 AK.
            [
                'user' => ['name' => 'Rizky Ramadhan', 'email' => 'rizky.ramadhan@bapeten.go.id'],
                'nip' => '199505122018021002',
                'nama_lengkap' => 'Rizky Ramadhan',
                'unit_nama' => 'BOU',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Terampil',
                'golongan_nama' => 'II/d',
                'tmt_jabatan' => '2021-04-01',
                'tmt_golongan' => '2023-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    ['tanggal' => '2023-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 5.0, 'predikat' => null], // Baseline II/d
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 5.0, 'predikat' => 'baik'], 
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 5.0, 'predikat' => 'baik'], 
                ],
                'kinerja' => []
            ],
            
            // 8. Siti Aminah (Mahir, III/b) - PANGKAT PUNCAK (Terkunci Jenjang)
            // Target: Kenaikan Jenjang ke Penyelia.
            // AK Integrasi = 8.0 AK.
            [
                'user' => ['name' => 'Siti Aminah', 'email' => 'siti.aminah@bapeten.go.id'],
                'nip' => '199208152015032004',
                'nama_lengkap' => 'Siti Aminah',
                'unit_nama' => 'DIIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Mahir',
                'golongan_nama' => 'III/b',
                'tmt_jabatan' => '2021-04-01',
                'tmt_golongan' => '2024-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 8.0, 'predikat' => null], // Baseline III/b
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 12.5, 'predikat' => 'baik'],
                    ['tanggal' => '2026-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 9.375, 'predikat' => 'butuh_perbaikan'],
                ],
                'kinerja' => []
            ],
            
            // 9. Hendra Wijaya (Penyelia, III/c) - JENJANG PUNCAK KETERAMPILAN
            // Target Pangkat: III/d (100 AK). Target Jenjang: Maksimal (Penyelia adalah jenjang tertinggi).
            // AK Integrasi = 15.0 AK.
            [
                'user' => ['name' => 'Hendra Wijaya', 'email' => 'hendra.wijaya@bapeten.go.id'],
                'nip' => '198710252009021001',
                'nama_lengkap' => 'Hendra Wijaya',
                'unit_nama' => 'DPIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Penyelia',
                'golongan_nama' => 'III/c',
                'tmt_jabatan' => '2022-04-01',
                'tmt_golongan' => '2022-04-01',
                'status_ukom' => false,
                'riwayat_pak' => [
                    ['tanggal' => '2022-04-01', 'is_konversi_baru' => false, 'ak_tambahan' => 15.0, 'predikat' => null], // Baseline III/c
                    ['tanggal' => '2023-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
                    ['tanggal' => '2024-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
                    ['tanggal' => '2025-04-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
                ],
                'kinerja' => []
            ],
            
            // 10. Tono (Penyelia, III/d) - MAKSIMAL KETERAMPILAN
            // Target Pangkat: Maksimal. Target Jenjang: Maksimal. Mentok sepenuhnya di Kategori Keterampilan.
            [
                'user' => ['name' => 'Tono', 'email' => 'tono@bapeten.go.id'],
                'nip' => '198004182005121002',
                'nama_lengkap' => 'Tono',
                'unit_nama' => 'P2STPIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Penyelia',
                'golongan_nama' => 'III/d',
                'tmt_jabatan' => '2018-01-01',
                'tmt_golongan' => '2021-01-01',
                'status_ukom' => true,
                'riwayat_pak' => [
                    ['tanggal' => '2021-01-01', 'is_konversi_baru' => false, 'ak_tambahan' => 10.0, 'predikat' => null], 
                    ['tanggal' => '2022-01-01', 'is_konversi_baru' => false, 'ak_tambahan' => 10.0, 'predikat' => null], 
                    ['tanggal' => '2023-01-01', 'is_konversi_baru' => true, 'ak_tambahan' => 25.0, 'predikat' => 'baik'], 
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
                    $koefisien = $pegawai->jabatan->koefisien_tahunan ?? 0;
                    
                    \App\Models\KinerjaTahunan::create([
                        'pegawai_id' => $pegawai->id,
                        'pak_id' => $createdPak->id,
                        'tahun' => $tahunKinerja,
                        'predikat' => $pak['predikat'],
                        'koefisien_saat_itu' => $koefisien,
                        'ak_didapat' => $pak['ak_tambahan'],
                    ]);
                }
            }

            // Seeding Kinerja Tahunan
            foreach ($row['kinerja'] as $kinerja) {
                $koefisien = $pegawai->jabatan->koefisien_tahunan ?? 0;
                
                \App\Models\KinerjaTahunan::create([
                    'pegawai_id' => $pegawai->id,
                    'tahun' => $kinerja['tahun'],
                    'predikat' => $kinerja['predikat'],
                    'koefisien_saat_itu' => $koefisien,
                    'ak_didapat' => $kinerja['ak'],
                ]);
            }
        }
    }
}
