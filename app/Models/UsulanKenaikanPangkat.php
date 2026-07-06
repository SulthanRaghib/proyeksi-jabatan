<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsulanKenaikanPangkat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'saldo_ak_awal' => 'float',
        'potongan_ak' => 'float',
        'sisa_ak' => 'float',
        'is_lintas_jenjang' => 'boolean',
        'tmt_golongan_baru' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function golonganLama()
    {
        return $this->belongsTo(Golongan::class, 'golongan_lama_id');
    }

    public function golonganBaru()
    {
        return $this->belongsTo(Golongan::class, 'golongan_baru_id');
    }

    public function dokumenUsulans()
    {
        return $this->hasMany(DokumenUsulan::class);
    }
}
