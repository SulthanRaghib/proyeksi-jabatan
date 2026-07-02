<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonversiPredikatKinerja extends Model
{
    use HasFactory;

    protected $table = 'konversi_predikat_kinerjas';

    /**
     * Available predikat kinerja values (enum values in DB).
     */
    public const PREDIKAT_OPTIONS = [
        'sangat_baik',
        'baik',
        'butuh_perbaikan',
        'kurang',
        'sangat_kurang',
    ];

    /**
     * Human-readable labels for each predikat.
     */
    public const PREDIKAT_LABELS = [
        'sangat_baik' => 'Sangat Baik',
        'baik' => 'Baik',
        'butuh_perbaikan' => 'Butuh Perbaikan',
        'kurang' => 'Kurang',
        'sangat_kurang' => 'Sangat Kurang',
    ];

    /**
     * Default percentage multipliers per predikat (Permen PANRB 1/2023).
     */
    public const PREDIKAT_PERSENTASE = [
        'sangat_baik' => 150.00,
        'baik' => 100.00,
        'butuh_perbaikan' => 75.00,
        'kurang' => 50.00,
        'sangat_kurang' => 25.00,
    ];

    /**
     * Badge CSS classes for each predikat (Bootstrap 5).
     */
    public const PREDIKAT_BADGE_CLASSES = [
        'sangat_baik' => 'bg-success-subtle text-dark border-success-subtle',
        'baik' => 'bg-primary-subtle text-dark border-primary-subtle',
        'butuh_perbaikan' => 'bg-warning-subtle text-dark border-warning-subtle',
        'kurang' => 'bg-danger-subtle text-dark border-danger-subtle',
        'sangat_kurang' => 'bg-dark-subtle text-dark border-dark-subtle',
    ];

    protected $fillable = [
        'jabatan_id',
        'predikat',
        'persentase',
        'nilai_ak',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'nilai_ak' => 'decimal:3',
    ];

    // ─── Relationships ──────────────────────────────────────────────

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    // ─── Accessors ──────────────────────────────────────────────────

    /**
     * Get the human-readable label for this record's predikat.
     */
    public function getPredikatLabelAttribute(): string
    {
        return self::PREDIKAT_LABELS[$this->predikat] ?? $this->predikat;
    }

    /**
     * Get the badge CSS class for this record's predikat.
     */
    public function getPredikatBadgeClassAttribute(): string
    {
        return self::PREDIKAT_BADGE_CLASSES[$this->predikat] ?? 'bg-secondary-subtle text-secondary';
    }

    // ─── Static Helpers ─────────────────────────────────────────────

    /**
     * Get the label for a given predikat key.
     */
    public static function labelFor(string $predikat): string
    {
        return self::PREDIKAT_LABELS[$predikat] ?? $predikat;
    }

    /**
     * Calculate the AK value from koefisien_tahunan and persentase.
     */
    public static function calculateNilaiAk(float $koefisienTahunan, float $persentase): float
    {
        return round($koefisienTahunan * ($persentase / 100), 3);
    }
}
