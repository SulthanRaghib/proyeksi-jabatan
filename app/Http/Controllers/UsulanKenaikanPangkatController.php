<?php

namespace App\Http\Controllers;

use App\Models\UsulanKenaikanPangkat;
use App\Models\Pegawai;
use App\Support\DashboardUiData;
use App\Http\Requests\StoreUsulanPangkatRequest;
use App\Http\Requests\ApproveUsulanPangkatRequest;
use App\Services\UsulanKenaikanPangkatService;
use Exception;

use App\Http\Requests\UpdateUsulanPangkatRequest;

class UsulanKenaikanPangkatController extends Controller
{
    protected $usulanService;

    public function __construct(UsulanKenaikanPangkatService $usulanService)
    {
        $this->usulanService = $usulanService;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $tab = $request->query('tab', 'sedang_diproses');
        
        $query = UsulanKenaikanPangkat::with(['pegawai', 'golonganLama', 'golonganBaru']);
        
        if ($tab !== 'semua') {
            if ($tab === 'sedang_diproses') {
                $query->whereIn('status', ['sedang_diproses', 'PROSES_KP_REGULER', 'PROSES_KENAIKAN_JENJANG']);
            } else {
                $query->where('status', $tab);
            }
        }
        
        $usulans = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['tab' => $tab]);

        return view('dashboard.usulan-pangkat.index', [
            'usulans' => $usulans,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function store(StoreUsulanPangkatRequest $request)
    {
        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        try {
            $result = $this->usulanService->storeUsulan(
                $request->validated(),
                $request->allFiles(),
                $pegawai
            );

            return back()->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateUsulanPangkatRequest $request, UsulanKenaikanPangkat $usulan)
    {
        try {
            $result = $this->usulanService->updateDraft($usulan, $request->allFiles());
            return back()->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(ApproveUsulanPangkatRequest $request, UsulanKenaikanPangkat $usulan)
    {
        try {
            $result = $this->usulanService->approveUsulan($usulan, $request->validated());
            return back()->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generateNoSk()
    {
        $year = date('Y');
        $month = date('m');
        
        $latestUsulan = UsulanKenaikanPangkat::where('nomor_sk_baru', 'like', "Kpts-%/B.2/KP.01.01/$month/$year")
            ->orderByRaw('CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(nomor_sk_baru, "/", 1), "-", -1) AS UNSIGNED) DESC')
            ->first();
            
        $nextNumber = 1;
        if ($latestUsulan && preg_match('/^Kpts-(\d+)\/B\.2\/KP\.01\.01\/\d{2}\/\d{4}$/', $latestUsulan->nomor_sk_baru, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        $generatedNo = sprintf("Kpts-%03d/B.2/KP.01.01/%s/%s", $nextNumber, $month, $year);

        return response()->json([
            'success' => true,
            'nomor_sk' => $generatedNo,
        ]);
    }
}
