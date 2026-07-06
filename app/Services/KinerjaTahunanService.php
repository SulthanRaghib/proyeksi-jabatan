<?php

namespace App\Services;

use App\Models\KinerjaTahunan;
use App\Models\Pegawai;
use Exception;

class KinerjaTahunanService
{
    /**
     * Menyimpan rekam jejak Kinerja Tahunan baru.
     *
     * @param array $data
     * @return KinerjaTahunan
     * @throws Exception
     */
    public function storeKinerja(array $data): KinerjaTahunan
    {
        $pegawai = Pegawai::with('jabatan')->findOrFail($data['pegawai_id']);
        $jabatan = $pegawai->jabatan;

        if (!$jabatan) {
            throw new Exception('Pegawai belum memiliki jabatan, tidak dapat menghitung angka kredit konversi.');
        }

        $koefisien = $jabatan->koefisien_tahunan;
        $akDidapat = $jabatan->getKonversiByPredikat($data['predikat']);

        if (KinerjaTahunan::where('pegawai_id', $pegawai->id)->where('tahun', $data['tahun'])->exists()) {
            throw new Exception('Data Kinerja untuk tahun tersebut sudah ada.');
        }

        return KinerjaTahunan::create([
            'pegawai_id' => $pegawai->id,
            'tahun' => $data['tahun'],
            'predikat' => $data['predikat'],
            'koefisien_saat_itu' => $koefisien,
            'ak_didapat' => $akDidapat,
        ]);
    }

    /**
     * Memperbarui rekam jejak Kinerja Tahunan.
     *
     * @param KinerjaTahunan $kinerjaTahunan
     * @param array $data
     * @return KinerjaTahunan
     * @throws Exception
     */
    public function updateKinerja(KinerjaTahunan $kinerjaTahunan, array $data): KinerjaTahunan
    {
        if (KinerjaTahunan::where('pegawai_id', $kinerjaTahunan->pegawai_id)
            ->where('tahun', $data['tahun'])
            ->where('id', '!=', $kinerjaTahunan->id)
            ->exists()) {
            throw new Exception('Data Kinerja untuk tahun tersebut sudah ada.');
        }

        $kinerjaTahunan->load('pegawai.jabatan');
        $jabatan = $kinerjaTahunan->pegawai->jabatan;

        if (!$jabatan) {
            throw new Exception('Pegawai belum memiliki jabatan.');
        }

        $akDidapat = $jabatan->getKonversiByPredikat($data['predikat']);

        $kinerjaTahunan->update([
            'tahun' => $data['tahun'],
            'predikat' => $data['predikat'],
            'ak_didapat' => $akDidapat,
        ]);

        return $kinerjaTahunan;
    }

    /**
     * Menghapus rekam jejak Kinerja Tahunan.
     *
     * @param KinerjaTahunan $kinerjaTahunan
     * @return void
     */
    public function deleteKinerja(KinerjaTahunan $kinerjaTahunan): void
    {
        $kinerjaTahunan->delete();
    }
}
