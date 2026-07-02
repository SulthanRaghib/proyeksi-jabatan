<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKonversiPredikatKinerjaRequest;
use App\Http\Requests\UpdateKonversiPredikatKinerjaRequest;
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

    /**
     * Batch generate konversi for a specific jabatan (AJAX or form).
     * Generates all 5 predikat entries based on koefisien_tahunan × persentase.
     */
    public function generate(Request $request): RedirectResponse
    {
        $request->validate(['jabatan_id' => 'required|exists:jabatans,id']);

        $jabatan = Jabatan::findOrFail($request->input('jabatan_id'));
        $koefisien = (float) $jabatan->koefisien_tahunan;
        $created = 0;

        foreach (KonversiPredikatKinerja::PREDIKAT_PERSENTASE as $predikat => $persentase) {
            $nilaiAk = KonversiPredikatKinerja::calculateNilaiAk($koefisien, $persentase);

            KonversiPredikatKinerja::updateOrCreate(
                [
                    'jabatan_id' => $jabatan->id,
                    'predikat' => $predikat,
                ],
                [
                    'persentase' => $persentase,
                    'nilai_ak' => $nilaiAk,
                ]
            );
            $created++;
        }

        return redirect()
            ->route('konversi-predikats.index')
            ->with('success', "Berhasil generate {$created} konversi untuk {$jabatan->nama_jabatan} ({$jabatan->jenjang}).");
    }
}
