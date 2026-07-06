<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'sedang_hukuman_disiplin',
        'is_locked_usulan',
    ];

    protected $casts = [
        'tmt_jabatan' => 'date',
        'tmt_golongan' => 'date',
        'status_ukom' => 'boolean',
        'sedang_hukuman_disiplin' => 'boolean',
        'is_locked_usulan' => 'boolean',
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

    public function kinerjaTahunans(): HasMany
    {
        return $this->hasMany(KinerjaTahunan::class);
    }

    /**
     * Get the latest (most recent) RiwayatPak record.
     * Auto-detected by tanggal_pak DESC, id DESC — no manual flag needed.
     */
    public function latestRiwayatPak(): HasOne
    {
        return $this->hasOne(RiwayatPak::class)->latestPak();
    }

    /**
     * Get the latest (most recent) RiwayatPak record.
     * Auto-detected by tanggal_pak DESC, id DESC — no manual flag needed.
     */
    public function latestKinerjaTahunan(): HasOne
    {
        return $this->hasOne(KinerjaTahunan::class)->latestOfMany();
    }

    public function activeUsulan(): HasOne
    {
        return $this->hasOne(UsulanKenaikanPangkat::class)
            ->whereIn('status', ['draft', 'sedang_diproses'])
            ->latestOfMany();
    }

    public function usulans(): HasMany
    {
        return $this->hasMany(UsulanKenaikanPangkat::class);
    }
}
