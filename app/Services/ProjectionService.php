<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\KonversiPredikatKinerja;
use App\Models\Pegawai;
use Carbon\Carbon;

class ProjectionService
{
    private const MINIMUM_YEARS_IN_RANK = 2; // For both Pangkat and Jenjang

    /**
     * Calculates the full projection for an employee, returning both 'pangkat' and 'jenjang'.
     */
    public function calculateProjection(
        Pegawai $pegawai,
        string $predikat = 'baik',
        string $surplusBehavior = 'hangus'
    ): array {
        $pegawai->loadMissing(['jabatan.konversiPredikat', 'riwayatPaks', 'kinerjaTahunans']);

        return [
            'pangkat' => $this->calculateTarget($pegawai, $predikat, 'pangkat', $surplusBehavior),
            'jenjang' => $this->calculateTarget($pegawai, $predikat, 'jenjang', $surplusBehavior),
            'surplus_behavior' => $surplusBehavior,
        ];
    }

    private function calculateTarget(
        Pegawai $pegawai,
        string $assumedPredikat,
        string $targetType,
        string $surplusBehavior
    ): array {
        $jabatan = $pegawai->jabatan;

        if (!$jabatan) {
            return $this->emptyProjection();
        }

        $targetAk = (float) (($targetType === 'jenjang'
            ? $jabatan->target_ak_kenaikan_jenjang
            : $jabatan->target_ak_kenaikan_pangkat) ?? 0);

        // SPEK LOGIKA 1.2: Opsi A - Menghitung AK secara dinamis sejak TMT terakhir
        $tmt = $targetType === 'pangkat' ? $pegawai->tmt_golongan : $pegawai->tmt_jabatan;
        $tmtDate = $tmt ? Carbon::parse($tmt) : null;
        
        $currentAk = 0.0;

        // 1. Add AK from PAKs that happened ON or AFTER the TMT
        if ($pegawai->relationLoaded('riwayatPaks') && $tmtDate) {
            $paks = $pegawai->riwayatPaks->filter(function($pak) use ($tmtDate) {
                return Carbon::parse($pak->tanggal_pak)->gte($tmtDate);
            });
            foreach ($paks as $pak) {
                if ($pak->is_konversi_baru) {
                    $currentAk += (float) $pak->ak_tambahan;
                }
            }
        }

        // Logika Pengunci Teraman (Anti-Null Bug) untuk Tahun SKP
        $lastPak = null;
        if ($pegawai->relationLoaded('riwayatPaks') && $tmtDate) {
            $lastPak = $pegawai->riwayatPaks
                ->filter(function($pak) use ($tmtDate) {
                    return Carbon::parse($pak->tanggal_pak)->gte($tmtDate);
                })
                ->sortByDesc('tanggal_pak')
                ->first();
        }

        if ($lastPak) {
            if (!is_null($lastPak->periode_akhir)) {
                $lastKinerjaYear = Carbon::parse($lastPak->periode_akhir)->year;
            } else {
                $lastKinerjaYear = Carbon::parse($lastPak->tanggal_pak)->year;
            }
        } else {
            $lastKinerjaYear = $tmtDate ? (int) $tmtDate->year : ((int) now()->year - 1);
        }

        // 2. Add AK from Kinerja Tahunan that happened AFTER the last calculated year
        if ($pegawai->relationLoaded('kinerjaTahunans')) {
            $kinerjas = $pegawai->kinerjaTahunans->where('tahun', '>', $lastKinerjaYear)->sortBy('tahun');
            foreach ($kinerjas as $kinerja) {
                $currentAk += (float) $kinerja->ak_didapat;
                $lastKinerjaYear = $kinerja->tahun;
            }
        }
        
        $surplusAk = 0.0;
        $deficitAk = $targetAk - $currentAk;

        if ($deficitAk < 0) {
            $surplusAk = abs($deficitAk);
            $deficitAk = 0.0;
        }

        $annualAk = $jabatan->getKonversiByPredikat($assumedPredikat);
        $annualAkBaik = $jabatan->getKonversiByPredikat('baik');

        $progressPercentage = $targetAk > 0 ? min(100.0, ($currentAk / $targetAk) * 100.0) : 100.0;

        if ($annualAk <= 0.0) {
            return $this->emptyProjection($currentAk, $targetAk, $deficitAk, $progressPercentage);
        }

        // Years needed mathematically
        $mathematicalYearsNeeded = (int) ceil($deficitAk / $annualAk);

        // Regulatory constraints
        // Pangkat: min 2 years from tmt_golongan
        // Jenjang: min 2 years from tmt_jabatan
        $tmt = $targetType === 'pangkat' ? $pegawai->tmt_golongan : $pegawai->tmt_jabatan;
        $yearsServed = $tmt ? (int) Carbon::parse($tmt)->diffInYears(now()) : 0;
        $remainingMinimumYears = max(0, self::MINIMUM_YEARS_IN_RANK - $yearsServed);

        $actualYearsNeeded = max($mathematicalYearsNeeded, $remainingMinimumYears);

        // The projected year starts from the last evaluated year, or current year
        $startYearForProjection = max((int) now()->year, $lastKinerjaYear);
        $projectedYear = $startYearForProjection + $actualYearsNeeded;

        $isReadyMathematically = $deficitAk <= 0;
        $isHeldBySpeedbump = $isReadyMathematically && $remainingMinimumYears > 0;
        $isHeldByUkom = false;

        // Jenjang requires Ukom
        if ($targetType === 'jenjang' && $isReadyMathematically && !$isHeldBySpeedbump) {
            if (!$pegawai->status_ukom) {
                $isHeldByUkom = true;
            }
        }

        $isFullyReady = $isReadyMathematically && !$isHeldBySpeedbump && !$isHeldByUkom;

        // Resolve target names
        $currentTargetName = '-';
        $nextTargetName = '-';
        $nextGolonganId = null;
        $isPangkatPuncak = false;
        
        if ($targetType === 'pangkat' && $pegawai->golongan) {
            $currentTargetName = $pegawai->golongan->nama_golongan;
            $nextGolongan = \App\Models\Golongan::where('id', '>', $pegawai->golongan_id)->orderBy('id')->first();
            $nextTargetName = $nextGolongan ? $nextGolongan->nama_golongan : 'Maksimal';
            $nextGolonganId = $nextGolongan ? $nextGolongan->id : null;
            
            // Cek Pangkat Puncak berdasarkan Pangkat/Golongan
            $pangkatPuncakList = ['III/b', 'III/d', 'IV/c', 'IV/e', 'II/d'];
            if (in_array($pegawai->golongan->pangkat, $pangkatPuncakList)) {
                $isPangkatPuncak = true;
            }
        } elseif ($targetType === 'jenjang' && $jabatan) {
            $currentTargetName = $jabatan->jenjang;
            $nextJabatan = \App\Models\Jabatan::where('kategori', $jabatan->kategori)->where('id', '>', $jabatan->id)->orderBy('id')->first();
            $nextTargetName = $nextJabatan ? $nextJabatan->jenjang : 'Maksimal';
        }

        return [
            'current_ak' => round($currentAk, 3),
            'target_ak' => round($targetAk, 3),
            'deficit_ak' => round($deficitAk, 3),
            'surplus_ak' => round($surplusAk, 3),
            'annual_ak' => round($annualAk, 3),
            'annual_ak_baik' => round($annualAkBaik, 3),
            'predikat' => $assumedPredikat,
            'predikat_label' => KonversiPredikatKinerja::labelFor($assumedPredikat),
            'estimated_years' => $actualYearsNeeded,
            'projected_year' => $projectedYear,
            'is_ready_mathematically' => $isReadyMathematically,
            'is_held_by_speedbump' => $isHeldBySpeedbump,
            'is_held_by_ukom' => $isHeldByUkom,
            'is_fully_ready' => $isFullyReady,
            'progress_percentage' => round($progressPercentage, 2),
            'tmt_used' => $tmt ? $tmt->format('d/m/Y') : '-',
            'years_served' => $yearsServed,
            'current_target_name' => $currentTargetName,
            'next_target_name' => $nextTargetName,
            'next_golongan_id' => $nextGolonganId,
            'is_pangkat_puncak' => $isPangkatPuncak,
            'is_sedang_hukuman' => $pegawai->sedang_hukuman_disiplin,
            'is_locked_usulan' => $pegawai->is_locked_usulan,
        ];
    }

    private function getLatestPak(Pegawai $pegawai)
    {
        if ($pegawai->relationLoaded('riwayatPaks')) {
            return $pegawai->riwayatPaks->sortBy([
                ['tanggal_pak', 'desc'],
                ['id', 'desc'],
            ])->first();
        }
        return $pegawai->riwayatPaks()->latestPak()->first();
    }

    private function emptyProjection($currentAk = 0.0, $targetAk = 0.0, $deficitAk = 0.0, $progress = 0.0): array
    {
        return [
            'current_ak' => $currentAk,
            'target_ak' => $targetAk,
            'deficit_ak' => $deficitAk,
            'surplus_ak' => 0.0,
            'annual_ak' => 0.0,
            'annual_ak_baik' => 0.0,
            'predikat' => 'baik',
            'predikat_label' => 'Baik',
            'estimated_years' => 0,
            'projected_year' => (int) now()->year,
            'is_ready_mathematically' => false,
            'is_held_by_speedbump' => false,
            'is_held_by_ukom' => false,
            'is_fully_ready' => false,
            'progress_percentage' => $progress,
            'tmt_used' => '-',
            'years_served' => 0,
            'current_target_name' => '-',
            'next_target_name' => '-',
        ];
    }

    // Include the remaining methods from original: getKonversiSummary, calculateAllScenarios
    
    public function getKonversiSummary(int $jabatanId): array
    {
        $konversis = KonversiPredikatKinerja::query()
            ->where('jabatan_id', $jabatanId)
            ->orderByRaw("FIELD(predikat, 'sangat_baik', 'baik', 'butuh_perbaikan', 'kurang', 'sangat_kurang')")
            ->get();

        return $konversis->map(fn(KonversiPredikatKinerja $k) => [
            'predikat' => $k->predikat,
            'label' => $k->predikat_label,
            'persentase' => (float) $k->persentase,
            'nilai_ak' => (float) $k->nilai_ak,
            'badge_class' => $k->predikat_badge_class,
        ])->keyBy('predikat')->toArray();
    }

    public function calculateAllScenarios(Pegawai $pegawai, string $activePredikat = 'baik', string $targetType = 'pangkat', string $surplusBehavior = 'hangus'): array
    {
        $pegawai->loadMissing(['jabatan.konversiPredikat', 'riwayatPaks', 'kinerjaTahunans']);
        
        $jabatan = $pegawai->jabatan;
        if (!$jabatan) {
            return ['scenarios' => [], 'current_ak' => 0, 'target_ak' => 0, 'active_predikat' => $activePredikat];
        }

        // Use calculateTarget for the active one to get baseline data
        $baseTarget = $this->calculateTarget($pegawai, $activePredikat, $targetType, $surplusBehavior);

        $colors = [
            'sangat_baik'     => '#059669',
            'baik'            => '#4f46e5',
            'butuh_perbaikan' => '#d97706',
            'kurang'          => '#dc2626',
            'sangat_kurang'   => '#7c3aed',
        ];

        $scenarios = [];
        $minYears = PHP_INT_MAX;
        $maxYears = 0;

        foreach (KonversiPredikatKinerja::PREDIKAT_OPTIONS as $predikat) {
            $scenarioTarget = $this->calculateTarget($pegawai, $predikat, $targetType, $surplusBehavior);
            $annualAk = $scenarioTarget['annual_ak'];
            $yearsNeeded = $scenarioTarget['estimated_years'];
            $projectedYear = $scenarioTarget['projected_year'];
            $label = KonversiPredikatKinerja::labelFor($predikat);
            
            if ($annualAk <= 0) {
                $yearsNeeded = null;
                $projectedYear = null;
            } else {
                if ($yearsNeeded < $minYears) $minYears = $yearsNeeded;
                if ($yearsNeeded > $maxYears) $maxYears = $yearsNeeded;
            }

            $scenarios[$predikat] = [
                'predikat'       => $predikat,
                'label'          => $label,
                'annual_ak'      => $annualAk,
                'years_needed'   => $yearsNeeded,
                'projected_year' => $projectedYear,
                'is_active'      => $predikat === $activePredikat,
                'is_fastest'     => false,
                'is_slowest'     => false,
                'is_ready'       => $scenarioTarget['is_fully_ready'],
                'color'          => $colors[$predikat] ?? '#6b7280',
                'badge_class'    => KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES[$predikat] ?? '',
            ];
        }

        foreach ($scenarios as $key => &$scenario) {
            if ($scenario['years_needed'] !== null) {
                $scenario['is_fastest'] = ($scenario['years_needed'] === $minYears);
                $scenario['is_slowest'] = ($scenario['years_needed'] === $maxYears && $maxYears !== $minYears);
            }
        }
        unset($scenario);

        return [
            'scenarios'       => $scenarios,
            'current_ak'      => $baseTarget['current_ak'],
            'target_ak'       => $baseTarget['target_ak'],
            'deficit_ak'      => $baseTarget['deficit_ak'],
            'active_predikat' => $activePredikat,
            'max_years'       => $maxYears > 0 ? $maxYears : 1,
            'target_type'     => $targetType,
        ];
    }

    /**
     * Gets filtered projections for the index page.
     *
     * @param string $search
     * @param string $status
     * @param string $performance
     * @param string $targetType
     * @param string $surplusBehavior
     * @return array
     */
    public function getFilteredProjections(string $search, string $status, string $performance, string $targetType, string $surplusBehavior): array
    {
        if (!in_array($performance, KonversiPredikatKinerja::PREDIKAT_OPTIONS)) {
            $performance = 'baik';
        }

        $pegawais = Pegawai::query()
            ->with(['jabatan.konversiPredikat', 'golongan', 'unitKerja', 'riwayatPaks', 'kinerjaTahunans', 'activeUsulan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('nama_lengkap', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function (Pegawai $pegawai) use ($performance, $targetType, $surplusBehavior) {
                $fullProjection = $this->calculateProjection($pegawai, $performance, $surplusBehavior);
                
                return [
                    'pegawai' => $pegawai,
                    'full_projection' => $fullProjection,
                    'projection' => $fullProjection[$targetType],
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

        return [
            'projections' => $filteredPegawais,
            'stats' => $stats,
            'highlights' => $highlights,
        ];
    }

    /**
     * Prepares data for the show chart.
     *
     * @param Pegawai $pegawai
     * @return array
     */
    public function getChartData(Pegawai $pegawai): array
    {
        $chartYears = $pegawai->riwayatPaks->map(function ($pak) {
            return \Carbon\Carbon::parse($pak->tanggal_pak)->format('Y');
        })->toArray();

        $chartAk = $pegawai->riwayatPaks->pluck('ak_total')->toArray();
        
        $chartPredikat = $pegawai->riwayatPaks->map(function ($pak) {
            return ucwords(str_replace('_', ' ', $pak->predikat_kinerja ?? '-'));
        })->toArray();

        $chartAkTambahan = $pegawai->riwayatPaks->pluck('ak_tambahan')->toArray();

        return [
            'years' => $chartYears,
            'ak' => $chartAk,
            'predikat' => $chartPredikat,
            'ak_tambahan' => $chartAkTambahan,
        ];
    }
}
