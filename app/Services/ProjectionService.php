<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pegawai;
use Carbon\Carbon;

/**
 * ProjectionService: Career Projection Calculator
 *
 * Calculates civil servant career progression based on Permen PANRB 1/2023 regulations.
 * Determines promotion eligibility by considering both mathematical (AK accumulation) and
 * regulatory (minimum service time) requirements.
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
     * 1. MATHEMATICAL REQUIREMENT: Accumulate sufficient AK based on annual coefficient
     *    and performance multiplier.
     * 2. REGULATORY REQUIREMENT: Serve a minimum of 4 years in the current rank.
     *
     * The projection accounts for the "speed bump" scenario where an employee has achieved
     * the required AK mathematically but must wait the minimum regulatory period before
     * becoming eligible for promotion.
     *
     * CALCULATION FLOW:
     * - Retrieves target AK and annual coefficient from employee's assigned position
     * - Fetches current AK from latest performance evaluation record
     * - Computes annual AK accumulation rate: koefisien × performanceMultiplier
     * - Calculates mathematical years needed to close AK gap
     * - Determines how long employee has served in current rank
     * - Applies regulatory 4-year minimum requirement
     * - Returns projection data indicating both status and timeline
     *
     * @param Pegawai $pegawai The employee to calculate projection for
     * @param float $performanceMultiplier Performance rating multiplier
     *                                       1.0 = Good performance (standard)
     *                                       1.5 = Very Good performance (accelerated)
     * @return array Structured projection data containing:
     *               - current_ak (float): Current accumulated Angka Kredit
     *               - target_ak (float): Required AK for next promotion
     *               - deficit_ak (float): Gap between target and current AK
     *               - estimated_years (int): Years until promotion eligible
     *               - projected_year (int): Expected calendar year of eligibility
     *               - is_ready_mathematically (bool): Deficit is zero or negative
     *               - is_held_by_speedbump (bool): Mathematically ready but waiting for 4-year rule
     *               - progress_percentage (float): Current AK as percentage of target (0-100)
     */
    public function calculateProjection(Pegawai $pegawai, float $performanceMultiplier = 1.0): array
    {
        // Pre-load relationships to avoid N+1 queries
        $pegawai->load('jabatan', 'riwayatPaks');

        // ============================================================================
        // STEP 1: RETRIEVE BASELINE DATA
        // ============================================================================

        $jabatan = $pegawai->jabatan;

        if (!$jabatan) {
            return [
                'current_ak' => 0.0,
                'target_ak' => 0.0,
                'deficit_ak' => 0.0,
                'estimated_years' => 0,
                'projected_year' => (int) now()->year,
                'is_ready_mathematically' => false,
                'is_held_by_speedbump' => false,
                'progress_percentage' => 0.0,
            ];
        }

        $targetAk = (float) ($jabatan->target_ak_kenaikan_pangkat ?? 0);
        $koefisienTahunan = (float) ($jabatan->koefisien_tahunan ?? 1);

        // ============================================================================
        // STEP 2: GET CURRENT AK FROM LATEST EVALUATION
        // ============================================================================

        $currentAk = $this->getCurrentAk($pegawai);

        // ============================================================================
        // STEP 3: CALCULATE AK DEFICIT
        // ============================================================================

        $deficitAk = max(0.0, $targetAk - $currentAk);

        // ============================================================================
        // STEP 4: CALCULATE ANNUAL AK ADDITION RATE
        // ============================================================================

        $annualAddition = $koefisienTahunan * $performanceMultiplier;

        // Safety check: prevent division by zero in next step
        if ($annualAddition <= 0) {
            $annualAddition = 1.0;
        }

        // ============================================================================
        // STEP 5: CALCULATE MATHEMATICAL YEARS NEEDED
        // ============================================================================
        // Using ceil() ensures we round up (5.1 years needed → 6 years)

        $mathematicalYearsNeeded = (int) ceil($deficitAk / $annualAddition);

        // ============================================================================
        // STEP 6: CALCULATE YEARS SERVED IN CURRENT RANK
        // ============================================================================

        $yearsServed = $this->calculateYearsServed($pegawai);

        // ============================================================================
        // STEP 7: APPLY REGULATORY MINIMUM (SPEED BUMP)
        // ============================================================================
        // Permen PANRB 1/2023 requires minimum 4 years in rank before promotion.
        // $remainingMinimumYears is how many years are still needed to satisfy this rule.

        $remainingMinimumYears = max(0, self::MINIMUM_YEARS_IN_RANK - $yearsServed);

        // ============================================================================
        // STEP 8: DETERMINE ACTUAL YEARS NEEDED
        // ============================================================================
        // The projection is the LONGER of:
        // a) Years needed to accumulate sufficient AK, OR
        // b) Years still needed to satisfy 4-year minimum service requirement
        // This prevents promotions before both conditions are met.

        $actualYearsNeeded = max($mathematicalYearsNeeded, $remainingMinimumYears);

        // ============================================================================
        // STEP 9: DETERMINE STATUS FLAGS
        // ============================================================================

        $isReadyMathematically = $deficitAk <= 0;
        $isHeldBySpeedbump = $isReadyMathematically && $remainingMinimumYears > 0;

        // ============================================================================
        // STEP 10: CALCULATE PROGRESS PERCENTAGE
        // ============================================================================
        // Shows current AK as percentage of target. Capped at 100% even if
        // employee has exceeded target AK (in case of retroactive adjustments).

        $progressPercentage = $targetAk > 0
            ? min(100.0, ($currentAk / $targetAk) * 100.0)
            : 100.0;

        // ============================================================================
        // STEP 11: CALCULATE PROJECTED PROMOTION YEAR
        // ============================================================================

        $projectedYear = (int) now()->year + $actualYearsNeeded;

        // ============================================================================
        // RETURN STRUCTURED RESULT
        // ============================================================================

        return [
            'current_ak' => $currentAk,
            'target_ak' => $targetAk,
            'deficit_ak' => $deficitAk,
            'estimated_years' => $actualYearsNeeded,
            'projected_year' => $projectedYear,
            'is_ready_mathematically' => $isReadyMathematically,
            'is_held_by_speedbump' => $isHeldBySpeedbump,
            'progress_percentage' => round($progressPercentage, 2),
        ];
    }

    /**
     * Get the latest Angka Kredit (AK) for an employee.
     *
     * Retrieves the most recent performance evaluation (RiwayatPak) record marked
     * as the latest. If no evaluation history exists, defaults to 0.
     *
     * BUSINESS RULE: Only one RiwayatPak record should have is_latest = true at any time.
     * If multiple records exist, this method returns the most recent by tanggal_pak.
     *
     * @param Pegawai $pegawai The employee
     * @return float Current accumulated AK, minimum 0
     */
    private function getCurrentAk(Pegawai $pegawai): float
    {
        $latestPak = $pegawai->riwayatPaks()
            ->where('is_latest', true)
            ->orderBy('tanggal_pak', 'desc')
            ->first();

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
        if (!$pegawai->tmt_golongan) {
            return 0;
        }

        $yearsServed = (int) now()->diffInYears(
            Carbon::parse($pegawai->tmt_golongan)
        );

        return max(0, $yearsServed);
    }
}
