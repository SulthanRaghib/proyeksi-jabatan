@extends('layouts.dashboard')

@section('title', 'Detail Proyeksi Jabatan - ' . $pegawai->nama_lengkap)

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-dark font-weight-medium mb-1">Detail Proyeksi Jabatan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('projections.index') }}" class="text-muted">Proyeksi
                                Jabatan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $pegawai->nama_lengkap }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                <a href="{{ route('projections.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="feather-icon me-1"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <h2 class="mb-0 me-3">{{ $pegawai->nama_lengkap }}</h2>
                    @if ($projection['is_held_by_speedbump'])
                        <span
                            class="badge bg-warning-subtle text-warning border border-warning-subtle fs-6 px-3 py-2">Tertahan
                            Waktu</span>
                    @elseif ($projection['is_ready_mathematically'])
                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-6 px-3 py-2">Siap
                            AK</span>
                    @else
                        <span
                            class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-2">Proses
                            AK</span>
                    @endif
                </div>
                <p class="text-muted mt-1 fs-5 mb-0">NIP: {{ $pegawai->nip }}</p>
            </div>
        </div>

        <div class="row">
            <!-- Card 1: Profile Info -->
            <div class="col-md-5 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Informasi Pegawai</h4>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Unit Kerja</small>
                            <div class="fw-medium text-dark">{{ $pegawai->unitKerja->nama_unit ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Golongan Saat Ini</small>
                            <div class="fw-medium text-dark">{{ $pegawai->golongan->nama_golongan ?? '-' }}</div>
                        </div>

                        <div class="mb-0">
                            <small class="text-muted d-block mb-1">Jabatan Saat Ini</small>
                            <div class="fw-medium text-dark">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Projection Summary -->
            <div class="col-md-7 col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Ringkasan Proyeksi</h4>

                        <div class="row text-center mb-4">
                            <div class="col-4 border-end">
                                <h2 class="mb-0 text-dark">{{ number_format($projection['current_ak'], 2, ',', '.') }}</h2>
                                <span class="text-muted small">AK Saat Ini</span>
                            </div>
                            <div class="col-4 border-end">
                                <h2 class="mb-0 text-dark">{{ number_format($projection['target_ak'], 0, ',', '.') }}</h2>
                                <span class="text-muted small">Target AK</span>
                            </div>
                            <div class="col-4">
                                <h2 class="mb-0 {{ $projection['deficit_ak'] <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($projection['deficit_ak'], 2, ',', '.') }}
                                </h2>
                                <span class="text-muted small">Kebutuhan AK</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Progres Pencapaian</span>
                                <span
                                    class="fw-medium">{{ number_format($projection['progress_percentage'], 1, ',', '.') }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{ $projection['is_held_by_speedbump'] ? 'bg-warning' : ($projection['is_ready_mathematically'] ? 'bg-success' : 'bg-primary') }}"
                                    role="progressbar" style="width: {{ min($projection['progress_percentage'], 100) }}%"
                                    aria-valuenow="{{ min($projection['progress_percentage'], 100) }}" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center bg-light rounded p-3">
                            <div class="me-3 text-primary">
                                <i data-feather="calendar" width="24" height="24"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark">Estimasi Kenaikan: Tahun {{ $projection['projected_year'] }}
                                </h5>
                                <small class="text-muted">Dibutuhkan sekitar {{ $projection['estimated_years'] }} tahun
                                    dari sekarang.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Card 3: Chart -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body overflow-hidden">
                        <h4 class="card-title mb-4">Tren Angka Kredit</h4>
                        <div id="ak-trend-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Card 4: History Table -->
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Riwayat Angka Kredit</h4>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Tahun</th>
                                        <th class="border-top-0">No. PAK</th>
                                        <th class="border-top-0">Total AK</th>
                                        <th class="border-top-0">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pegawai->riwayatPaks as $pak)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pak->tanggal_pak)->format('Y') }}</td>
                                            <td>{{ $pak->no_pak ?? '-' }}</td>
                                            <td class="fw-medium">{{ number_format($pak->ak_total, 3, ',', '.') }}</td>
                                            <td>
                                                @if ($pak->is_latest)
                                                    <span class="badge bg-primary">Terbaru</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat PAK.
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
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof c3 !== 'undefined') {
                var chartYears = {!! json_encode($chartYears) !!};
                var chartAk = {!! json_encode($chartAk) !!};

                // Only generate chart if there's data
                if (chartYears.length > 0 && chartAk.length > 0) {
                    var chart = c3.generate({
                        bindto: '#ak-trend-chart',
                        data: {
                            x: 'x',
                            columns: [
                                ['x', ...chartYears],
                                ['Total AK', ...chartAk]
                            ],
                            type: 'line',
                            colors: {
                                'Total AK': '#4f46e5'
                            }
                        },
                        axis: {
                            x: {
                                type: 'category',
                                tick: {
                                    centered: true
                                }
                            },
                            y: {
                                tick: {
                                    format: d3.format(".2f")
                                }
                            }
                        },
                        grid: {
                            y: {
                                show: true
                            }
                        },
                        point: {
                            r: 4
                        }
                    });
                } else {
                    document.getElementById('ak-trend-chart').innerHTML =
                        '<div class="d-flex justify-content-center align-items-center h-100 text-muted">Data trend tidak tersedia.</div>';
                }

                // Re-render icons if feather is available
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
    </script>
@endpush
