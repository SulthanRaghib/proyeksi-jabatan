<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use App\Support\DashboardUiData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $pegawais = Pegawai::query()
            ->with(['user', 'unitKerja', 'jabatan', 'golongan'])
            ->when($search !== '', function ($query) use ($search) {
                $query
                    ->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            })
            ->orderBy('nama_lengkap')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.pegawai.index', [
            'pegawais' => $pegawais,
            'search' => $search,
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.pegawai.create', [
            'users' => User::all(['id', 'name', 'email']),
            'unitKerjas' => UnitKerja::all(['id', 'nama_unit']),
            'jabatans' => Jabatan::all(['id', 'nama_jabatan', 'jenjang']),
            'golongans' => Golongan::all(['id', 'nama_golongan', 'pangkat']),
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function store(StorePegawaiRequest $request): RedirectResponse
    {
        Pegawai::create($request->validated());

        return redirect()
            ->route('pegawais.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai): View
    {
        return view('dashboard.pegawai.edit', [
            'pegawai' => $pegawai,
            'users' => User::all(['id', 'name', 'email']),
            'unitKerjas' => UnitKerja::all(['id', 'nama_unit']),
            'jabatans' => Jabatan::all(['id', 'nama_jabatan', 'jenjang']),
            'golongans' => Golongan::all(['id', 'nama_golongan', 'pangkat']),
            'notifications' => DashboardUiData::notifications(),
            'menuGroups' => DashboardUiData::menuGroups(),
        ]);
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai): RedirectResponse
    {
        $pegawai->update($request->validated());

        return redirect()
            ->route('pegawais.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        $pegawai->delete();

        return redirect()
            ->route('pegawais.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}
