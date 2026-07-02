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

        // Validate predikat against allowed values
        if (!in_array($performance, KonversiPredikatKinerja::PREDIKAT_OPTIONS)) {
            $performance = 'baik';
        }

        $pegawais = Pegawai::query()
            ->with(['jabatan.konversiPredikat', 'golongan', 'unitKerja', 'riwayatPaks'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('nama_lengkap', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function (Pegawai $pegawai) use ($projectionService, $performance, $targetType) {
                $projection = $projectionService->calculateProjection($pegawai, $performance, $targetType, 6);

                return [
                    'pegawai' => $pegawai,
                    'projection' => $projection,
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

    public function show(Pegawai $pegawai, ProjectionService $projectionService)
    {
        $pegawai->load([
            'jabatan.konversiPredikat',
            'golongan',
            'unitKerja',
            'riwayatPaks' => function ($query) {
                $query->orderBy('tanggal_pak', 'asc');
            }
        ]);

        $projection = $projectionService->calculateProjection($pegawai);

        // Get konversi summary for this pegawai's jabatan
        $konversiSummary = $pegawai->jabatan
            ? $projectionService->getKonversiSummary($pegawai->jabatan->id)
            : [];

        // Calculate all projections (one per predikat) for comparison table
        $projectionComparison = [];
        foreach (KonversiPredikatKinerja::PREDIKAT_OPTIONS as $predikat) {
            $projectionComparison[$predikat] = $projectionService->calculateProjection($pegawai, $predikat);
        }

        $chartYears = $pegawai->riwayatPaks->map(function ($pak) {
            return \Carbon\Carbon::parse($pak->tanggal_pak)->format('Y');
        })->toArray();

        $chartAk = $pegawai->riwayatPaks->pluck('ak_total')->toArray();

        return view('dashboard.proyeksi-jabatan.show', [
            'pegawai' => $pegawai,
            'projection' => $projection,
            'konversiSummary' => $konversiSummary,
            'projectionComparison' => $projectionComparison,
            'chartYears' => $chartYears,
            'chartAk' => $chartAk,
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
            'predikatBadgeClasses' => KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES,
            'menuGroups' => DashboardUiData::menuGroups(),
            'notifications' => DashboardUiData::notifications(),
        ]);
    }
}
