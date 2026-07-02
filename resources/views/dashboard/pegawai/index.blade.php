@extends('layouts.dashboard')

@section('title', 'Data Pegawai')

@push('styles')
    <style>
        /* Modern Filters */
        .filter-card {
            border-radius: 1rem;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .custom-input {
            border-radius: 0.75rem;
            padding: 0.6rem 1rem;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            transition: all 0.2s;
        }
        
        .custom-input:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        /* Modern Table Card */
        .table-card {
            border-radius: 1rem;
            border: none;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .modern-table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #6b7280;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem;
        }
        
        .modern-table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            background: #f3f4f6;
            color: #6b7280;
            transition: all 0.2s;
            border: none;
        }
        
        .action-btn:hover {
            background: #e5e7eb;
            color: #374151;
        }
        
        .action-btn.edit:hover {
            background: #fef3c7;
            color: #d97706;
        }
        
        .action-btn.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>
@endpush

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
                                        <div class="action-buttons">
                                            <a href="{{ route('pegawais.edit', $pegawai) }}"
                                                class="action-btn edit" title="Edit">
                                                <i data-feather="edit-2" width="14" height="14"></i>
                                            </a>
                                            <form action="{{ route('pegawais.destroy', $pegawai) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Hapus">
                                                    <i data-feather="trash-2" width="14" height="14"></i>
                                                </button>
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
