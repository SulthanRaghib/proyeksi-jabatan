@extends('layouts.dashboard')

@section('title', 'Proyeksi Jabatan')

@push('styles')
    <style>
        /* Modern Stat Cards */
        .stat-card {
            border-radius: 1rem;
            border: none;
            overflow: hidden;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            padding: 1.5rem;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .stat-bg-icon {
            position: absolute;
            right: -15px;
            bottom: -15px;
            opacity: 0.04;
            transform: rotate(-15deg);
            transition: all 0.5s ease;
        }

        .stat-card:hover .stat-bg-icon {
            transform: rotate(0) scale(1.2);
            opacity: 0.08;
        }

    </style>
@endpush

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-7">
                <h3 class="page-title text-dark font-weight-medium mb-1">Proyeksi Kenaikan Jabatan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Proyeksi Jabatan</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-5 text-md-end mt-3 mt-md-0">
                <span class="badge bg-light text-dark border py-2 px-3 me-2">Periodisasi BKN: 6 kali/tahun</span>
                <span class="badge bg-light text-dark border py-2 px-3">Sumber AK: Riwayat PAK</span>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Stats Row -->
        <div class="row mb-4 g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i data-feather="users" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Pegawai Dipantau</p>
                        <h3 class="mb-0 text-dark fw-bolder">{{ $stats['total'] }}</h3>
                    </div>
                    <i data-feather="users" width="100" height="100" class="stat-bg-icon text-primary"></i>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i data-feather="check-circle" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Siap Secara AK</p>
                        <h3 class="mb-0 text-dark fw-bolder">{{ $stats['ready'] }}</h3>
                    </div>
                    <i data-feather="check-circle" width="100" height="100" class="stat-bg-icon text-success"></i>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i data-feather="clock" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Tertahan Waktu</p>
                        <h3 class="mb-0 text-dark fw-bolder">{{ $stats['speedbump'] }}</h3>
                    </div>
                    <i data-feather="clock" width="100" height="100" class="stat-bg-icon text-warning"></i>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i data-feather="trending-up" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Rata-rata Progres</p>
                        <h3 class="mb-0 text-info fw-bolder">{{ $stats['avg_progress'] }}%</h3>
                    </div>
                    <i data-feather="trending-up" width="100" height="100" class="stat-bg-icon text-info"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">Radar Proyeksi Jabatan</h4>
                                <p class="text-muted mb-0 small">Pantau status kenaikan pangkat berdasarkan Angka Kredit
                                    (AK).</p>
                            </div>
                        </div>

                        <!-- Filter Form yang lebih clean menyatu dengan background putih -->
                        <div class="filter-card mb-4">
                            <div class="card-body p-3">
                                <form method="GET" action="{{ route('projections.index') }}" class="row g-2 align-items-center">
                                    <div class="col-12 col-md-5">
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                                <i data-feather="search" width="16" height="16"></i>
                                            </span>
                                            <input type="text" name="q" value="{{ $search }}" class="form-control custom-input border-start-0 ps-0"
                                                placeholder="Cari nama atau NIP...">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select name="status" class="form-select custom-input">
                                            <option value="all" @selected($status === 'all')>Semua Status</option>
                                            <option value="ready" @selected($status === 'ready')>Siap AK</option>
                                            <option value="waiting" @selected($status === 'waiting')>Tertahan Waktu</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select name="performance" class="form-select custom-input">
                                            @foreach ($predikatLabels as $key => $label)
                                                <option value="{{ $key }}" @selected($performance === $key)>
                                                    Kinerja: {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-1 d-grid">
                                        <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm">
                                            Cari
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table modern-table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Pegawai</th>
                                                <th>Jabatan & Unit</th>
                                                <th style="min-width: 180px;">Progress AK</th>
                                                <th>Estimasi</th>
                                                <th>Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                <tbody>
                                    @forelse ($projections as $item)
                                        @php
                                            $pegawai = $item['pegawai'];
                                            $projection = $item['projection'];
                                            $ready = $projection['is_ready_mathematically'];
                                            $held = $projection['is_held_by_speedbump'];
                                            $progress = $projection['progress_percentage'];
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-medium text-dark">{{ $pegawai->nama_lengkap }}</div>
                                                <div class="text-muted small">{{ $pegawai->nip }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $pegawai->jabatan->nama_jabatan }}</div>
                                                <div class="text-muted small">{{ $pegawai->golongan->nama_golongan }} •
                                                    {{ $pegawai->unitKerja->nama_unit }}</div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span>{{ number_format($projection['current_ak'], 2, ',', '.') }} /
                                                        {{ number_format($projection['target_ak'], 0, ',', '.') }}</span>
                                                    <span
                                                        class="fw-medium">{{ number_format($progress, 1, ',', '.') }}%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $held ? 'bg-warning' : ($ready ? 'bg-success' : 'bg-primary') }}"
                                                        role="progressbar" style="width: {{ min($progress, 100) }}%">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if (isset($projection['estimated_periods']))
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary-subtle text-primary rounded px-2 py-1 me-2 fw-bold text-center" style="min-width: 48px; font-size: 0.85rem; white-space: nowrap;">
                                                            {{ $projection['projected_year'] }}
                                                        </div>
                                                        <div class="text-muted small" style="white-space: nowrap;">
                                                            {{ $projection['estimated_periods'] }} Periode
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="white-space: nowrap;">Tak Terukur</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($held)
                                                    <span
                                                        class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">Tertahan
                                                        Waktu</span>
                                                @elseif ($ready)
                                                    <span
                                                        class="badge bg-success-subtle text-dark border border-success-subtle px-2 py-1">Siap
                                                        AK</span>
                                                @else
                                                    <span
                                                        class="badge bg-primary-subtle text-dark border border-primary-subtle px-2 py-1">Proses
                                                        AK</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <x-btn href="{{ route('projections.show', $pegawai) }}" variant="soft" size="sm" icon="eye">
                                                    Detail
                                                </x-btn>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <div class="mb-3"><i data-feather="inbox" width="48" height="48" class="text-secondary opacity-50"></i></div>
                                                Tidak ada data yang cocok dengan filter pencarian Anda.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sorotan Cepat -->
            <div class="col-12 col-xl-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Sorotan Cepat</h4>
                            <span class="badge bg-primary-subtle text-primary rounded-pill">Top 3</span>
                        </div>

                        <div>
                            @forelse ($highlights as $item)
                                @php
                                    $pegawai = $item['pegawai'];
                                    $projection = $item['projection'];
                                @endphp
                                <div class="estimation-alert-box p-3 mb-3 border bg-white" style="border-radius: 12px;">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $pegawai->nama_lengkap }}</div>
                                                <div class="text-muted" style="font-size: 0.8rem;">{{ $pegawai->jabatan->nama_jabatan }}</div>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{{ number_format($projection['progress_percentage'], 0) }}%</span>
                                        </div>

                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-primary"
                                                style="width: {{ min($projection['progress_percentage'], 100) }}%"></div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center text-muted mt-2" style="font-size: 0.8rem;">
                                            <span class="d-flex align-items-center gap-1">
                                                <i data-feather="clock" width="12" height="12"></i>
                                                {{ $projection['estimated_years'] }} tahun
                                            </span>
                                            <span class="fw-bold text-dark">Target {{ $projection['projected_year'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">Belum ada data sorotan.</p>
                                </div>
                            @endforelse
                        </div>

                        @if (count($highlights) > 0)
                            <div class="text-center mt-3">
                                <a href="{{ route('projections.index', ['status' => 'ready']) }}"
                                    class="text-primary text-decoration-none small">Lihat Semua Siap AK &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
