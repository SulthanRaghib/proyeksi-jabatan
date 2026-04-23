<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jabatan',
        'jenjang',
        'koefisien_tahunan',
        'target_ak_kenaikan_pangkat',
        'target_ak_kenaikan_jenjang',
    ];
}
