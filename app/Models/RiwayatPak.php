<?php

namespace App\Models;

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
        'is_latest',
    ];

    protected $casts = [
        'tanggal_pak' => 'date',
        'ak_total' => 'decimal:3',
        'is_latest' => 'boolean',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
