@extends('layouts.dashboard')

@section('title', 'Dashboard Proyeksi Jabatan')

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
            height: 100%;
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
            background: #f3f4f6;
            color: #4b5563;
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
        
        .modern-card {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }
    </style>
@endpush

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Dashboard Proyeksi Jabatan</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ringkasan</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-end">
                    <select class="form-control bg-white border-0 shadow-sm rounded">
                        <option selected>2026</option>
                        <option value="1">2025</option>
                        <option value="2">2024</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            @foreach ($summaryCards as $card)
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i data-feather="{{ $card['icon'] }}" width="24" height="24"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">{{ $card['title'] }}</p>
                            <h3 class="mb-0 text-dark fw-bolder">{{ $card['value'] }}</h3>
                        </div>
                        <i data-feather="{{ $card['icon'] }}" width="100" height="100" class="stat-bg-icon"></i>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="card-title mb-0">Ringkasan PAK</h4>
                                <p class="text-muted small mb-0">Data terakhir dan nilai rata-rata AK</p>
                            </div>
                            <span class="badge bg-primary">Sistem</span>
                        </div>

                        <div class="row text-muted mb-4">
                            <div class="col-6 mb-3">
                                <div class="small">Total catatan PAK</div>
                                <h3 class="mb-0">{{ $pakCount }}</h3>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="small">Rata-rata AK</div>
                                <h3 class="mb-0">{{ $averageAk ? number_format($averageAk, 3) : '-' }}</h3>
                            </div>
                        </div>

                        <div class="border rounded p-3 mb-3">
                            <div class="small text-muted">PAK terakhir</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $latestPak ? $latestPak->tanggal_pak->format('d M Y') : 'Belum ada data' }}</strong>
                                <span class="badge bg-success">AK
                                    {{ $latestPak ? number_format($latestPak->ak_total, 3) : '-' }}</span>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Pelaksanaan Ukom</span>
                                <strong>{{ $approvedPercent }}%</strong>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $approvedPercent }}%;" aria-valuenow="{{ $approvedPercent }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-3 text-muted small">
                                <div>Disetujui: {{ $approved }}</div>
                                <div>Belum: {{ $pending }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="card-title mb-0">Status Ukom Pegawai</h4>
                                <p class="text-muted small mb-0">Kondisi verifikasi pegawai dalam sistem</p>
                            </div>
                            <span class="badge bg-info">Evaluasi</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Terdaftar Ukom</div>
                                    <h3 class="mb-0">{{ $approved }}</h3>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="text-muted small">Belum Ukom</div>
                                    <h3 class="mb-0">{{ $pending }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Persentase pelaksanaan</span>
                                <strong>{{ $approvedPercent }}%</strong>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $approvedPercent }}%;" aria-valuenow="{{ $approvedPercent }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="small text-muted">Gunakan ringkasan ini untuk memprioritaskan perbaikan data dan
                            verifikasi jabatan.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 col-lg-12 mb-3">
                <div class="table-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">Pegawai Terbaru</h4>
                                <p class="text-muted small mb-0">Menampilkan 5 entri terakhir</p>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table modern-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Unit Kerja</th>
                                    <th>Jabatan</th>
                                    <th>Golongan</th>
                                    <th class="text-center">Ukom</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestEmployees as $employee)
                                    <tr>
                                        <td>
                                            <div class="fw-medium text-dark">{{ $employee->nama_lengkap }}</div>
                                            <div class="text-muted small">{{ $employee->nip }}</div>
                                        </td>
                                        <td>{{ $employee->unitKerja->nama_unit ?? '-' }}</td>
                                        <td>{{ $employee->jabatan->nama_jabatan ?? '-' }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $employee->golongan->nama_golongan ?? '-' }}</span></td>
                                        <td class="text-center">
                                            @if ($employee->status_ukom)
                                                <span class="badge bg-success bg-opacity-10 text-success border-0 px-2 py-1">Ya</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 px-2 py-1">Tidak</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada data pegawai terbaru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-12 mb-3">
                <div class="card modern-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">Jabatan dan Unit</h4>
                                <p class="text-muted small mb-0">Distribusi jabatan dan unit kerja utama</p>
                            </div>
                        </div>

                        @foreach ($jenjangCounts as $group)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">{{ $group->jenjang }}</span>
                                    <strong>{{ $group->total }}</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ min(100, round(($group->total / max(1, $summaryCards[1]['value'])) * 100)) }}%;"
                                        aria-valuenow="{{ $group->total }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <h6 class="mb-3">Unit Kerja Terbanyak</h6>
                            <ul class="list-group list-group-flush">
                                @forelse ($topUnits as $unit)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                        <span>{{ $unit->nama_unit }}</span>
                                        <span class="badge bg-primary rounded-pill">{{ $unit->total }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted px-0 py-3">Tidak ada data unit kerja.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
