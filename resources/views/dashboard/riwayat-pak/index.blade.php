@extends('layouts.dashboard')

@section('title', 'Riwayat PAK')




@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Riwayat PAK</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Riwayat PAK</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                <a href="{{ route('riwayat-paks.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="feather-icon me-1"></i>
                    Tambah Riwayat PAK
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
                <form method="GET" action="{{ route('riwayat-paks.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                <i data-feather="search" width="16" height="16"></i>
                            </span>
                            <input type="text" name="q" value="{{ $search }}" class="form-control custom-input border-start-0 ps-0"
                                placeholder="Cari nomor PAK, nama pegawai, atau NIP...">
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
                                <th>Pegawai</th>
                                <th>Nomor PAK</th>
                                <th>Tanggal PAK</th>
                                <th class="text-center">Predikat</th>
                                <th class="text-end">AK Tambahan</th>
                                <th class="text-end">AK Total</th>
                                <th class="text-center">Status</th>
                                <th width="220" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatPaks as $riwayatPak)
                                @php
                                    $akTotal = (float) $riwayatPak->ak_total;
                                    $akTambahan = (float) $riwayatPak->ak_tambahan;
                                    $isLatest = $riwayatPak->is_computed_latest ?? false;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration + ($riwayatPaks->currentPage() - 1) * $riwayatPaks->perPage() }}
                                    </td>
                                    <td>
                                        <div>{{ $riwayatPak->pegawai->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $riwayatPak->pegawai->nip }}</small>
                                    </td>
                                    <td>{{ $riwayatPak->no_pak }}</td>
                                    <td>{{ $riwayatPak->tanggal_pak?->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        @if ($riwayatPak->predikat_kinerja)
                                            <span class="badge border {{ $riwayatPak->predikat_badge_class }} px-2 py-1">
                                                {{ $riwayatPak->predikat_label }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($akTambahan > 0)
                                            <span class="text-success fw-medium">+{{ number_format($akTambahan, 3, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">{{ number_format($akTambahan, 3, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($akTotal, 3, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if ($isLatest)
                                            <span class="badge bg-success">Terbaru</span>
                                        @else
                                            <span class="badge bg-secondary">Riwayat</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <x-action-button type="edit" href="{{ route('riwayat-paks.edit', $riwayatPak) }}" />
                                            <x-action-button type="delete_modal" 
                                                action="{{ route('riwayat-paks.destroy', $riwayatPak) }}" 
                                                message="Yakin ingin menghapus riwayat PAK ini?" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada riwayat PAK.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
            
            @if ($riwayatPaks->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $riwayatPaks->links() }}
                </div>
            @endif
        </div>
        </div>
    </div>
@endsection
