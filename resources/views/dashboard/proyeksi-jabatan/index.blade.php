@extends('layouts.dashboard')

@section('title', 'Proyeksi Jabatan')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-7">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Proyeksi Kenaikan Jabatan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Proyeksi Jabatan</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-5 text-md-end mt-3 mt-md-0">
                <div class="d-inline-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border">Periodisasi BKN: 6 kali/tahun</span>
                    <span class="badge bg-light text-dark border">Sumber AK: Riwayat PAK terbaru</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Pegawai Dipantau</p>
                                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                            </div>
                            <div class="ms-auto text-primary">
                                <i data-feather="users" width="28" height="28"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Siap Secara AK</p>
                                <h3 class="mb-0">{{ $stats['ready'] }}</h3>
                            </div>
                            <div class="ms-auto text-success">
                                <i data-feather="check-circle" width="28" height="28"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tertahan Minimum Waktu</p>
                                <h3 class="mb-0">{{ $stats['speedbump'] }}</h3>
                            </div>
                            <div class="ms-auto text-warning">
                                <i data-feather="clock" width="28" height="28"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Rata-rata Progres</p>
                                <h3 class="mb-0">{{ $stats['avg_progress'] }}%</h3>
                            </div>
                            <div class="ms-auto text-info">
                                <i data-feather="trending-up" width="28" height="28"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12 col-xl-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                            <div>
                                <h4 class="card-title mb-1">Radar Proyeksi Jabatan</h4>
                                <p class="text-muted mb-0">Pantau pegawai yang paling dekat naik pangkat berdasarkan AK dan
                                    masa kerja.</p>
                            </div>
                            <form method="GET" action="{{ route('projections.index') }}" class="d-flex flex-wrap gap-2">
                                <input type="text" name="q" value="{{ $search }}" class="form-control"
                                    style="min-width: 220px;" placeholder="Cari nama atau NIP">
                                <select name="status" class="form-select" style="min-width: 160px;">
                                    <option value="all" @selected($status === 'all')>Semua Status</option>
                                    <option value="ready" @selected($status === 'ready')>Siap Secara AK</option>
                                    <option value="waiting" @selected($status === 'waiting')>Tertahan Minimum Waktu</option>
                                </select>

                                <select name="performance" class="form-select" style="min-width: 180px;">
                                    <option value="sangat_baik">Sangat Baik (150%)</option>
                                    <option value="baik" selected>Baik (100%)</option>
                                    <option value="butuh_perbaikan">Butuh Perbaikan (75%)</option>
                                    <option value="kurang">Kurang (50%)</option>
                                    <option value="sangat_kurang">Sangat Kurang (25%)</option>
                                </select>

                                <select name="target" class="form-select" style="min-width: 180px;">
                                    <option value="pangkat" selected>Target: PANGKAT</option>
                                    <option value="jenjang">Target: JENJANG</option>
                                </select>

                                <button type="submit" class="btn btn-primary">Terapkan</button>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Pegawai</th>
                                        <th>Jabatan</th>
                                        <th>Progress AK</th>
                                        <th>Estimasi</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
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
                                                <div class="fw-semibold">{{ $pegawai->nama_lengkap }}</div>
                                                <div class="text-muted small">{{ $pegawai->nip }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $pegawai->jabatan->nama_jabatan }}</div>
                                                <div class="text-muted small">{{ $pegawai->golongan->nama_golongan }} •
                                                    {{ $pegawai->unitKerja->nama_unit }}</div>
                                            </td>
                                            <td style="min-width: 220px;">
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span>{{ number_format($projection['current_ak'], 3, ',', '.') }} /
                                                        {{ number_format($projection['target_ak'], 0, ',', '.') }}</span>
                                                    <span>{{ number_format($progress, 2, ',', '.') }}%</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar {{ $ready ? 'bg-success' : ($held ? 'bg-warning' : 'bg-primary') }}"
                                                        role="progressbar" style="width: {{ $progress }}%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($projection['estimated_periods']))
                                                    <div class="fw-semibold">{{ $projection['estimated_periods'] }} periode</div>
                                                    <div class="text-muted small">(≈ {{ $projection['estimated_years'] }} tahun) • Target: {{ $projection['projected_year'] }}</div>
                                                @else
                                                    <div class="fw-semibold text-warning">Tidak dapat dihitung</div>
                                                    <div class="text-muted small">Periksa koefisien atau data jabatan</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($ready)
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle">Siap
                                                        AK</span>
                                                @elseif ($held)
                                                    <span
                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle">Menunggu
                                                        4 Tahun</span>
                                                @else
                                                    <span
                                                        class="badge bg-primary-subtle text-primary border border-primary-subtle">Proses
                                                        AK</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('pegawais.edit', $pegawai) }}"
                                                    class="btn btn-sm btn-outline-secondary">Detail Pegawai</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                Tidak ada data yang cocok dengan filter proyeksi saat ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Sorotan Cepat</h4>
                        @forelse ($highlights as $item)
                            @php
                                $pegawai = $item['pegawai'];
                                $projection = $item['projection'];
                            @endphp
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $pegawai->nama_lengkap }}</div>
                                        <div class="text-muted small">{{ $pegawai->jabatan->nama_jabatan }}</div>
                                    </div>
                                    <span
                                        class="badge bg-light text-dark border">{{ number_format($projection['progress_percentage'], 1, ',', '.') }}%</span>
                                </div>
                                <div class="progress mt-3" style="height: 6px;">
                                    <div class="progress-bar bg-info"
                                        style="width: {{ $projection['progress_percentage'] }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                    <span>Estimasi {{ $projection['estimated_years'] }} tahun</span>
                                    <span>{{ $projection['projected_year'] }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada data yang bisa ditampilkan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
