@extends('layouts.dashboard')

@section('title', 'Detail Proyeksi Jabatan - ' . $pegawai->nama_lengkap)

@push('styles')
    <style>
        .konversi-table th,
        .konversi-table td {
            text-align: center;
            vertical-align: middle;
        }

        .konversi-table .active-predikat {
            background: #eef2ff;
            border: 2px solid #4f46e5;
            font-weight: 700;
        }

        .projection-comparison-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }

        .projection-comparison-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .projection-comparison-card.active {
            border-color: #4f46e5;
            background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
        }
    </style>
@endpush

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
            {{-- Card 1: Profile Info --}}
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

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Jabatan Saat Ini</small>
                            <div class="fw-medium text-dark">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Jenjang / Kategori</small>
                            <div class="fw-medium text-dark">
                                {{ $pegawai->jabatan->jenjang ?? '-' }}
                                @if ($pegawai->jabatan?->kategori)
                                    <span class="badge bg-light text-dark border ms-1">
                                        {{ ucfirst($pegawai->jabatan->kategori) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-0">
                            <small class="text-muted d-block mb-1">Koefisien AK Tahunan</small>
                            <div class="fw-medium text-primary fs-5">
                                {{ number_format((float) ($pegawai->jabatan->koefisien_tahunan ?? 0), 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Projection Summary --}}
            <div class="col-md-7 col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Ringkasan Proyeksi</h4>

                        <div class="row text-center mb-4">
                            <div class="col-3 border-end">
                                <h2 class="mb-0 text-dark">{{ number_format($projection['current_ak'], 2, ',', '.') }}</h2>
                                <span class="text-muted small">AK Saat Ini</span>
                            </div>
                            <div class="col-3 border-end">
                                <h2 class="mb-0 text-dark">{{ number_format($projection['target_ak'], 0, ',', '.') }}</h2>
                                <span class="text-muted small">Target AK</span>
                            </div>
                            <div class="col-3 border-end">
                                <h2 class="mb-0 {{ $projection['deficit_ak'] <= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($projection['deficit_ak'], 2, ',', '.') }}
                                </h2>
                                <span class="text-muted small">Kebutuhan AK</span>
                            </div>
                            <div class="col-3">
                                <h2 class="mb-0 text-primary">
                                    {{ number_format($projection['annual_ak'], 3, ',', '.') }}
                                </h2>
                                <span class="text-muted small">AK/Tahun ({{ $projection['predikat_label'] }})</span>
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

        {{-- Card 3: Konversi Predikat Kinerja Table --}}
        @if (!empty($konversiSummary))
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="card-title mb-1">Tabel Konversi Predikat Kinerja</h4>
                                    <p class="text-muted small mb-0">
                                        Nilai AK tahunan berdasarkan predikat kinerja untuk
                                        <strong>{{ $pegawai->jabatan->nama_jabatan }}
                                            ({{ $pegawai->jabatan->jenjang }})</strong>
                                    </p>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    Koefisien:
                                    {{ number_format((float) $pegawai->jabatan->koefisien_tahunan, 2, ',', '.') }}
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered konversi-table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Predikat</th>
                                            <th>Persentase</th>
                                            <th>AK Tahunan</th>
                                            <th>AK per Periode (6×/tahun)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($konversiSummary as $key => $data)
                                            <tr class="{{ $key === $projection['predikat'] ? 'active-predikat' : '' }}">
                                                <td>
                                                    <span
                                                        class="badge border {{ $data['badge_class'] }} px-2 py-1">{{ $data['label'] }}</span>
                                                </td>
                                                <td class="fw-medium">{{ number_format($data['persentase'], 0) }}%</td>
                                                <td class="fw-bold fs-5">
                                                    {{ number_format($data['nilai_ak'], 3, ',', '.') }}</td>
                                                <td>{{ number_format($data['nilai_ak'] / 6, 3, ',', '.') }}</td>
                                                <td>
                                                    @if ($key === $projection['predikat'])
                                                        <span class="badge bg-primary">Aktif</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Card 4: Projection Comparison per Predikat --}}
        @if (!empty($projectionComparison))
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="card-title mb-1">Simulasi Proyeksi per Predikat Kinerja</h4>
                            <p class="text-muted small mb-4">Perbandingan estimasi kenaikan pangkat berdasarkan skenario
                                predikat kinerja yang berbeda.</p>

                            <div class="row g-3">
                                @foreach ($projectionComparison as $predikat => $proj)
                                    @php
                                        $isActive = $predikat === 'baik';
                                        $badgeClass =
                                            $predikatBadgeClasses[$predikat] ?? 'bg-secondary-subtle text-secondary';
                                    @endphp
                                    <div class="col-12 col-md-6 col-xl">
                                        <div
                                            class="projection-comparison-card p-3 h-100 {{ $isActive ? 'active' : '' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <span
                                                    class="badge border {{ $badgeClass }} px-2 py-1">{{ $predikatLabels[$predikat] }}</span>
                                                @if ($isActive)
                                                    <span class="badge bg-primary" style="font-size: 0.65rem;">Default</span>
                                                @endif
                                            </div>

                                            <div class="mb-2">
                                                <div class="small text-muted">AK/Tahun</div>
                                                <div class="fw-bold fs-5 text-dark">
                                                    {{ number_format($proj['annual_ak'], 3, ',', '.') }}
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="small text-muted">Estimasi Tahun</div>
                                                <div class="fw-bold text-dark">
                                                    @if (isset($proj['estimated_years']))
                                                        {{ $proj['estimated_years'] }} tahun
                                                    @else
                                                        <span class="text-danger">—</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div>
                                                <div class="small text-muted">Target Tahun</div>
                                                <div class="fw-bold text-primary">{{ $proj['projected_year'] }}</div>
                                            </div>

                                            @if ($proj['is_ready_mathematically'])
                                                <div class="mt-2">
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle w-100 py-1">
                                                        Siap AK
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- Card 5: Chart --}}
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
            {{-- Card 6: History Table with predikat column --}}
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
                                        <th class="border-top-0">Predikat</th>
                                        <th class="border-top-0 text-end">AK Tambahan</th>
                                        <th class="border-top-0 text-end">Total AK</th>
                                        <th class="border-top-0 text-center">Perubahan</th>
                                        <th class="border-top-0 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $previousAk = null;
                                        $latestId = $pegawai->riwayatPaks->sortByDesc('tanggal_pak')->sortByDesc('id')->first()?->id;
                                    @endphp
                                    @forelse ($pegawai->riwayatPaks as $pak)
                                        @php
                                            $akTotal = (float) $pak->ak_total;
                                            $akTambahan = (float) $pak->ak_tambahan;
                                            $difference = $previousAk !== null ? $akTotal - $previousAk : null;
                                            $isLatest = $pak->id === $latestId;
                                        @endphp
                                        <tr class="{{ $isLatest ? 'table-primary' : '' }}">
                                            <td>{{ \Carbon\Carbon::parse($pak->tanggal_pak)->format('d/m/Y') }}</td>
                                            <td>{{ $pak->no_pak ?? '-' }}</td>
                                            <td>
                                                @if ($pak->predikat_kinerja)
                                                    <span class="badge border {{ $pak->predikat_badge_class }} px-2 py-1">
                                                        {{ $pak->predikat_label }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($akTambahan > 0)
                                                    <span
                                                        class="text-success fw-medium">+{{ number_format($akTambahan, 3, ',', '.') }}</span>
                                                @else
                                                    <span
                                                        class="text-muted">{{ number_format($akTambahan, 3, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-medium">{{ number_format($akTotal, 3, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if ($difference !== null)
                                                    @if ($difference > 0)
                                                        <span
                                                            class="badge bg-success">+{{ number_format($difference, 3, ',', '.') }}</span>
                                                    @elseif ($difference < 0)
                                                        <span
                                                            class="badge bg-danger">{{ number_format($difference, 3, ',', '.') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">0</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-info">Awal</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($isLatest)
                                                    <span class="badge bg-primary">Terbaru</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $previousAk = $akTotal; @endphp
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat PAK.
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
