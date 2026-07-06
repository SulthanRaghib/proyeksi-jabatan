<?php

namespace App\Http\Controllers;

use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\RiwayatPak;
use App\Models\UnitKerja;
use App\Services\DashboardService;
use App\Support\DashboardUiData;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        $summaryCards = $dashboardService->getSummaryCards();
        $pakStats = $dashboardService->getPakStats();
        
        $latestPak = $pakStats['latestPak'];
        $pakCount = $pakStats['pakCount'];
        $averageAk = $pakStats['averageAk'];
        $approved = $pakStats['approved'];
        $pending = $pakStats['pending'];
        $approvedPercent = $pakStats['approvedPercent'];

        $latestEmployees = $dashboardService->getLatestEmployees();
        $jenjangCounts = $dashboardService->getJenjangCounts();
        $topUnits = $dashboardService->getTopUnits();

        $notifications = DashboardUiData::notifications();
        $menuGroups = DashboardUiData::menuGroups();

        return view('dashboard.index', compact(
            'summaryCards',
            'latestPak',
            'pakCount',
            'averageAk',
            'approved',
            'pending',
            'approvedPercent',
            'latestEmployees',
            'jenjangCounts',
            'topUnits',
            'notifications',
            'menuGroups'
        ));
    }
}
