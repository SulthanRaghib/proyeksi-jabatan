@extends('layouts.dashboard')

@section('title', 'Data Master - Jabatan')

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

        {{-- Filter & Search --}}
        <div class="filter-card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('jabatans.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                <i data-feather="search" width="16" height="16"></i>
                            </span>
                            <input type="text" name="q" value="{{ $search }}" class="form-control custom-input border-start-0 ps-0"
                                placeholder="Cari nama jabatan atau jenjang...">
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
                                    <td><span class="badge bg-light text-dark border">{{ number_format((float) $jabatan->koefisien_tahunan, 2, ',', '.') }}</span></td>
                                    <td><span class="badge bg-success bg-opacity-25 text-dark border-0 fw-bold">{{ number_format((int) $jabatan->target_ak_kenaikan_pangkat, 0, ',', '.') }}</span></td>
                                    <td>
                                        @if($jabatan->target_ak_kenaikan_jenjang)
                                            <span class="badge bg-warning bg-opacity-25 text-dark border-0 fw-bold">{{ number_format((int) $jabatan->target_ak_kenaikan_jenjang, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('jabatans.edit', $jabatan) }}"
                                                class="action-btn edit" title="Edit">
                                                <i data-feather="edit-2" width="14" height="14"></i>
                                            </a>
                                            <form action="{{ route('jabatans.destroy', $jabatan) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')">
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
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada data jabatan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
            
            @if ($jabatans->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $jabatans->links() }}
                </div>
            @endif
        </div>
        </div>
    </div>
@endsection
