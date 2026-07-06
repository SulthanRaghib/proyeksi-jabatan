<?php

namespace App\Http\Controllers;

use App\Models\UsulanKenaikanPangkat;
use App\Models\Pegawai;
use App\Support\DashboardUiData;
use App\Http\Requests\StoreUsulanPangkatRequest;
use App\Http\Requests\ApproveUsulanPangkatRequest;
use App\Services\UsulanKenaikanPangkatService;
use Exception;

class UsulanKenaikanPangkatController extends Controller
{
    protected $usulanService;

    public function __construct(UsulanKenaikanPangkatService $usulanService)
    {
        $this->usulanService = $usulanService;
    }

    public function index()
    {
        $usulans = UsulanKenaikanPangkat::with(['pegawai', 'golonganLama', 'golonganBaru'])
            ->whereIn('status', ['sedang_diproses', 'selesai'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

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

    public function submit(UsulanKenaikanPangkat $usulan)
    {
        try {
            $result = $this->usulanService->submitDraft($usulan);
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
}
