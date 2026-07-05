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

        // Validate predikat against allowed values
        if (!in_array($performance, KonversiPredikatKinerja::PREDIKAT_OPTIONS)) {
            $performance = 'baik';
        }

        $pegawais = Pegawai::query()
            ->with(['jabatan.konversiPredikat', 'golongan', 'unitKerja', 'riwayatPaks', 'kinerjaTahunans'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('nama_lengkap', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function (Pegawai $pegawai) use ($projectionService, $performance, $targetType, $surplusBehavior) {
                $fullProjection = $projectionService->calculateProjection($pegawai, $performance, $surplusBehavior);
                
                // For index table filtering/stats, we use the specific target type
                $activeProjection = $fullProjection[$targetType];

                return [
                    'pegawai' => $pegawai,
                    'full_projection' => $fullProjection,
                    'projection' => $activeProjection,
                ];
            });

        $filteredPegawais = $pegawais->filter(function (array $item) use ($status) {
            if ($status === 'ready') {
                return $item['projection']['is_ready_mathematically'] && !$item['projection']['is_held_by_speedbump'];
            }

            if ($status === 'waiting') {
                return $item['projection']['is_held_by_speedbump'];
            }

            return true;
        })->values();

        $stats = [
            'total' => $filteredPegawais->count(),
            'ready' => $filteredPegawais->where('projection.is_ready_mathematically', true)->where('projection.is_held_by_speedbump', false)->count(),
            'speedbump' => $filteredPegawais->where('projection.is_held_by_speedbump', true)->count(),
            'avg_progress' => round($filteredPegawais->avg(fn(array $item) => $item['projection']['progress_percentage']) ?? 0, 2),
        ];

        $highlights = $filteredPegawais
            ->sortByDesc(fn(array $item) => $item['projection']['progress_percentage'])
            ->take(3)
            ->values();

        return view('dashboard.proyeksi-jabatan.index', [
            'menuGroups' => DashboardUiData::menuGroups(),
            'notifications' => DashboardUiData::notifications(),
            'search' => $search,
            'status' => $status,
            'performance' => $performance,
            'stats' => $stats,
            'projections' => $filteredPegawais,
            'highlights' => $highlights,
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
            }
        ]);

        $surplusBehavior = (string) $request->input('surplus_behavior', 'hangus');

        // Determine default performance predicate for simulation
        $latestPredikat = $pegawai->kinerjaTahunans->last()?->predikat
            ?? $pegawai->riwayatPaks->last()?->predikat_kinerja 
            ?? 'baik';
            
        if (!in_array($latestPredikat, KonversiPredikatKinerja::PREDIKAT_OPTIONS)) {
            $latestPredikat = 'baik';
        }

        $fullProjection = $projectionService->calculateProjection($pegawai, $latestPredikat, $surplusBehavior);
        // The view will receive the full projection object to render both boxes

        // Get konversi summary for this pegawai's jabatan
        $konversiSummary = $pegawai->jabatan
            ? $projectionService->getKonversiSummary($pegawai->jabatan->id)
            : [];

        // Calculate all projections (one per predikat) for comparison table
        $projectionComparison = [];
        foreach (KonversiPredikatKinerja::PREDIKAT_OPTIONS as $predikat) {
            $projectionComparison[$predikat] = $projectionService->calculateProjection($pegawai, $predikat, $surplusBehavior);
        }

        // Calculate estimation scenarios for the timeline UI (default to pangkat for timeline, can be toggled via JS if needed)
        $estimationScenarios = $projectionService->calculateAllScenarios($pegawai, $latestPredikat, 'pangkat', $surplusBehavior);

        $chartYears = $pegawai->riwayatPaks->map(function ($pak) {
            return \Carbon\Carbon::parse($pak->tanggal_pak)->format('Y');
        })->toArray();

        $chartAk = $pegawai->riwayatPaks->pluck('ak_total')->toArray();

        return view('dashboard.proyeksi-jabatan.show', [
            'pegawai' => $pegawai,
            'full_projection' => $fullProjection,
            'surplusBehavior' => $surplusBehavior,
            'konversiSummary' => $konversiSummary,
            'projectionComparison' => $projectionComparison,
            'estimationScenarios' => $estimationScenarios,
            'chartYears' => $chartYears,
            'chartAk' => $chartAk,
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
            'predikatBadgeClasses' => KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES,
            'menuGroups' => DashboardUiData::menuGroups(),
            'notifications' => DashboardUiData::notifications(),
        ]);
    }
}
