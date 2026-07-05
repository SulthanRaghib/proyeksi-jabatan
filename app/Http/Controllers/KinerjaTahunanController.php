<?php

namespace App\Http\Controllers;

use App\Models\KinerjaTahunan;
use App\Models\Pegawai;
use App\Models\KonversiPredikatKinerja;
use Illuminate\Http\Request;
use App\Support\DashboardUiData;

class KinerjaTahunanController extends Controller
{
    public function create(Request $request)
    {
        $pegawaiId = $request->input('pegawai_id');
        $pegawai = Pegawai::with('jabatan.konversiPredikat')->findOrFail($pegawaiId);
        
        return view('dashboard.kinerja-tahunans.create', [
            'pegawai' => $pegawai,
            'predikatOptions' => KonversiPredikatKinerja::PREDIKAT_OPTIONS,
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
            'menuGroups' => DashboardUiData::menuGroups(),
            'notifications' => DashboardUiData::notifications(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'tahun' => 'required|integer|min:2000|max:2099',
            'predikat' => 'required|string|in:' . implode(',', KonversiPredikatKinerja::PREDIKAT_OPTIONS),
        ]);

        $pegawai = Pegawai::with('jabatan')->findOrFail($validated['pegawai_id']);
        $jabatan = $pegawai->jabatan;

        $koefisien = $jabatan->koefisien_tahunan;
        $akDidapat = $jabatan->getKonversiByPredikat($validated['predikat']);

        if (KinerjaTahunan::where('pegawai_id', $pegawai->id)->where('tahun', $validated['tahun'])->exists()) {
            return back()->with('error', 'Data Kinerja untuk tahun tersebut sudah ada.')->withInput();
        }

        KinerjaTahunan::create([
            'pegawai_id' => $pegawai->id,
            'tahun' => $validated['tahun'],
            'predikat' => $validated['predikat'],
            'koefisien_saat_itu' => $koefisien,
            'ak_didapat' => $akDidapat,
        ]);

        return redirect()->route('projections.show', $pegawai)->with('success', 'Riwayat Kinerja Tahunan berhasil ditambahkan.');
    }

    public function edit(KinerjaTahunan $kinerjaTahunan)
    {
        $kinerjaTahunan->load('pegawai.jabatan');
        
        return view('dashboard.kinerja-tahunans.edit', [
            'kinerjaTahunan' => $kinerjaTahunan,
            'pegawai' => $kinerjaTahunan->pegawai,
            'predikatOptions' => KonversiPredikatKinerja::PREDIKAT_OPTIONS,
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
            'menuGroups' => DashboardUiData::menuGroups(),
            'notifications' => DashboardUiData::notifications(),
        ]);
    }

    public function update(Request $request, KinerjaTahunan $kinerjaTahunan)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'predikat' => 'required|string|in:' . implode(',', KonversiPredikatKinerja::PREDIKAT_OPTIONS),
        ]);

        if (KinerjaTahunan::where('pegawai_id', $kinerjaTahunan->pegawai_id)
            ->where('tahun', $validated['tahun'])
            ->where('id', '!=', $kinerjaTahunan->id)
            ->exists()) {
            return back()->with('error', 'Data Kinerja untuk tahun tersebut sudah ada.')->withInput();
        }

        $jabatan = $kinerjaTahunan->pegawai->jabatan;
        $akDidapat = $jabatan->getKonversiByPredikat($validated['predikat']);

        $kinerjaTahunan->update([
            'tahun' => $validated['tahun'],
            'predikat' => $validated['predikat'],
            'ak_didapat' => $akDidapat,
        ]);

        return redirect()->route('projections.show', $kinerjaTahunan->pegawai_id)->with('success', 'Riwayat Kinerja Tahunan berhasil diperbarui.');
    }

    public function destroy(KinerjaTahunan $kinerjaTahunan)
    {
        $pegawaiId = $kinerjaTahunan->pegawai_id;
        $kinerjaTahunan->delete();

        return redirect()->route('projections.show', $pegawaiId)->with('success', 'Riwayat Kinerja Tahunan berhasil dihapus.');
    }
}
