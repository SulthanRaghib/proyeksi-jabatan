<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJabatanRequest;
use App\Http\Requests\UpdateJabatanRequest;
use App\Models\Jabatan;
use App\Support\DashboardUiData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $jabatans = Jabatan::query()
            ->when($search !== '', function ($query) use ($search) {
                $query
                    ->where('nama_jabatan', 'like', "%{$search}%")
                    ->orWhere('jenjang', 'like', "%{$search}%");
            })
            ->orderBy('nama_jabatan')
            ->orderBy('jenjang')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.master.jabatan.index', [
            'jabatans' => $jabatans,
            'search' => $search,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.master.jabatan.create', [
            'jenjangOptions' => Jabatan::JENJANG_OPTIONS,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function store(StoreJabatanRequest $request): RedirectResponse
    {
        Jabatan::create($request->validated());

        return redirect()
            ->route('jabatans.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Jabatan $jabatan): View
    {
        return view('dashboard.master.jabatan.edit', [
            'jabatan' => $jabatan,
            'jenjangOptions' => Jabatan::JENJANG_OPTIONS,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function update(UpdateJabatanRequest $request, Jabatan $jabatan): RedirectResponse
    {
        $jabatan->update($request->validated());

        return redirect()
            ->route('jabatans.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        $jabatan->delete();

        return redirect()
            ->route('jabatans.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
