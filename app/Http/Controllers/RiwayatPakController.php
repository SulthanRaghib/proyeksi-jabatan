<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRiwayatPakRequest;
use App\Http\Requests\UpdateRiwayatPakRequest;
use App\Models\Pegawai;
use App\Models\RiwayatPak;
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

        return view('dashboard.riwayat-pak.index', [
            'riwayatPaks' => $riwayatPaks,
            'search' => $search,
            ...$this->baseViewData(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.riwayat-pak.create', [
            'pegawais' => $this->pegawaiOptions(),
            ...$this->baseViewData(),
        ]);
    }

    public function store(StoreRiwayatPakRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $riwayatPak = RiwayatPak::create($request->validated());
            $this->syncLatestFlag($riwayatPak, (bool) $riwayatPak->is_latest);
        });

        return redirect()
            ->route('riwayat-paks.index')
            ->with('success', 'Riwayat PAK berhasil ditambahkan.');
    }

    public function edit(RiwayatPak $riwayatPak): View
    {
        return view('dashboard.riwayat-pak.edit', [
            'riwayatPak' => $riwayatPak,
            'pegawais' => $this->pegawaiOptions(),
            ...$this->baseViewData(),
        ]);
    }

    public function update(UpdateRiwayatPakRequest $request, RiwayatPak $riwayatPak): RedirectResponse
    {
        DB::transaction(function () use ($request, $riwayatPak): void {
            $riwayatPak->update($request->validated());
            $this->syncLatestFlag($riwayatPak, (bool) $riwayatPak->is_latest);
        });

        return redirect()
            ->route('riwayat-paks.index')
            ->with('success', 'Riwayat PAK berhasil diperbarui.');
    }

    public function destroy(RiwayatPak $riwayatPak): RedirectResponse
    {
        DB::transaction(function () use ($riwayatPak): void {
            $pegawaiId = $riwayatPak->pegawai_id;
            $wasLatest = (bool) $riwayatPak->is_latest;

            $riwayatPak->delete();

            if ($wasLatest) {
                RiwayatPak::query()
                    ->where('pegawai_id', $pegawaiId)
                    ->orderByDesc('tanggal_pak')
                    ->orderByDesc('id')
                    ->limit(1)
                    ->update(['is_latest' => true]);
            }
        });

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

    private function syncLatestFlag(RiwayatPak $riwayatPak, bool $isLatest): void
    {
        if (! $isLatest) {
            return;
        }

        RiwayatPak::query()
            ->where('pegawai_id', $riwayatPak->pegawai_id)
            ->whereKeyNot($riwayatPak->id)
            ->update(['is_latest' => false]);
    }

    private function pegawaiOptions()
    {
        return Pegawai::query()
            ->select(['id', 'nip', 'nama_lengkap'])
            ->orderBy('nama_lengkap')
            ->get();
    }
}
