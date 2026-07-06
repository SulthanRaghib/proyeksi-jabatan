<?php

namespace App\Http\Controllers;

use App\Models\UsulanKenaikanPangkat;
use App\Models\Pegawai;
use App\Models\DokumenUsulan;
use App\Models\RiwayatPak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Support\DashboardUiData;

class UsulanKenaikanPangkatController extends Controller
{
    public function index()
    {
        $usulans = UsulanKenaikanPangkat::with(['pegawai', 'golonganLama', 'golonganBaru'])
            ->whereIn('status', ['sedang_diproses', 'selesai'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.usulan-pangkat.index', [
            'usulans' => $usulans,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'golongan_baru_id' => 'required|exists:golongans,id',
            'saldo_ak_awal' => 'required|numeric',
            'potongan_ak' => 'required|numeric',
            'sisa_ak' => 'required|numeric',
            'is_lintas_jenjang' => 'required|boolean',
            'action_type' => 'required|in:draft,submit',
        ]);

        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        if ($pegawai->sedang_hukuman_disiplin) {
            return back()->with('error', 'Pegawai sedang menjalani hukuman disiplin dan tidak dapat diusulkan.');
        }

        if ($pegawai->is_locked_usulan) {
            return back()->with('error', 'Pegawai sudah memiliki usulan yang sedang diproses.');
        }

        DB::beginTransaction();
        try {
            $usulan = UsulanKenaikanPangkat::create([
                'pegawai_id' => $pegawai->id,
                'golongan_lama_id' => $pegawai->golongan_id,
                'golongan_baru_id' => $request->golongan_baru_id,
                'status' => $request->action_type === 'submit' ? 'sedang_diproses' : 'draft',
                'saldo_ak_awal' => $request->saldo_ak_awal,
                'potongan_ak' => $request->potongan_ak,
                'sisa_ak' => $request->sisa_ak,
                'is_lintas_jenjang' => $request->is_lintas_jenjang,
            ]);

            // Handle file uploads
            $dokumenFields = ['sk_pangkat', 'sk_jabatan', 'pak_konversi', 'skp'];
            if ($request->is_lintas_jenjang) {
                $dokumenFields[] = 'ukom';
                $dokumenFields[] = 'formasi';
            }

            $year = date('Y');
            $uploadPath = "dokumen_usulan/{$pegawai->nip}/{$year}";

            foreach ($dokumenFields as $jenis) {
                if ($request->hasFile($jenis)) {
                    $file = $request->file($jenis);
                    $path = $file->store($uploadPath, 'public');
                    
                    DokumenUsulan::create([
                        'usulan_kenaikan_pangkat_id' => $usulan->id,
                        'jenis_dokumen' => $jenis,
                        'file_path' => $path,
                    ]);
                }
            }

            if ($request->action_type === 'submit') {
                $pegawai->update(['is_locked_usulan' => true]);
                $message = 'Usulan Kenaikan Pangkat berhasil dikirim dan pegawai telah dikunci untuk diproses.';
            } else {
                $message = 'Draf Usulan Kenaikan Pangkat berhasil disimpan.';
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function submit(UsulanKenaikanPangkat $usulan)
    {
        // Handle submitting an existing draft
        if ($usulan->status !== 'draft') {
            return back()->with('error', 'Usulan ini bukan draf.');
        }

        $usulan->update(['status' => 'sedang_diproses']);
        $usulan->pegawai->update(['is_locked_usulan' => true]);

        return back()->with('success', 'Usulan berhasil dikirim untuk diproses.');
    }

    public function approve(Request $request, UsulanKenaikanPangkat $usulan)
    {
        $request->validate([
            'nomor_sk_baru' => 'required|string|max:255',
            'tmt_golongan_baru' => 'required|date',
        ]);

        if ($usulan->status !== 'sedang_diproses') {
            return back()->with('error', 'Status usulan tidak valid untuk disetujui.');
        }

        DB::beginTransaction();
        try {
            // Update the usulan
            $usulan->update([
                'status' => 'selesai',
                'nomor_sk_baru' => $request->nomor_sk_baru,
                'tmt_golongan_baru' => $request->tmt_golongan_baru,
            ]);

            // Update Pegawai Golongan and unlock
            $pegawai = $usulan->pegawai;
            $pegawai->update([
                'golongan_id' => $usulan->golongan_baru_id,
                'tmt_golongan' => $request->tmt_golongan_baru,
                'is_locked_usulan' => false,
            ]);

            // Generate Riwayat PAK Transaction for the deduction
            RiwayatPak::create([
                'pegawai_id' => $pegawai->id,
                'no_pak' => 'POTONGAN-SK-' . $request->nomor_sk_baru,
                'tanggal_pak' => $request->tmt_golongan_baru,
                'periode_awal' => $request->tmt_golongan_baru,
                'periode_akhir' => $request->tmt_golongan_baru,
                'ak_dasar' => $usulan->saldo_ak_awal,
                'ak_pengalaman' => 0,
                'ak_tambahan' => -$usulan->potongan_ak,
                'ak_total' => $usulan->sisa_ak,
                'keterangan' => 'Pemotongan AK otomatis Kenaikan Pangkat ke ' . $usulan->golonganBaru->nama_golongan,
                // optional: predikat_kinerja, etc can be null here or a special flag
            ]);

            DB::commit();
            return back()->with('success', 'Persetujuan Kenaikan Pangkat berhasil dieksekusi. Data AK dan Golongan telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }
}
