<?php

namespace App\Services;

use App\Models\UsulanKenaikanPangkat;
use App\Models\Pegawai;
use App\Models\DokumenUsulan;
use App\Models\RiwayatPak;
use Illuminate\Support\Facades\DB;
use Exception;

class UsulanKenaikanPangkatService
{
    /**
     * Menyimpan usulan kenaikan pangkat baru beserta dokumennya.
     *
     * @param array $data Data dari request
     * @param \Illuminate\Http\UploadedFile[] $files File yang diunggah
     * @param Pegawai $pegawai
     * @return array Array berisi 'success' boolean dan 'message' string
     * @throws Exception
     */
    public function storeUsulan(array $data, array $files, Pegawai $pegawai): array
    {
        if ($pegawai->sedang_hukuman_disiplin) {
            throw new Exception('Pegawai sedang menjalani hukuman disiplin dan tidak dapat diusulkan.');
        }

        if ($pegawai->activeUsulan()->exists()) {
            throw new Exception('Pegawai ini sudah memiliki draf tertunda atau usulan yang sedang diproses. Silakan selesaikan atau hapus usulan tersebut terlebih dahulu.');
        }

        if (isset($data['is_lintas_jenjang']) && $data['is_lintas_jenjang']) {
            if (!$pegawai->status_ukom) {
                throw new Exception('Pegawai belum dinyatakan lulus Uji Kompetensi (Ukom) untuk Kenaikan Jenjang.');
            }
            
            $currentGolonganName = $pegawai->golongan ? $pegawai->golongan->nama_golongan : '';
            $isPangkatPuncak = false;
            
            $pangkatPuncakList = ['III/b', 'III/d', 'IV/c', 'IV/e', 'II/d'];
            if ($pegawai->jabatan && strtolower($pegawai->jabatan->jenjang) === 'pemula' && $currentGolonganName === 'II/a') {
                $isPangkatPuncak = true;
            } elseif (in_array($pegawai->golongan->pangkat ?? '', $pangkatPuncakList)) {
                $isPangkatPuncak = true;
            }
            
            if (!$isPangkatPuncak) {
                throw new Exception('Pegawai belum mencapai pangkat puncak pada jenjang saat ini untuk dapat mengusulkan Kenaikan Jenjang.');
            }
        }

        DB::beginTransaction();
        try {
            $usulan = UsulanKenaikanPangkat::create([
                'pegawai_id' => $pegawai->id,
                'golongan_lama_id' => $pegawai->golongan_id,
                'golongan_baru_id' => $data['golongan_baru_id'],
                'status' => $data['action_type'] === 'submit' ? ($data['is_lintas_jenjang'] ? 'PROSES_KENAIKAN_JENJANG' : 'PROSES_KP_REGULER') : 'draft',
                'saldo_ak_awal' => $data['saldo_ak_awal'],
                'potongan_ak' => $data['potongan_ak'],
                'sisa_ak' => $data['sisa_ak'],
                'is_lintas_jenjang' => $data['is_lintas_jenjang'],
            ]);

            $year = date('Y');
            $uploadPath = "dokumen_usulan/{$pegawai->nip}/{$year}";

            foreach ($files as $jenis => $file) {
                if ($file) {
                    $path = $file->store($uploadPath, 'public');
                    
                    DokumenUsulan::create([
                        'usulan_kenaikan_pangkat_id' => $usulan->id,
                        'jenis_dokumen' => $jenis,
                        'file_path' => $path,
                    ]);
                }
            }

            if ($data['action_type'] === 'submit') {
                $pegawai->update(['is_locked_usulan' => true]);
                $message = 'Usulan Kenaikan Pangkat berhasil dikirim dan pegawai telah dikunci untuk diproses.';
            } else {
                $message = 'Draf Usulan Kenaikan Pangkat berhasil disimpan.';
            }

            DB::commit();
            return ['success' => true, 'message' => $message];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Melengkapi dokumen draf usulan dan mengirimkannya.
     *
     * @param UsulanKenaikanPangkat $usulan
     * @param array $files
     * @return array
     * @throws Exception
     */
    public function updateDraft(UsulanKenaikanPangkat $usulan, array $files): array
    {
        if ($usulan->status !== 'draft') {
            throw new Exception('Usulan ini bukan draf.');
        }

        DB::beginTransaction();
        try {
            $pegawai = $usulan->pegawai;
            $year = date('Y');
            $uploadPath = "dokumen_usulan/{$pegawai->nip}/{$year}";

            foreach ($files as $jenis => $file) {
                if ($file) {
                    $path = $file->store($uploadPath, 'public');
                    
                    DokumenUsulan::updateOrCreate(
                        [
                            'usulan_kenaikan_pangkat_id' => $usulan->id,
                            'jenis_dokumen' => $jenis,
                        ],
                        [
                            'file_path' => $path,
                        ]
                    );
                }
            }

            $statusSubmit = $usulan->is_lintas_jenjang ? 'PROSES_KENAIKAN_JENJANG' : 'PROSES_KP_REGULER';
            $usulan->update(['status' => $statusSubmit]);
            $pegawai->update(['is_locked_usulan' => true]);

            DB::commit();
            return ['success' => true, 'message' => 'Dokumen berhasil dilengkapi dan Usulan dikirim untuk diproses.'];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Menyetujui usulan dan memperbarui data pegawai serta riwayat PAK.
     *
     * @param UsulanKenaikanPangkat $usulan
     * @param array $data Data nomor SK dan TMT
     * @return array
     * @throws Exception
     */
    public function approveUsulan(UsulanKenaikanPangkat $usulan, array $data): array
    {
        if (!in_array($usulan->status, ['sedang_diproses', 'PROSES_KP_REGULER', 'PROSES_KENAIKAN_JENJANG'])) {
            throw new Exception('Status usulan tidak valid untuk disetujui.');
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'status' => 'selesai',
                'nomor_sk_baru' => $data['nomor_sk_baru'],
                'tmt_golongan_baru' => $data['tmt_golongan_baru'],
            ];

            if ($usulan->is_lintas_jenjang) {
                $updateData['no_sertifikat_ukom'] = $data['no_sertifikat_ukom'] ?? null;
                $updateData['tgl_lulus_ukom'] = $data['tgl_lulus_ukom'] ?? null;
            }

            $usulan->update($updateData);

            // Update Pegawai and unlock
            $pegawai = $usulan->pegawai;
            
            if ($usulan->is_lintas_jenjang) {
                // === KENAIKAN JENJANG ===
                // Hanya update jabatan & tmt_jabatan. Golongan & AK TIDAK berubah.
                $updateFields = [
                    'is_locked_usulan' => false,
                ];

                $currentJabatan = $pegawai->jabatan;
                if ($currentJabatan) {
                    $nextJabatan = \App\Models\Jabatan::where('kategori', $currentJabatan->kategori)
                        ->where('id', '>', $currentJabatan->id)
                        ->orderBy('id')
                        ->first();
                        
                    if ($nextJabatan) {
                        $updateFields['jabatan_id'] = $nextJabatan->id;
                        $updateFields['tmt_jabatan'] = $data['tmt_golongan_baru'];
                    }
                }

                $pegawai->update($updateFields);
                // Tidak membuat PAK reset apapun — AK tetap utuh.
            } else {
                // === KENAIKAN PANGKAT ===
                $updateFields = [
                    'golongan_id' => $usulan->golongan_baru_id,
                    'tmt_golongan' => $data['tmt_golongan_baru'],
                    'is_locked_usulan' => false,
                ];

                // Deteksi apakah ini kenaikan pangkat lintas jenjang
                // (pangkat lama BUKAN dalam daftar pangkat jenjang aktif)
                $projectionService = app(ProjectionService::class);
                $pangkats = $projectionService->getPangkatListForJenjang(
                    $pegawai->jabatan->jenjang ?? '',
                    $pegawai->jabatan->kategori ?? ''
                );
                $golonganLama = \App\Models\Golongan::find($usulan->golongan_lama_id);
                $isLintasJenjangPangkat = $golonganLama && !in_array($golonganLama->nama_golongan, $pangkats);

                if ($isLintasJenjangPangkat) {
                    // Pangkat lintas jenjang: Reset AK, update tmt_jabatan sebagai baseline baru
                    $updateFields['tmt_jabatan'] = $data['tmt_golongan_baru'];
                }

                $pegawai->update($updateFields);

                if ($isLintasJenjangPangkat) {
                    // Buat PAK baseline reset untuk pangkat lintas jenjang
                    \App\Models\RiwayatPak::create([
                        'pegawai_id' => $pegawai->id,
                        'no_pak' => 'DASAR-KP-LJ-' . $usulan->id,
                        'tanggal_pak' => $data['tmt_golongan_baru'],
                        'ak_tambahan' => 0,
                        'ak_total' => 0,
                        'is_konversi_baru' => true,
                        'periode_awal' => $data['tmt_golongan_baru'],
                        'periode_akhir' => $data['tmt_golongan_baru'],
                    ]);
                }
                // Kenaikan pangkat dalam jenjang sama: AK diakumulasi, tidak ada PAK virtual.
            }

            // Recalculate all records sequentially to ensure chronological correctness
            $allRecords = \App\Models\RiwayatPak::query()
                ->where('pegawai_id', $pegawai->id)
                ->orderBy('tanggal_pak')
                ->orderBy('id')
                ->get();

            $runningTotal = 0;
            foreach ($allRecords as $record) {
                $runningTotal += (float) $record->ak_tambahan;
                if (round((float) $record->ak_total, 3) !== round($runningTotal, 3)) {
                    $record->updateQuietly(['ak_total' => $runningTotal]);
                }
            }

            DB::commit();
            return ['success' => true, 'message' => 'Persetujuan Kenaikan Pangkat berhasil dieksekusi. Data AK dan Golongan telah diperbarui.'];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mendapatkan Angka Kredit Dasar sesuai Lampiran II PerBKN 3/2023.
     */
    public function getAkDasar(string $jenjang, string $golongan): float
    {
        $jenjang = strtolower(trim($jenjang));
        $golongan = trim($golongan);
        
        // Keahlian
        if ($jenjang === 'pertama' || $jenjang === 'ahli pertama') {
            return $golongan === 'III/b' ? 50.0 : 0.0;
        }
        if ($jenjang === 'muda' || $jenjang === 'ahli muda') {
            return $golongan === 'III/d' ? 50.0 : 0.0;
        }
        if ($jenjang === 'madya' || $jenjang === 'ahli madya') {
            if ($golongan === 'IV/b') return 150.0;
            if ($golongan === 'IV/c') return 300.0;
            return 0.0;
        }
        if ($jenjang === 'utama' || $jenjang === 'ahli utama') {
            return $golongan === 'IV/e' ? 200.0 : 0.0;
        }
        
        // Keterampilan
        if ($jenjang === 'pemula') {
            return 0.0;
        }
        if ($jenjang === 'terampil') {
            return $golongan === 'II/d' ? 20.0 : 0.0;
        }
        if ($jenjang === 'mahir') {
            return $golongan === 'III/b' ? 50.0 : 0.0;
        }
        if ($jenjang === 'penyelia') {
            return $golongan === 'III/d' ? 100.0 : 0.0;
        }
        
        return 0.0;
    }
}
