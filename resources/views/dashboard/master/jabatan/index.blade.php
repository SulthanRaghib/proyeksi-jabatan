@extends('layouts.dashboard')

@section('title', 'Data Master - Jabatan')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Data Master Jabatan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Jabatan</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                <a href="{{ route('jabatans.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="feather-icon me-1"></i>
                    Tambah Jabatan
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('jabatans.index') }}" class="row g-2 mb-4">
                    <div class="col-12 col-md-10">
                        <input type="text" name="q" value="{{ $search }}" class="form-control"
                            placeholder="Cari nama jabatan atau jenjang...">
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-primary">Cari</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="70">No</th>
                                <th>Nama Jabatan</th>
                                <th>Jenjang</th>
                                <th>Koefisien Tahunan</th>
                                <th>Target AK Pangkat</th>
                                <th>Target AK Jenjang</th>
                                <th width="220" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jabatans as $jabatan)
                                <tr>
                                    <td>{{ $loop->iteration + ($jabatans->currentPage() - 1) * $jabatans->perPage() }}</td>
                                    <td>{{ $jabatan->nama_jabatan }}</td>
                                    <td>{{ $jabatan->jenjang }}</td>
                                    <td>{{ number_format((float) $jabatan->koefisien_tahunan, 2, ',', '.') }}</td>
                                    <td>{{ number_format((int) $jabatan->target_ak_kenaikan_pangkat, 0, ',', '.') }}</td>
                                    <td>{{ number_format((int) $jabatan->target_ak_kenaikan_jenjang, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('jabatans.edit', $jabatan) }}"
                                            class="btn btn-sm btn-outline-warning">Edit</a>
                                        <form action="{{ route('jabatans.destroy', $jabatan) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data jabatan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $jabatans->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
