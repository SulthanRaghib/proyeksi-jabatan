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

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('riwayat-paks.index') }}" class="row g-2 mb-4">
                    <div class="col-12 col-md-10">
                        <input type="text" name="q" value="{{ $search }}" class="form-control"
                            placeholder="Cari nomor PAK, nama pegawai, atau NIP...">
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
                                <th>Pegawai</th>
                                <th>Nomor PAK</th>
                                <th>Tanggal PAK</th>
                                <th>AK Total</th>
                                <th>Status</th>
                                <th width="220" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatPaks as $riwayatPak)
                                <tr>
                                    <td>{{ $loop->iteration + ($riwayatPaks->currentPage() - 1) * $riwayatPaks->perPage() }}
                                    </td>
                                    <td>
                                        <div>{{ $riwayatPak->pegawai->nama_lengkap }}</div>
                                        <small class="text-muted">{{ $riwayatPak->pegawai->nip }}</small>
                                    </td>
                                    <td>{{ $riwayatPak->no_pak }}</td>
                                    <td>{{ $riwayatPak->tanggal_pak?->format('d/m/Y') }}</td>
                                    <td>{{ number_format((float) $riwayatPak->ak_total, 3, ',', '.') }}</td>
                                    <td>
                                        @if ($riwayatPak->is_latest)
                                            <span class="badge bg-success">Terbaru</span>
                                        @else
                                            <span class="badge bg-secondary">Arsip</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('riwayat-paks.edit', $riwayatPak) }}"
                                            class="btn btn-sm btn-outline-warning">Edit</a>
                                        <form action="{{ route('riwayat-paks.destroy', $riwayatPak) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus riwayat PAK ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat PAK.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $riwayatPaks->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
