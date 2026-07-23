@extends('layouts.dashboard')

@section('title', 'Proyeksi Jabatan')
@section('content')
    <x-page-header title="Proyeksi Kenaikan Jabatan" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Proyeksi Jabatan'],
    ]">
        <x-slot:action>
            <span class="badge bg-light text-dark border py-2 px-3 me-2">Periodisasi BKN: 6 kali/tahun</span>
            <span class="badge bg-light text-dark border py-2 px-3">Sumber AK: Riwayat PAK</span>
        </x-slot:action>
    </x-page-header>

    <div class="container-fluid">
        <x-alert-flash />
        <!-- Stats Row -->
        <div class="row mb-4 g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Pegawai Dipantau" :value="$stats['total']" icon="users" color="primary" />
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Siap Secara AK" :value="$stats['ready']" icon="check-circle" color="success" />
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Tertahan Waktu" :value="$stats['speedbump']" icon="clock" color="warning" />
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Rata-rata Progres" :value="$stats['avg_progress'] . '%'" icon="trending-up" color="info" />
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
                                    <div class="col-12 col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                                <i data-feather="search" width="16" height="16"></i>
                                            </span>
                                            <input type="text" name="q" value="{{ $search }}" class="form-control custom-input border-start-0 ps-0"
                                                placeholder="Nama / NIP...">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select name="target" class="form-select custom-input">
                                            <option value="pangkat" @selected(request('target', 'pangkat') === 'pangkat')>Kenaikan Pangkat</option>
                                            <option value="jenjang" @selected(request('target') === 'jenjang')>Kenaikan Jenjang</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <select name="status" class="form-select custom-input">
                                            <option value="all" @selected($status === 'all')>Semua Status</option>
                                            <option value="ready" @selected($status === 'ready')>Siap AK</option>
                                            <option value="waiting" @selected($status === 'waiting')>Tertahan</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <select name="performance" class="form-select custom-input">
                                            @foreach ($predikatLabels as $key => $label)
                                                <option value="{{ $key }}" @selected($performance === $key)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2 d-grid">
                                        <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm">
                                            Terapkan Filter
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
                                                @if (isset($projection['estimated_years']))
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary-subtle text-primary rounded px-2 py-1 me-2 fw-bold text-center" style="min-width: 48px; font-size: 0.85rem; white-space: nowrap;">
                                                            {{ $projection['projected_year'] }}
                                                        </div>
                                                        <div class="text-muted small" style="white-space: nowrap;">
                                                            {{ $projection['estimated_years'] }} Tahun
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="white-space: nowrap;">Tak Terukur</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column align-items-start gap-2">
                                                    @if ($pegawai->activeUsulan)
                                                        @if($pegawai->activeUsulan->status === 'draft')
                                                            <span class="badge bg-secondary text-white border border-secondary px-2 py-1 shadow-sm"><i data-feather="edit-3" width="12" height="12" class="me-1"></i> Draf Tertunda</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark border border-warning px-2 py-1 shadow-sm"><i data-feather="loader" width="12" height="12" class="me-1"></i> Proses SK</span>
                                                        @endif
                                                    @elseif ($projection['is_fully_ready'])
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 shadow-sm">Siap AK & Syarat</span>
                                                    @elseif (isset($projection['is_held_by_ukom']) && $projection['is_held_by_ukom'])
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 shadow-sm">Menunggu Ukom</span>
                                                    @elseif ($held)
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 shadow-sm">Tertahan Waktu</span>
                                                    @elseif ($ready)
                                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 shadow-sm">Siap AK</span>
                                                    @else
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 shadow-sm">Proses AK</span>
                                                    @endif
                                                    
                                                    @if ($ready)
                                                        <div class="badge bg-white text-dark border border-light shadow-sm text-start" style="white-space: normal; line-height: 1.4;">
                                                            <span class="text-muted small d-block mb-1">Naik ke:</span>
                                                            <span class="fw-bold text-primary">{{ $projection['next_target_name'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <x-btn href="{{ route('projections.show', $pegawai) }}" variant="soft" size="sm" icon="eye">
                                                    Detail
                                                </x-btn>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <x-empty-state icon="inbox" title="Tidak ada data" description="Tidak ada data yang cocok dengan filter pencarian Anda." />
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
