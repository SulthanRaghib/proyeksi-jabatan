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

        {{-- Filter & Search --}}
        <div class="filter-card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('unit-kerjas.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                <i data-feather="search" width="16" height="16"></i>
                            </span>
                            <input type="text" name="q" value="{{ $search }}" class="form-control custom-input border-start-0 ps-0"
                                placeholder="Cari nama unit kerja...">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="table-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table table-hover mb-0">
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
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <x-action-button type="edit" :href="route('unit-kerjas.edit', $unitKerja)" />
                                            <x-action-button type="delete_modal" 
                                                action="{{ route('unit-kerjas.destroy', $unitKerja) }}" 
                                                message="Yakin ingin menghapus unit kerja {{ $unitKerja->nama_unit }}?" />
                                        </div>
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

                </div>
            </div>
            
            @if ($unitKerjas->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $unitKerjas->links() }}
                </div>
            @endif
        </div>
        </div>
    </div>
@endsection
