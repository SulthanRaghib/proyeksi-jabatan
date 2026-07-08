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
}
