@extends('layouts.dashboard')

@section('title', 'Data Master - Unit Kerja')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Data Master Unit Kerja</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Unit Kerja</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                <a href="{{ route('unit-kerjas.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="feather-icon me-1"></i>
                    Tambah Unit Kerja
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
                <form method="GET" action="{{ route('unit-kerjas.index') }}" class="row g-2 mb-4">
                    <div class="col-12 col-md-10">
                        <input type="text" name="q" value="{{ $search }}" class="form-control"
                            placeholder="Cari nama unit kerja...">
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-primary">Cari</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="80">No</th>
                                <th>Nama Unit</th>
                                <th width="220" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($unitKerjas as $unitKerja)
                                <tr>
                                    <td>{{ $loop->iteration + ($unitKerjas->currentPage() - 1) * $unitKerjas->perPage() }}
                                    </td>
                                    <td>{{ $unitKerja->nama_unit }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('unit-kerjas.edit', $unitKerja) }}"
                                            class="btn btn-sm btn-outline-warning">Edit</a>
                                        <form action="{{ route('unit-kerjas.destroy', $unitKerja) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus unit kerja ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada data unit kerja.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $unitKerjas->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
