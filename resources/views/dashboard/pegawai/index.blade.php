@extends('layouts.dashboard')

@section('title', 'Data Pegawai')




@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Data Pegawai</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                <a href="{{ route('pegawais.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="feather-icon me-1"></i>
                    Tambah Pegawai
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
                <form method="GET" action="{{ route('pegawais.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                <i data-feather="search" width="16" height="16"></i>
                            </span>
                            <input type="text" name="q" value="{{ $search }}" class="form-control custom-input border-start-0 ps-0"
                                placeholder="Cari nama atau NIP...">
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
                                <th width="70">No</th>
                                <th>NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Unit Kerja</th>
                                <th>Jabatan</th>
                                <th>Golongan</th>
                                <th>TMT Jabatan</th>
                                <th width="220" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pegawais as $pegawai)
                                <tr>
                                    <td>{{ $loop->iteration + ($pegawais->currentPage() - 1) * $pegawais->perPage() }}</td>
                                    <td>{{ $pegawai->nip }}</td>
                                    <td>{{ $pegawai->nama_lengkap }}</td>
                                    <td>{{ $pegawai->unitKerja->nama_unit }}</td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $pegawai->jabatan->nama_jabatan }}</div>
                                        <div class="small text-muted">{{ $pegawai->jabatan->jenjang }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $pegawai->golongan->nama_golongan }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($pegawai->tmt_jabatan)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <x-action-button type="edit" :href="route('pegawais.edit', $pegawai)" />
                                            <form action="{{ route('pegawais.destroy', $pegawai) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-action-button type="delete" />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada data pegawai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
            
            @if ($pegawais->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $pegawais->links() }}
                </div>
            @endif
        </div>
        </div>
    </div>
@endsection
