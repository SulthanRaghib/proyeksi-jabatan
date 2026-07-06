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

        DB::beginTransaction();
        try {
            $usulan = UsulanKenaikanPangkat::create([
                'pegawai_id' => $pegawai->id,
                'golongan_lama_id' => $pegawai->golongan_id,
                'golongan_baru_id' => $data['golongan_baru_id'],
                'status' => $data['action_type'] === 'submit' ? 'sedang_diproses' : 'draft',
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

            $usulan->update(['status' => 'sedang_diproses']);
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
        if ($usulan->status !== 'sedang_diproses') {
            throw new Exception('Status usulan tidak valid untuk disetujui.');
        }

        DB::beginTransaction();
        try {
            // Update the usulan
            $usulan->update([
                'status' => 'selesai',
                'nomor_sk_baru' => $data['nomor_sk_baru'],
                'tmt_golongan_baru' => $data['tmt_golongan_baru'],
            ]);

            // Update Pegawai Golongan and unlock
            $pegawai = $usulan->pegawai;
            $pegawai->update([
                'golongan_id' => $usulan->golongan_baru_id,
                'tmt_golongan' => $data['tmt_golongan_baru'],
                'is_locked_usulan' => false,
            ]);

            // Generate Riwayat PAK Transaction for the deduction
            RiwayatPak::create([
                'pegawai_id' => $pegawai->id,
                'no_pak' => 'POTONGAN-SK-' . $data['nomor_sk_baru'],
                'tanggal_pak' => $data['tmt_golongan_baru'],
                'periode_awal' => $data['tmt_golongan_baru'],
                'periode_akhir' => $data['tmt_golongan_baru'],
                'ak_dasar' => $usulan->saldo_ak_awal,
                'ak_pengalaman' => 0,
                'ak_tambahan' => -$usulan->potongan_ak,
                'ak_total' => $usulan->sisa_ak,
                'keterangan' => 'Pemotongan AK otomatis Kenaikan Pangkat ke ' . $usulan->golonganBaru->nama_golongan,
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'Persetujuan Kenaikan Pangkat berhasil dieksekusi. Data AK dan Golongan telah diperbarui.'];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
