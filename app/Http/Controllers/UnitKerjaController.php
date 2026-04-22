<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Support\DashboardUiData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitKerjaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $unitKerjas = UnitKerja::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama_unit', 'like', "%{$search}%");
            })
            ->orderBy('nama_unit')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.master.unit-kerja.index', [
            'unitKerjas' => $unitKerjas,
            'search' => $search,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.master.unit-kerja.create', [
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => ['required', 'string', 'max:255', 'unique:unit_kerjas,nama_unit'],
        ]);

        UnitKerja::create($validated);

        return redirect()
            ->route('unit-kerjas.index')
            ->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function edit(UnitKerja $unitKerja): View
    {
        return view('dashboard.master.unit-kerja.edit', [
            'unitKerja' => $unitKerja,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function update(Request $request, UnitKerja $unitKerja): RedirectResponse
    {
        $validated = $request->validate([
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                Rule::unique('unit_kerjas', 'nama_unit')->ignore($unitKerja->id),
            ],
        ]);

        $unitKerja->update($validated);

        return redirect()
            ->route('unit-kerjas.index')
            ->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy(UnitKerja $unitKerja): RedirectResponse
    {
        $unitKerja->delete();

        return redirect()
            ->route('unit-kerjas.index')
            ->with('success', 'Unit kerja berhasil dihapus.');
    }
}
