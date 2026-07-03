<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\KonversiPredikatKinerja;
use App\Models\Pegawai;
use Carbon\Carbon;

/**
 * ProjectionService: Career Projection Calculator
 *
 * Calculates civil servant career progression based on Permen PANRB 1/2023 regulations.
 * Determines promotion eligibility by considering both mathematical (AK accumulation) and
 * regulatory (minimum service time) requirements.
 *
 * KEY CHANGE (v2): Now uses database-driven konversi predikat kinerja values instead of
 * hardcoded multipliers. Each jabatan × predikat combination has a specific AK value
 * stored in the konversi_predikat_kinerjas table.
 *
 * @package App\Services
 */
class ProjectionService
{
    /**
     * The minimum years of service required in current rank before promotion eligibility.
     * This is the "regulatory speed bump" that prevents purely mathematical early promotions.
     */
    private const MINIMUM_YEARS_IN_RANK = 4;

    /**
     * Calculate career projection for a civil servant.
     *
     * This method determines when an employee is projected to be eligible for promotion
     * based on their current Angka Kredit (AK/credit score) and the dual requirements
     * established in Permen PANRB 1/2023:
     *
     * 1. MATHEMATICAL REQUIREMENT: Accumulate sufficient AK based on annual AK obtained
     *    from predikat kinerja conversion table (database-driven).
     * 2. REGULATORY REQUIREMENT: Serve a minimum of 4 years in the current rank.
     *
     * CALCULATION FLOW:
     * - Retrieves target AK and annual AK from konversi predikat kinerja table
     * - Fetches current AK from latest performance evaluation record
     * - Computes annual AK accumulation rate from konversi table (not hardcoded multiplier)
     * - Calculates mathematical years needed to close AK gap
     * - Determines how long employee has served in current rank
     * - Applies regulatory 4-year minimum requirement
     * - Returns projection data indicating both status and timeline
     *
     * @param Pegawai $pegawai The employee to calculate projection for
     * @param string $predikat The performance predicate key (e.g. 'baik', 'sangat_baik')
     * @param string $targetType Whether calculating for 'pangkat' or 'jenjang' promotion
     * @param int $periodsPerYear Number of BKN periodisasi per year (default 6)
     * @return array Structured projection data
     */
    public function calculateProjection(
        Pegawai $pegawai,
        string $predikat = 'baik',
        string $targetType = 'pangkat',
        int $periodsPerYear = 6
    ): array {
        // Pre-load relationships to avoid N+1 queries if not already loaded
        $pegawai->loadMissing('jabatan.konversiPredikat', 'riwayatPaks');

        // ============================================================================
        // STEP 1: RETRIEVE BASELINE DATA
        // ============================================================================

        $jabatan = $pegawai->jabatan;

        if (!$jabatan) {
            return $this->emptyProjection();
        }

        // Choose target AK based on requested target type: 'pangkat' or 'jenjang'
        $targetAk = (float) (($targetType === 'jenjang'
            ? $jabatan->target_ak_kenaikan_jenjang
            : $jabatan->target_ak_kenaikan_pangkat) ?? 0);

        // ============================================================================
        // STEP 2: GET ANNUAL AK FROM KONVERSI TABLE (DATABASE-DRIVEN)
        // ============================================================================

        $annualAk = $jabatan->getKonversiByPredikat($predikat);

        // Also get the "Baik" (100%) baseline for comparison display
        $annualAkBaik = $jabatan->getKonversiByPredikat('baik');

        // ============================================================================
        // STEP 3: GET CURRENT AK FROM LATEST EVALUATION
        // ============================================================================

        $currentAk = $this->getCurrentAk($pegawai);

        // ============================================================================
        // STEP 4: CALCULATE AK DEFICIT
        // ============================================================================

        $deficitAk = max(0.0, $targetAk - $currentAk);

        // ============================================================================
        // STEP 5: CALCULATE PERIOD-BASED ADDITION
        // ============================================================================

        // Convert annual AK to per-period addition
        $periodAddition = $annualAk / max(1, $periodsPerYear);

        // Progress percentage (calculate early so it's available for error returns)
        $progressPercentage = $targetAk > 0
            ? min(100.0, ($currentAk / $targetAk) * 100.0)
            : 100.0;

        // If periodAddition is zero or negative then calculation cannot proceed accurately
        if ($periodAddition <= 0.0) {
            return [
                'current_ak' => $currentAk,
                'target_ak' => $targetAk,
                'deficit_ak' => $deficitAk,
                'annual_ak' => $annualAk,
                'annual_ak_baik' => $annualAkBaik,
                'predikat' => $predikat,
                'predikat_label' => KonversiPredikatKinerja::labelFor($predikat),
                'estimated_periods' => null,
                'estimated_years' => null,
                'projected_year' => (int) now()->year,
                'is_ready_mathematically' => $deficitAk <= 0,
                'is_held_by_speedbump' => false,
                'progress_percentage' => round($progressPercentage, 2),
                'error' => 'invalid_coefficient',
            ];
        }

        // ============================================================================
        // STEP 6: CALCULATE MATHEMATICAL PERIODS NEEDED
        // ============================================================================

        $mathematicalPeriodsNeeded = (int) ceil($deficitAk / $periodAddition);

        // ============================================================================
        // STEP 7: CALCULATE YEARS SERVED IN CURRENT RANK
        // ============================================================================

        $yearsServed = $this->calculateYearsServed($pegawai);

        // Convert years served to periods served
        $periodsServed = (int) floor($yearsServed * $periodsPerYear);

        // Remaining minimum periods to satisfy regulatory 4-year requirement
        $remainingMinimumPeriods = max(0, (self::MINIMUM_YEARS_IN_RANK * $periodsPerYear) - $periodsServed);

        // Determine actual periods needed (max of mathematical vs regulatory)
        $actualPeriodsNeeded = max($mathematicalPeriodsNeeded, $remainingMinimumPeriods);

        // Status flags
        $isReadyMathematically = $deficitAk <= 0;
        $isHeldBySpeedbump = $isReadyMathematically && $remainingMinimumPeriods > 0;

        // Projected year (approx) — convert periods to years and add to current year
        $projectedYear = (int) now()->year + (int) ceil($actualPeriodsNeeded / max(1, $periodsPerYear));

        return [
            'current_ak' => $currentAk,
            'target_ak' => $targetAk,
            'deficit_ak' => $deficitAk,
            'annual_ak' => $annualAk,
            'annual_ak_baik' => $annualAkBaik,
            'predikat' => $predikat,
            'predikat_label' => KonversiPredikatKinerja::labelFor($predikat),
            'estimated_periods' => $actualPeriodsNeeded,
            'estimated_years' => round($actualPeriodsNeeded / max(1, $periodsPerYear), 2),
            'projected_year' => $projectedYear,
            'is_ready_mathematically' => $isReadyMathematically,
            'is_held_by_speedbump' => $isHeldBySpeedbump,
            'progress_percentage' => round($progressPercentage, 2),
        ];
    }

    /**
     * Get the conversion table summary for a jabatan across all predikats.
     *
     * Returns an array of konversi data for display in UI tables.
     *
     * @param int $jabatanId
     * @return array<string, array{predikat: string, label: string, persentase: float, nilai_ak: float}>
     */
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

    /**
     * Calculate projection scenarios for ALL predikats.
     *
     * Returns a structured array for the estimation timeline UI, showing
     * how long it takes to reach target AK under each predikat scenario.
     * Automatically detects the fastest and slowest scenario.
     *
     * @param Pegawai $pegawai The employee
     * @param string $activePredikat The currently active/selected predikat
     * @return array{scenarios: array, current_ak: float, target_ak: float, active_predikat: string}
     */
    public function calculateAllScenarios(Pegawai $pegawai, string $activePredikat = 'baik'): array
    {
        $pegawai->loadMissing('jabatan.konversiPredikat', 'riwayatPaks');

        $jabatan = $pegawai->jabatan;
        if (!$jabatan) {
            return ['scenarios' => [], 'current_ak' => 0, 'target_ak' => 0, 'active_predikat' => $activePredikat];
        }

        $targetAk = (float) ($jabatan->target_ak_kenaikan_pangkat ?? 0);
        $currentAk = $this->getCurrentAk($pegawai);
        $deficitAk = max(0.0, $targetAk - $currentAk);
        $currentYear = (int) now()->year;

        // Color palette for each predikat (for the estimation bars)
        $colors = [
            'sangat_baik'     => '#059669', // emerald
            'baik'            => '#4f46e5', // indigo
            'butuh_perbaikan' => '#d97706', // amber
            'kurang'          => '#dc2626', // red
            'sangat_kurang'   => '#7c3aed', // violet
        ];

        $scenarios = [];
        $minYears = PHP_INT_MAX;
        $maxYears = 0;

        foreach (KonversiPredikatKinerja::PREDIKAT_OPTIONS as $predikat) {
            $annualAk = $jabatan->getKonversiByPredikat($predikat);
            $label = KonversiPredikatKinerja::labelFor($predikat);

            if ($annualAk <= 0) {
                $scenarios[$predikat] = [
                    'predikat'       => $predikat,
                    'label'          => $label,
                    'annual_ak'      => 0,
                    'years_needed'   => null,
                    'projected_year' => null,
                    'is_active'      => $predikat === $activePredikat,
                    'is_fastest'     => false,
                    'is_slowest'     => false,
                    'is_ready'       => $deficitAk <= 0,
                    'color'          => $colors[$predikat] ?? '#6b7280',
                    'badge_class'    => KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES[$predikat] ?? '',
                ];
                continue;
            }

            // If already at target, years needed is 0
            if ($deficitAk <= 0) {
                $yearsNeeded = 0;
            } else {
                $yearsNeeded = (int) ceil($deficitAk / $annualAk);
            }

            $projectedYear = $currentYear + $yearsNeeded;

            if ($yearsNeeded < $minYears) {
                $minYears = $yearsNeeded;
            }
            if ($yearsNeeded > $maxYears) {
                $maxYears = $yearsNeeded;
            }

            $scenarios[$predikat] = [
                'predikat'       => $predikat,
                'label'          => $label,
                'annual_ak'      => round($annualAk, 3),
                'years_needed'   => $yearsNeeded,
                'projected_year' => $projectedYear,
                'is_active'      => $predikat === $activePredikat,
                'is_fastest'     => false, // will be set below
                'is_slowest'     => false,
                'is_ready'       => $deficitAk <= 0,
                'color'          => $colors[$predikat] ?? '#6b7280',
                'badge_class'    => KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES[$predikat] ?? '',
            ];
        }

        // Mark fastest and slowest
        foreach ($scenarios as $key => &$scenario) {
            if ($scenario['years_needed'] !== null) {
                $scenario['is_fastest'] = ($scenario['years_needed'] === $minYears);
                $scenario['is_slowest'] = ($scenario['years_needed'] === $maxYears && $maxYears !== $minYears);
            }
        }
        unset($scenario);

        return [
            'scenarios'       => $scenarios,
            'current_ak'      => $currentAk,
            'target_ak'       => $targetAk,
            'deficit_ak'      => $deficitAk,
            'active_predikat' => $activePredikat,
            'max_years'       => $maxYears > 0 ? $maxYears : 1,
        ];
    }

    /**
     * Return an empty projection array (no jabatan assigned).
     */
    private function emptyProjection(): array
    {
        return [
            'current_ak' => 0.0,
            'target_ak' => 0.0,
            'deficit_ak' => 0.0,
            'annual_ak' => 0.0,
            'annual_ak_baik' => 0.0,
            'predikat' => 'baik',
            'predikat_label' => 'Baik',
            'estimated_years' => 0,
            'projected_year' => (int) now()->year,
            'is_ready_mathematically' => false,
            'is_held_by_speedbump' => false,
            'progress_percentage' => 0.0,
        ];
    }

    /**
     * Get the latest Angka Kredit (AK) for an employee.
     *
     * Retrieves the most recent performance evaluation (RiwayatPak) record
     * auto-detected by tanggal_pak DESC, id DESC. No manual flag needed.
     *
     * @param Pegawai $pegawai The employee
     * @return float Current accumulated AK, minimum 0
     */
    private function getCurrentAk(Pegawai $pegawai): float
    {
        if ($pegawai->relationLoaded('riwayatPaks')) {
            $latestPak = $pegawai->riwayatPaks->sortBy([
                ['tanggal_pak', 'desc'],
                ['id', 'desc'],
            ])->first();
        } else {
            $latestPak = $pegawai->riwayatPaks()
                ->latestPak()
                ->first();
        }

        return $latestPak ? (float) $latestPak->ak_total : 0.0;
    }

    /**
     * Calculate years served in current rank (golongan).
     *
     * Computes the time difference between the employee's assignment date to the
     * current rank (tmt_golongan) and today. This value determines how much longer
     * the employee must wait to satisfy the 4-year minimum service requirement.
     *
     * EDGE CASES:
     * - If tmt_golongan is null: returns 0 (employee has no start date in rank)
     * - If tmt_golongan is in the future: returns 0 (not yet assigned)
     *
     * @param Pegawai $pegawai The employee
     * @return int Years served in current rank, minimum 0
     */
    private function calculateYearsServed(Pegawai $pegawai): int
    {
        $tmt = $pegawai->tmt_golongan ?? $pegawai->tmt_jabatan;

        if (!$tmt) {
            return 0;
        }

        $yearsServed = (int) Carbon::parse($tmt)->diffInYears(now());

        return max(0, $yearsServed);
    }
}
