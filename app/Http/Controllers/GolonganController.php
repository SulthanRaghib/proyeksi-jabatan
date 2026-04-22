<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGolonganRequest;
use App\Http\Requests\UpdateGolonganRequest;
use App\Models\Golongan;
use App\Support\DashboardUiData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GolonganController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $golongans = Golongan::query()
            ->when($search !== '', function ($query) use ($search) {
                $query
                    ->where('nama_golongan', 'like', "%{$search}%")
                    ->orWhere('pangkat', 'like', "%{$search}%");
            })
            ->orderBy('nama_golongan')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.master.golongan.index', [
            'golongans' => $golongans,
            'search' => $search,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.master.golongan.create', [
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function store(StoreGolonganRequest $request): RedirectResponse
    {
        Golongan::create($request->validated());

        return redirect()
            ->route('golongans.index')
            ->with('success', 'Golongan berhasil ditambahkan.');
    }

    public function edit(Golongan $golongan): View
    {
        return view('dashboard.master.golongan.edit', [
            'golongan' => $golongan,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function update(UpdateGolonganRequest $request, Golongan $golongan): RedirectResponse
    {
        $golongan->update($request->validated());

        return redirect()
            ->route('golongans.index')
            ->with('success', 'Golongan berhasil diperbarui.');
    }

    public function destroy(Golongan $golongan): RedirectResponse
    {
        $golongan->delete();

        return redirect()
            ->route('golongans.index')
            ->with('success', 'Golongan berhasil dihapus.');
    }
}
