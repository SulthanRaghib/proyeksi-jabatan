<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    public const JENJANG_OPTIONS = [
        'Pertama',
        'Muda',
        'Madya',
        'Utama',
    ];

    protected $fillable = [
        'nama_jabatan',
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
}
