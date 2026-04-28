<?php

namespace App\Http\Controllers;

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

        // Map performance label to multiplier
        $performanceMap = [
            'sangat_baik' => 1.5,
            'baik' => 1.0,
            'butuh_perbaikan' => 0.75,
            'kurang' => 0.5,
            'sangat_kurang' => 0.25,
        ];

        $multiplier = $performanceMap[$performance] ?? 1.0;

        $pegawais = Pegawai::query()
            ->with(['jabatan', 'golongan', 'unitKerja', 'riwayatPaks' => function ($query) {
                $query->where('is_latest', true);
            }])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('nama_lengkap', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function (Pegawai $pegawai) use ($projectionService, $multiplier, $targetType) {
                $projection = $projectionService->calculateProjection($pegawai, $multiplier, $targetType, 6);

                return [
                    'pegawai' => $pegawai,
                    'projection' => $projection,
                ];
            });

        $filteredPegawais = $pegawais->filter(function (array $item) use ($status) {
            if ($status === 'ready') {
                return $item['projection']['is_ready_mathematically'];
            }

            if ($status === 'waiting') {
                return $item['projection']['is_held_by_speedbump'];
            }

            return true;
        })->values();

        $stats = [
            'total' => $filteredPegawais->count(),
            'ready' => $filteredPegawais->where('projection.is_ready_mathematically', true)->count(),
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
            'stats' => $stats,
            'projections' => $filteredPegawais,
            'highlights' => $highlights,
        ]);
    }
}
