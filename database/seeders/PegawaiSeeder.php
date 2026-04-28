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
        $pegawaiRows = [
            [
                'user' => [
                    'name' => 'Budi Santoso',
                    'email' => 'budi.santoso@bapeten.go.id',
                ],
                'nip' => '198801012010011001',
                'nama_lengkap' => 'Budi Santoso',
                'unit_nama' => 'INSP',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Pertama',
                'golongan_nama' => 'III/c',
                'tmt_jabatan' => '2024-01-02',
                'tmt_golongan' => '2023-03-01',
                'status_ukom' => true,
            ],
            [
                'user' => [
                    'name' => 'Siti Rahmawati',
                    'email' => 'siti.rahmawati@bapeten.go.id',
                ],
                'nip' => '198905102011012002',
                'nama_lengkap' => 'Siti Rahmawati',
                'unit_nama' => 'BHKK',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Muda',
                'golongan_nama' => 'III/d',
                'tmt_jabatan' => '2023-07-01',
                'tmt_golongan' => '2022-04-01',
                'status_ukom' => false,
            ],
            [
                'user' => [
                    'name' => 'Andi Pratama',
                    'email' => 'andi.pratama@bapeten.go.id',
                ],
                'nip' => '199002212012011003',
                'nama_lengkap' => 'Andi Pratama',
                'unit_nama' => 'P2STPIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Madya',
                'golongan_nama' => 'IV/a',
                'tmt_jabatan' => '2022-10-01',
                'tmt_golongan' => '2021-01-01',
                'status_ukom' => true,
            ],
            [
                'user' => [
                    'name' => 'Nina Lestari',
                    'email' => 'nina.lestari@bapeten.go.id',
                ],
                'nip' => '198706182009122004',
                'nama_lengkap' => 'Nina Lestari',
                'unit_nama' => 'DPFRZR',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Utama',
                'golongan_nama' => 'IV/b',
                'tmt_jabatan' => '2021-05-15',
                'tmt_golongan' => '2020-06-01',
                'status_ukom' => true,
            ],
            [
                'user' => [
                    'name' => 'Rudi Hartono',
                    'email' => 'rudi.hartono@bapeten.go.id',
                ],
                'nip' => '199101152013011005',
                'nama_lengkap' => 'Rudi Hartono',
                'unit_nama' => 'BPIK',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Pertama',
                'golongan_nama' => 'III/b',
                'tmt_jabatan' => '2024-02-01',
                'tmt_golongan' => '2023-01-01',
                'status_ukom' => false,
            ],
            [
                'user' => [
                    'name' => 'Dewi Puspita',
                    'email' => 'dewi.puspita@bapeten.go.id',
                ],
                'nip' => '198912302011022006',
                'nama_lengkap' => 'Dewi Puspita',
                'unit_nama' => 'BOU',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Muda',
                'golongan_nama' => 'III/d',
                'tmt_jabatan' => '2023-11-01',
                'tmt_golongan' => '2022-08-01',
                'status_ukom' => true,
            ],
            [
                'user' => [
                    'name' => 'Fajar Nugroho',
                    'email' => 'fajar.nugroho@bapeten.go.id',
                ],
                'nip' => '199205052014031007',
                'nama_lengkap' => 'Fajar Nugroho',
                'unit_nama' => 'BDL',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Madya',
                'golongan_nama' => 'IV/a',
                'tmt_jabatan' => '2022-09-15',
                'tmt_golongan' => '2021-07-01',
                'status_ukom' => true,
            ],
            [
                'user' => [
                    'name' => 'Maya Sari',
                    'email' => 'maya.sari@bapeten.go.id',
                ],
                'nip' => '198803112010042008',
                'nama_lengkap' => 'Maya Sari',
                'unit_nama' => 'DPIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Utama',
                'golongan_nama' => 'IV/b',
                'tmt_jabatan' => '2021-03-01',
                'tmt_golongan' => '2020-01-01',
                'status_ukom' => true,
            ],
            [
                'user' => [
                    'name' => 'Taufik Hidayat',
                    'email' => 'taufik.hidayat@bapeten.go.id',
                ],
                'nip' => '199307212015011009',
                'nama_lengkap' => 'Taufik Hidayat',
                'unit_nama' => 'DKKN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Pertama',
                'golongan_nama' => 'III/c',
                'tmt_jabatan' => '2024-04-01',
                'tmt_golongan' => '2023-05-01',
                'status_ukom' => false,
            ],
            [
                'user' => [
                    'name' => 'Lina Kartika',
                    'email' => 'lina.kartika@bapeten.go.id',
                ],
                'nip' => '198911082013012010',
                'nama_lengkap' => 'Lina Kartika',
                'unit_nama' => 'DIIBN',
                'jabatan_nama' => 'Pengawas Radiasi',
                'jabatan_jenjang' => 'Muda',
                'golongan_nama' => 'IV/a',
                'tmt_jabatan' => '2023-08-01',
                'tmt_golongan' => '2022-03-01',
                'status_ukom' => true,
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

            Pegawai::query()->updateOrCreate(
                ['nip' => $row['nip']],
                [
                    'user_id' => $user->id,
                    'unit_kerja_id' => $unitKerjaId,
                    'jabatan_id' => $jabatanId,
                    'golongan_id' => $golonganId,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'tmt_jabatan' => $row['tmt_jabatan'],
                    'tmt_golongan' => $row['tmt_golongan'],
                    'status_ukom' => $row['status_ukom'],
                ]
            );
        }
    }
}
