<?php

namespace App\Http\Controllers;

use App\Models\KinerjaTahunan;
use App\Models\Pegawai;
use App\Models\KonversiPredikatKinerja;
use Illuminate\Http\Request;
use App\Support\DashboardUiData;
use App\Http\Requests\StoreKinerjaTahunanRequest;
use App\Http\Requests\UpdateKinerjaTahunanRequest;
use App\Services\KinerjaTahunanService;
use Exception;

class KinerjaTahunanController extends Controller
{
    protected $kinerjaService;

    public function __construct(KinerjaTahunanService $kinerjaService)
    {
        $this->kinerjaService = $kinerjaService;
    }

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

    public function store(StoreKinerjaTahunanRequest $request)
    {
        try {
            $kinerjaTahunan = $this->kinerjaService->storeKinerja($request->validated());
            return redirect()->route('projections.show', $kinerjaTahunan->pegawai_id)
                ->with('success', 'Riwayat Kinerja Tahunan berhasil ditambahkan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
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

    public function update(UpdateKinerjaTahunanRequest $request, KinerjaTahunan $kinerjaTahunan)
    {
        try {
            $updatedKinerja = $this->kinerjaService->updateKinerja($kinerjaTahunan, $request->validated());
            return redirect()->route('projections.show', $updatedKinerja->pegawai_id)
                ->with('success', 'Riwayat Kinerja Tahunan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(KinerjaTahunan $kinerjaTahunan)
    {
        $pegawaiId = $kinerjaTahunan->pegawai_id;
        $this->kinerjaService->deleteKinerja($kinerjaTahunan);

        return redirect()->route('projections.show', $pegawaiId)->with('success', 'Riwayat Kinerja Tahunan berhasil dihapus.');
    }
}
