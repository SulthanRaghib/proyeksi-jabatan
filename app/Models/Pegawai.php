<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'unit_kerja_id',
        'jabatan_id',
        'golongan_id',
        'nip',
        'nama_lengkap',
        'tmt_jabatan',
        'tmt_golongan',
        'status_ukom',
    ];

    protected $casts = [
        'tmt_jabatan' => 'date',
        'tmt_golongan' => 'date',
        'status_ukom' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function golongan(): BelongsTo
    {
        return $this->belongsTo(Golongan::class);
    }

    public function riwayatPaks(): HasMany
    {
        return $this->hasMany(RiwayatPak::class);
    }
}
