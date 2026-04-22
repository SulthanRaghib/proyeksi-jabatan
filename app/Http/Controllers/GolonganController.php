<?php

namespace App\Http\Controllers;

use App\Models\Golongan;
use App\Support\DashboardUiData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_golongan' => ['required', 'string', 'max:255', 'unique:golongans,nama_golongan'],
            'pangkat' => ['required', 'string', 'max:255'],
        ]);

        Golongan::create($validated);

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

    public function update(Request $request, Golongan $golongan): RedirectResponse
    {
        $validated = $request->validate([
            'nama_golongan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('golongans', 'nama_golongan')->ignore($golongan->id),
            ],
            'pangkat' => ['required', 'string', 'max:255'],
        ]);

        $golongan->update($validated);

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
