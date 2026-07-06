<?php

namespace App\Services;

use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\RiwayatPak;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummaryCards(): array
    {
        return [
            [
                'value' => Pegawai::count(),
                'title' => 'Total Pegawai',
                'icon' => 'user-plus',
            ],
            [
                'value' => Jabatan::count(),
                'title' => 'Total Jabatan',
                'icon' => 'briefcase',
            ],
            [
                'value' => UnitKerja::count(),
                'title' => 'Total Unit Kerja',
                'icon' => 'home',
            ],
            [
                'value' => Golongan::count(),
                'title' => 'Total Golongan',
                'icon' => 'award',
            ],
        ];
    }

    public function getPakStats(): array
    {
        $statusUkom = Pegawai::selectRaw('SUM(CASE WHEN status_ukom = 1 THEN 1 ELSE 0 END) as approved, SUM(CASE WHEN status_ukom = 0 THEN 1 ELSE 0 END) as pending')
            ->first();

        $approved = $statusUkom->approved ?? 0;
        $pending = $statusUkom->pending ?? 0;
        $approvedPercent = $approved + $pending ? round(($approved / ($approved + $pending)) * 100) : 0;

        return [
            'latestPak' => RiwayatPak::latest('tanggal_pak')->first(),
            'pakCount' => RiwayatPak::count(),
            'averageAk' => RiwayatPak::avg('ak_total'),
            'approved' => $approved,
            'pending' => $pending,
            'approvedPercent' => $approvedPercent,
        ];
    }

    public function getLatestEmployees()
    {
        return Pegawai::with(['unitKerja', 'jabatan', 'golongan'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function getJenjangCounts()
    {
        return Jabatan::select('jenjang', DB::raw('count(*) as total'))
            ->groupBy('jenjang')
            ->orderByDesc('total')
            ->get();
    }

    public function getTopUnits()
    {
        return UnitKerja::select('nama_unit', DB::raw('count(*) as total'))
            ->groupBy('nama_unit')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
}
