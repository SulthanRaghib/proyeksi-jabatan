<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKonversiPredikatKinerjaRequest;
use App\Http\Requests\UpdateKonversiPredikatKinerjaRequest;
use App\Http\Requests\GenerateKonversiPredikatRequest;
use App\Services\KonversiPredikatService;
use App\Models\Jabatan;
use App\Models\KonversiPredikatKinerja;
use App\Support\DashboardUiData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KonversiPredikatKinerjaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $filterKategori = (string) $request->query('kategori', '');

        $jabatans = Jabatan::query()
            ->with(['konversiPredikat' => function ($query) {
                $query->orderByRaw("FIELD(predikat, 'sangat_baik', 'baik', 'butuh_perbaikan', 'kurang', 'sangat_kurang')");
            }])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama_jabatan', 'like', "%{$search}%")
                    ->orWhere('jenjang', 'like', "%{$search}%");
            })
            ->when($filterKategori !== '', function ($query) use ($filterKategori) {
                $query->where('kategori', $filterKategori);
            })
            ->orderBy('kategori')
            ->orderByRaw("FIELD(jenjang, 'Pemula', 'Terampil', 'Mahir', 'Penyelia', 'Pertama', 'Muda', 'Madya', 'Utama')")
            ->get();

        // Stats
        $totalKonversi = KonversiPredikatKinerja::count();
        $totalJabatan = Jabatan::count();
        $jabatanTerisi = Jabatan::whereHas('konversiPredikat')->count();
        $jabatanBelumTerisi = $totalJabatan - $jabatanTerisi;

        return view('dashboard.master.konversi-predikat.index', [
            'jabatans' => $jabatans,
            'search' => $search,
            'filterKategori' => $filterKategori,
            'stats' => [
                'total_konversi' => $totalKonversi,
                'total_jabatan' => $totalJabatan,
                'jabatan_terisi' => $jabatanTerisi,
                'jabatan_belum_terisi' => $jabatanBelumTerisi,
            ],
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.master.konversi-predikat.create', [
            'jabatans' => Jabatan::query()
                ->orderBy('kategori')
                ->orderBy('nama_jabatan')
                ->orderByRaw("FIELD(jenjang, 'Pemula', 'Terampil', 'Mahir', 'Penyelia', 'Pertama', 'Muda', 'Madya', 'Utama')")
                ->get(),
            'predikatOptions' => KonversiPredikatKinerja::PREDIKAT_OPTIONS,
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
            'predikatPersentase' => KonversiPredikatKinerja::PREDIKAT_PERSENTASE,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function store(StoreKonversiPredikatKinerjaRequest $request): RedirectResponse
    {
        KonversiPredikatKinerja::create($request->validated());

        return redirect()
            ->route('konversi-predikats.index')
            ->with('success', 'Konversi predikat kinerja berhasil ditambahkan.');
    }

    public function edit(KonversiPredikatKinerja $konversiPredikat): View
    {
        $konversiPredikat->load('jabatan');

        return view('dashboard.master.konversi-predikat.edit', [
            'konversi' => $konversiPredikat,
            'jabatans' => Jabatan::query()
                ->orderBy('kategori')
                ->orderBy('nama_jabatan')
                ->orderByRaw("FIELD(jenjang, 'Pemula', 'Terampil', 'Mahir', 'Penyelia', 'Pertama', 'Muda', 'Madya', 'Utama')")
                ->get(),
            'predikatOptions' => KonversiPredikatKinerja::PREDIKAT_OPTIONS,
            'predikatLabels' => KonversiPredikatKinerja::PREDIKAT_LABELS,
            'predikatPersentase' => KonversiPredikatKinerja::PREDIKAT_PERSENTASE,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function update(UpdateKonversiPredikatKinerjaRequest $request, KonversiPredikatKinerja $konversiPredikat): RedirectResponse
    {
        $konversiPredikat->update($request->validated());

        return redirect()
            ->route('konversi-predikats.index')
            ->with('success', 'Konversi predikat kinerja berhasil diperbarui.');
    }

    public function destroy(KonversiPredikatKinerja $konversiPredikat): RedirectResponse
    {
        $konversiPredikat->delete();

        return redirect()
            ->route('konversi-predikats.index')
            ->with('success', 'Konversi predikat kinerja berhasil dihapus.');
    }

    public function generate(GenerateKonversiPredikatRequest $request, KonversiPredikatService $service): RedirectResponse
    {
        $result = $service->generateKonversiForJabatan($request->validated('jabatan_id'));

        return redirect()
            ->route('konversi-predikats.index')
            ->with('success', "Berhasil generate {$result['created_count']} konversi untuk {$result['jabatan_nama']} ({$result['jabatan_jenjang']}).");
    }
}
