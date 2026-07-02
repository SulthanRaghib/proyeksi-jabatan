<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    use HasFactory;

    /**
     * Jenjang options for Keahlian category.
     */
    public const JENJANG_KEAHLIAN = [
        'Pertama',
        'Muda',
        'Madya',
        'Utama',
    ];

    /**
     * Jenjang options for Keterampilan category.
     */
    public const JENJANG_KETERAMPILAN = [
        'Pemula',
        'Terampil',
        'Mahir',
        'Penyelia',
    ];

    /**
     * All jenjang options (union of both categories).
     */
    public const JENJANG_OPTIONS = [
        'Pertama',
        'Muda',
        'Madya',
        'Utama',
        'Pemula',
        'Terampil',
        'Mahir',
        'Penyelia',
    ];

    /**
     * Available kategori options.
     */
    public const KATEGORI_OPTIONS = [
        'keahlian',
        'keterampilan',
    ];

    /**
     * Human-readable labels for kategori.
     */
    public const KATEGORI_LABELS = [
        'keahlian' => 'Keahlian',
        'keterampilan' => 'Keterampilan',
    ];

    protected $fillable = [
        'nama_jabatan',
        'kategori',
        'jenjang',
        'koefisien_tahunan',
        'target_ak_kenaikan_pangkat',
        'target_ak_kenaikan_jenjang',
    ];

    protected $casts = [
        'koefisien_tahunan' => 'decimal:2',
        'target_ak_kenaikan_pangkat' => 'integer',
        'target_ak_kenaikan_jenjang' => 'integer',
    ];

    // ─── Relationships ──────────────────────────────────────────────

    /**
     * Get all konversi predikat kinerja entries for this jabatan.
     */
    public function konversiPredikat(): HasMany
    {
        return $this->hasMany(KonversiPredikatKinerja::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────

    /**
     * Get the AK conversion value for a specific predikat.
     *
     * Returns the nilai_ak from the konversi table for this jabatan + predikat.
     * Falls back to calculated value if no specific conversion exists.
     */
    public function getKonversiByPredikat(string $predikat): float
    {
        // Try database lookup first
        if ($this->relationLoaded('konversiPredikat')) {
            $konversi = $this->konversiPredikat
                ->where('predikat', $predikat)
                ->first();
        } else {
            $konversi = $this->konversiPredikat()
                ->where('predikat', $predikat)
                ->first();
        }

        if ($konversi) {
            return (float) $konversi->nilai_ak;
        }

        // Fallback: calculate from koefisien × persentase
        $persentase = KonversiPredikatKinerja::PREDIKAT_PERSENTASE[$predikat] ?? 100.0;

        return KonversiPredikatKinerja::calculateNilaiAk(
            (float) $this->koefisien_tahunan,
            $persentase
        );
    }

    /**
     * Get the human-readable label for this jabatan's kategori.
     */
    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI_LABELS[$this->kategori] ?? $this->kategori;
    }

    /**
     * Get jenjang options valid for this jabatan's kategori.
     */
    public static function jenjangOptionsForKategori(string $kategori): array
    {
        return $kategori === 'keterampilan'
            ? self::JENJANG_KETERAMPILAN
            : self::JENJANG_KEAHLIAN;
    }
}
