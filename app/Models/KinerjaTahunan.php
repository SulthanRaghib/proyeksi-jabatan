<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KinerjaTahunan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'pak_id',
        'tahun',
        'predikat',
        'koefisien_saat_itu',
        'ak_didapat',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'koefisien_saat_itu' => 'decimal:3',
        'ak_didapat' => 'decimal:3',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pak()
    {
        return $this->belongsTo(RiwayatPak::class, 'pak_id');
    }
}
