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
        'ak_total',
        'ak_tambahan',
    ];

    protected $casts = [
        'tanggal_pak' => 'date',
        'ak_total' => 'decimal:3',
        'ak_tambahan' => 'decimal:3',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Scope to order records so the latest comes first.
     */
    public function scopeLatestPak(Builder $query): Builder
    {
        return $query->orderByDesc('tanggal_pak')->orderByDesc('id');
    }
}
