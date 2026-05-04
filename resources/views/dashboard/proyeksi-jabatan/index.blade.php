@extends('layouts.dashboard')

@section('title', 'Proyeksi Jabatan')

@push('styles')
<style>
    .border-left-primary { border-left: 4px solid #4f46e5 !important; }
    .border-left-success { border-left: 4px solid #10b981 !important; }
    .border-left-warning { border-left: 4px solid #f59e0b !important; }
    .border-left-info { border-left: 4px solid #0ea5e9 !important; }
</style>
@endpush

@section('content')
    <div class="page-breadcrumb mb-4">
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

    <div class="container-fluid px-0">
        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Pegawai Dipantau</p>
                                <h3 class="mb-0 text-dark">{{ $stats['total'] }}</h3>
                            </div>
                            <div class="ms-auto text-primary">
                                <i data-feather="users" width="32" height="32"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Siap Secara AK</p>
                                <h3 class="mb-0 text-success">{{ $stats['ready'] }}</h3>
                            </div>
                            <div class="ms-auto text-success">
                                <i data-feather="check-circle" width="32" height="32"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 border-left-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tertahan Waktu</p>
                                <h3 class="mb-0 text-warning">{{ $stats['speedbump'] }}</h3>
                            </div>
                            <div class="ms-auto text-warning">
                                <i data-feather="clock" width="32" height="32"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="text-muted mb-1">Rata-rata Progres</p>
                                <h3 class="mb-0 text-info">{{ $stats['avg_progress'] }}%</h3>
                            </div>
                            <div class="ms-auto text-info">
                                <i data-feather="trending-up" width="32" height="32"></i>
                            </div>
                        </div>
                    </div>
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
                                <p class="text-muted mb-0 small">Pantau status kenaikan pangkat berdasarkan Angka Kredit (AK).</p>
                            </div>
                        </div>

                        <!-- Filter Form yang lebih clean menyatu dengan background putih -->
                        <form method="GET" action="{{ route('projections.index') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama atau NIP...">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="all" @selected($status === 'all')>Semua Status</option>
                                        <option value="ready" @selected($status === 'ready')>Siap AK</option>
                                        <option value="waiting" @selected($status === 'waiting')>Tertahan Waktu</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="performance" class="form-select">
                                        <option value="sangat_baik" @selected($performance === 'sangat_baik')>Kinerja: Sangat Baik</option>
                                        <option value="baik" @selected($performance === 'baik')>Kinerja: Baik</option>
                                        <option value="butuh_perbaikan" @selected($performance === 'butuh_perbaikan')>Kinerja: Butuh Perbaikan</option>
                                        <option value="kurang" @selected($performance === 'kurang')>Kinerja: Kurang</option>
                                        <option value="sangat_kurang" @selected($performance === 'sangat_kurang')>Kinerja: Sangat Kurang</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-outline-primary w-100">Cari</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Pegawai</th>
                                        <th class="border-top-0">Jabatan & Unit</th>
                                        <th class="border-top-0" style="min-width: 180px;">Progress AK</th>
                                        <th class="border-top-0">Estimasi</th>
                                        <th class="border-top-0">Status</th>
                                        <th class="border-top-0 text-center">Aksi</th>
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
                                                <div class="text-muted small">{{ $pegawai->golongan->nama_golongan }} • {{ $pegawai->unitKerja->nama_unit }}</div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span>{{ number_format($projection['current_ak'], 2, ',', '.') }} / {{ number_format($projection['target_ak'], 0, ',', '.') }}</span>
                                                    <span class="fw-medium">{{ number_format($progress, 1, ',', '.') }}%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $held ? 'bg-warning' : ($ready ? 'bg-success' : 'bg-primary') }}" role="progressbar" style="width: {{ min($progress, 100) }}%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($projection['estimated_periods']))
                                                    <div class="fw-medium">{{ $projection['estimated_periods'] }} Periode</div>
                                                    <div class="text-muted small">Target: {{ $projection['projected_year'] }}</div>
                                                @else
                                                    <div class="text-danger small">Tak Terukur</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($held)
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Tertahan Waktu</span>
                                                @elseif ($ready)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Siap AK</span>
                                                @else
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Proses AK</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('pegawais.edit', $pegawai) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
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

            <!-- Sorotan Cepat -->
            <div class="col-12 col-xl-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Sorotan Cepat</h4>
                        </div>
                        
                        <div>
                            @forelse ($highlights as $item)
                                @php
                                    $pegawai = $item['pegawai'];
                                    $projection = $item['projection'];
                                @endphp
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-medium text-dark">{{ $pegawai->nama_lengkap }}</div>
                                            <div class="text-muted small">{{ $pegawai->jabatan->nama_jabatan }}</div>
                                        </div>
                                        <span class="badge bg-primary">{{ number_format($projection['progress_percentage'], 0) }}%</span>
                                    </div>
                                    
                                    <div class="progress mb-2" style="height: 5px;">
                                        <div class="progress-bar bg-primary" style="width: {{ min($projection['progress_percentage'], 100) }}%"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between text-muted small">
                                        <span>Estimasi {{ $projection['estimated_years'] }} tahun</span>
                                        <span class="fw-medium text-dark">Target {{ $projection['projected_year'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">Belum ada data sorotan.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @if(count($highlights) > 0)
                            <div class="text-center mt-3">
                                <a href="{{ route('projections.index', ['status' => 'ready']) }}" class="text-primary text-decoration-none small">Lihat Semua Siap AK &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
