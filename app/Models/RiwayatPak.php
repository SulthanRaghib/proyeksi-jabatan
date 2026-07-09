<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPak extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'no_pak',
        'tanggal_pak',
        'periode_awal',
        'periode_akhir',
        'ak_total',
        'ak_tambahan',
        'is_konversi_baru',
    ];

    protected $casts = [
        'tanggal_pak' => 'date:Y-m-d',
        'periode_awal' => 'date:Y-m-d',
        'periode_akhir' => 'date:Y-m-d',
        'is_konversi_baru' => 'boolean',
        'ak_total' => 'decimal:3',
        'ak_tambahan' => 'decimal:3',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function kinerjas()
    {
        return $this->hasMany(KinerjaTahunan::class, 'pak_id');
    }

    public function getPredikatKinerjaAttribute()
    {
        return $this->kinerjas->first()?->predikat;
    }

    /**
     * Scope to order records so the latest comes first.
     */
    public function scopeLatestPak(Builder $query): Builder
    {
        return $query->orderByDesc('tanggal_pak')->orderByDesc('id');
    }

    // ─── Accessors ──────────────────────────────────────────────────

    /**
     * Get the human-readable predikat kinerja label.
     */
    public function getPredikatLabelAttribute(): string
    {
        if (!$this->predikat_kinerja) {
            return '-';
        }

        return KonversiPredikatKinerja::PREDIKAT_LABELS[$this->predikat_kinerja] ?? $this->predikat_kinerja;
    }

    /**
     * Get badge CSS class for the predikat.
     */
    public function getPredikatBadgeClassAttribute(): string
    {
        if (!$this->predikat_kinerja) {
            return 'bg-secondary-subtle text-secondary';
        }

        return KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES[$this->predikat_kinerja]
            ?? 'bg-secondary-subtle text-secondary';
    }

    /**
     * Get the human-readable assessment period label.
     */
    public function getPeriodePenilaianLabelAttribute(): string
    {
        if ($this->periode_awal && $this->periode_akhir) {
            $awal = \Carbon\Carbon::parse($this->periode_awal);
            $akhir = \Carbon\Carbon::parse($this->periode_akhir);
            
            // Check if it's a full calendar year (Jan 1 to Dec 31)
            if ($awal->month === 1 && $awal->day === 1 && $akhir->month === 12 && $akhir->day === 31) {
                return (string) $akhir->year;
            }
            
            // If it's a sub-year period, let's see how many months
            $months = ($akhir->year - $awal->year) * 12 + ($akhir->month - $awal->month) + 1;
            if ($months === 3) {
                $triwulan = (int) ceil($akhir->month / 3);
                return $akhir->year . " (Triwulan " . $this->romawi($triwulan) . ")";
            } elseif ($months === 6) {
                $semester = (int) ceil($akhir->month / 6);
                return $akhir->year . " (Semester " . $this->romawi($semester) . ")";
            }
            
            // Fallback: show date range
            return $awal->format('d/m/Y') . ' - ' . $akhir->format('d/m/Y');
        }
        
        // Fallback if no periods: use the year of tanggal_pak
        if ($this->tanggal_pak) {
            return (string) \Carbon\Carbon::parse($this->tanggal_pak)->year;
        }
        
        return '-';
    }
    
    private function romawi(int $num): string
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
        return $map[$num] ?? (string)$num;
    }
}
