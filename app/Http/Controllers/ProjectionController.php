<?php

namespace App\Http\Controllers;

use App\Models\KonversiPredikatKinerja;
use App\Models\Pegawai;
use App\Services\ProjectionService;
use App\Support\DashboardUiData;
use Illuminate\Http\Request;

class ProjectionController extends Controller
{
    public function index(Request $request, ProjectionService $projectionService)
    {
        $search = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');
        $performance = (string) $request->input('performance', 'baik');
        $targetType = (string) $request->input('target', 'pangkat');
        $surplusBehavior = (string) $request->input('surplus_behavior', 'hangus');

        $data = $projectionService->getFilteredProjections($search, $status, $performance, $targetType, $surplusBehavior);

        return view('dashboard.proyeksi-jabatan.index', [
            'menuGroups' => DashboardUiData::menuGroups(),
            'notifications' => DashboardUiData::notifications(),
            'search' => $search,
            'status' => $status,
            'performance' => $performance,
            'stats' => $data['stats'],
            'projections' => $data['projections'],
            'highlights' => $data['highlights'],
            'predikatOptions' => KonversiPredikatKinerja::PREDIKAT_OPTIONS,
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
        ]);
    }

    public function show(Request $request, Pegawai $pegawai, ProjectionService $projectionService)
    {
        $pegawai->load([
            'jabatan.konversiPredikat',
            'golongan',
            'unitKerja',
            'riwayatPaks' => function ($query) {
                $query->orderBy('tanggal_pak', 'asc');
            },
            'kinerjaTahunans' => function ($query) {
                $query->orderBy('tahun', 'asc');
            },
            'activeUsulan'
        ]);

        $surplusBehavior = (string) $request->input('surplus_behavior', 'hangus');

        $latestPredikat = $pegawai->kinerjaTahunans->last()?->predikat
            ?? $pegawai->riwayatPaks->last()?->predikat_kinerja 
            ?? 'baik';
            
        if (!in_array($latestPredikat, KonversiPredikatKinerja::PREDIKAT_OPTIONS)) {
            $latestPredikat = 'baik';
        }

        $fullProjection = $projectionService->calculateProjection($pegawai, $latestPredikat, $surplusBehavior);

        $konversiSummary = $pegawai->jabatan
            ? $projectionService->getKonversiSummary($pegawai->jabatan->id)
            : [];

        $projectionComparison = [];
        foreach (KonversiPredikatKinerja::PREDIKAT_OPTIONS as $predikat) {
            $projectionComparison[$predikat] = $projectionService->calculateProjection($pegawai, $predikat, $surplusBehavior);
        }

        $estimationScenarios = $projectionService->calculateAllScenarios($pegawai, $latestPredikat, 'pangkat', $surplusBehavior);

        $chartData = $projectionService->getChartData($pegawai);

        return view('dashboard.proyeksi-jabatan.show', [
            'pegawai' => $pegawai,
            'full_projection' => $fullProjection,
            'surplusBehavior' => $surplusBehavior,
            'konversiSummary' => $konversiSummary,
            'projectionComparison' => $projectionComparison,
            'estimationScenarios' => $estimationScenarios,
            'chartYears' => $chartData['years'],
            'chartAk' => $chartData['ak'],
            'chartPredikat' => $chartData['predikat'],
            'chartAkTambahan' => $chartData['ak_tambahan'],
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
            'predikatBadgeClasses' => KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES,
            'menuGroups' => DashboardUiData::menuGroups(),
            'notifications' => DashboardUiData::notifications(),
        ]);
    }
}
