<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRiwayatPakRequest;
use App\Http\Requests\UpdateRiwayatPakRequest;
use App\Models\KonversiPredikatKinerja;
use App\Models\Pegawai;
use App\Models\RiwayatPak;
use App\Services\RiwayatPakService;
use App\Support\DashboardUiData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RiwayatPakController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $riwayatPaks = RiwayatPak::query()
            ->with(['pegawai:id,nip,nama_lengkap'])
            ->when($search !== '', function ($query) use ($search) {
                $query
                    ->where('no_pak', 'like', "%{$search}%")
                    ->orWhereHas('pegawai', function ($pegawaiQuery) use ($search) {
                        $pegawaiQuery
                            ->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('tanggal_pak')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Determine which record is the latest per pegawai for badge display
        $latestIds = RiwayatPak::query()
            ->select(DB::raw('MAX(id) as id'))
            ->whereIn('pegawai_id', $riwayatPaks->getCollection()->pluck('pegawai_id')->unique())
            ->groupBy('pegawai_id')
            ->havingRaw('id = (SELECT r2.id FROM riwayat_paks r2 WHERE r2.pegawai_id = riwayat_paks.pegawai_id ORDER BY r2.tanggal_pak DESC, r2.id DESC LIMIT 1)')
            ->pluck('id')
            ->toArray();

        // Alternative simpler approach — just compute latest per pegawai
        $latestPerPegawai = [];
        foreach ($riwayatPaks->getCollection()->pluck('pegawai_id')->unique() as $pegawaiId) {
            $latest = RiwayatPak::query()
                ->where('pegawai_id', $pegawaiId)
                ->latestPak()
                ->value('id');
            if ($latest) {
                $latestPerPegawai[] = $latest;
            }
        }

        $riwayatPaks->getCollection()->transform(function ($item) use ($latestPerPegawai) {
            $item->is_computed_latest = in_array($item->id, $latestPerPegawai);
            return $item;
        });

        return view('dashboard.riwayat-pak.index', [
            'riwayatPaks' => $riwayatPaks,
            'search' => $search,
            ...$this->baseViewData(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.riwayat-pak.create', [
            'pegawais' => $this->pegawaiWithFullContext(),
            ...$this->baseViewData(),
        ]);
    }

    public function store(StoreRiwayatPakRequest $request, RiwayatPakService $service): RedirectResponse
    {
        $service->storeRiwayatPak($request->validated());

        return redirect()
            ->route('riwayat-paks.index')
            ->with('success', 'Riwayat PAK berhasil ditambahkan.');
    }

    public function edit(RiwayatPak $riwayatPak): View
    {
        return view('dashboard.riwayat-pak.edit', [
            'riwayatPak' => $riwayatPak,
            'pegawais' => $this->pegawaiWithFullContext(),
            ...$this->baseViewData(),
        ]);
    }

    public function update(UpdateRiwayatPakRequest $request, RiwayatPak $riwayatPak, RiwayatPakService $service): RedirectResponse
    {
        $service->updateRiwayatPak($riwayatPak, $request->validated());

        return redirect()
            ->route('riwayat-paks.index')
            ->with('success', 'Riwayat PAK berhasil diperbarui.');
    }

    public function destroy(RiwayatPak $riwayatPak, RiwayatPakService $service): RedirectResponse
    {
        $service->deleteRiwayatPak($riwayatPak);

        return redirect()
            ->route('riwayat-paks.index')
            ->with('success', 'Riwayat PAK berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function baseViewData(): array
    {
        return [
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ];
    }

    /**
     * Load pegawais with full context for the form display:
     * latest AK, jabatan, golongan, konversi predikat, and recent riwayat records.
     */
    private function pegawaiWithFullContext()
    {
        return Pegawai::query()
            ->select(['id', 'nip', 'nama_lengkap', 'jabatan_id', 'golongan_id'])
            ->with([
                'jabatan:id,nama_jabatan,jenjang,target_ak_kenaikan_pangkat,koefisien_tahunan',
                'jabatan.konversiPredikat',
                'golongan:id,nama_golongan',
                'riwayatPaks' => function ($query) {
                    $query->latestPak()->limit(5);
                },
            ])
            ->orderBy('nama_lengkap')
            ->get();
    }

    /**
     * API: Get the konversi AK value for a specific pegawai + predikat combination.
     *
     * Returns JSON with the nilai_ak from the konversi_predikat_kinerjas table
     * based on the pegawai's jabatan. Used by the Riwayat PAK form to auto-fill
     * the AK tambahan field when a predikat kinerja is selected.
     *
     * @param Pegawai $pegawai
     * @param string $predikat
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKonversiAk(Pegawai $pegawai, string $predikat)
    {
        $pegawai->load('jabatan.konversiPredikat');

        if (!$pegawai->jabatan) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai belum memiliki jabatan.',
                'nilai_ak' => 0,
            ]);
        }

        $nilaiAk = $pegawai->jabatan->getKonversiByPredikat($predikat);

        $konversi = $pegawai->jabatan->konversiPredikat
            ->where('predikat', $predikat)
            ->first();

        return response()->json([
            'success' => true,
            'nilai_ak' => $nilaiAk,
            'persentase' => $konversi ? (float) $konversi->persentase : (KonversiPredikatKinerja::PREDIKAT_PERSENTASE[$predikat] ?? 100),
            'jabatan' => $pegawai->jabatan->nama_jabatan,
            'jenjang' => $pegawai->jabatan->jenjang,
            'koefisien' => (float) $pegawai->jabatan->koefisien_tahunan,
            'source' => $konversi ? 'database' : 'calculated',
        ]);
    }
    public function generateNoPak(RiwayatPakService $service)
    {
        $generatedNo = $service->generateNoPak();

        return response()->json([
            'success' => true,
            'no_pak' => $generatedNo
        ]);
    }
}
