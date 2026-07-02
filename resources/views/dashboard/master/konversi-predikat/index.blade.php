@extends('layouts.dashboard')

@section('title', 'Data Master - Konversi Predikat Kinerja')

@push('styles')
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.15s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card.border-primary {
            border-left-color: #4f46e5 !important;
        }

        .stat-card.border-success {
            border-left-color: #10b981 !important;
        }

        .stat-card.border-warning {
            border-left-color: #f59e0b !important;
        }

        .stat-card.border-info {
            border-left-color: #0ea5e9 !important;
        }

        .konversi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }

        .konversi-cell {
            text-align: center;
            padding: 0.5rem 0.25rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .konversi-cell:hover {
            transform: scale(1.05);
        }

        .konversi-cell.filled {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .konversi-cell.empty {
            background: #fef2f2;
            color: #991b1b;
            border: 1px dashed #fecaca;
        }

        .jabatan-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: box-shadow 0.2s ease;
        }

        .jabatan-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .kategori-badge-keahlian {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
        }

        .kategori-badge-keterampilan {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
        }

        .predikat-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-dark font-weight-medium mb-1">Konversi Predikat Kinerja</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#" class="text-muted">Data Master</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Konversi Predikat</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                <a href="{{ route('konversi-predikats.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="feather-icon me-1"></i>
                    Tambah Konversi
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i data-feather="check-circle" class="me-2" width="18" height="18"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stats Row --}}
        <div class="row mb-4">
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-card border-primary h-100">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Total Konversi</p>
                        <h3 class="mb-0 text-dark">{{ $stats['total_konversi'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-card border-info h-100">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Total Jabatan</p>
                        <h3 class="mb-0 text-dark">{{ $stats['total_jabatan'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-card border-success h-100">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Jabatan Terisi</p>
                        <h3 class="mb-0 text-success">{{ $stats['jabatan_terisi'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-card border-warning h-100">
                    <div class="card-body py-3">
                        <p class="text-muted mb-1 small">Belum Terisi</p>
                        <h3 class="mb-0 text-warning">{{ $stats['jabatan_belum_terisi'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('konversi-predikats.index') }}" class="row g-2">
                    <div class="col-12 col-md-6">
                        <input type="text" name="q" value="{{ $search }}" class="form-control"
                            placeholder="Cari nama jabatan atau jenjang...">
                    </div>
                    <div class="col-12 col-md-3">
                        <select name="kategori" class="form-select">
                            <option value="" @selected($filterKategori === '')>Semua Kategori</option>
                            <option value="keahlian" @selected($filterKategori === 'keahlian')>Keahlian</option>
                            <option value="keterampilan" @selected($filterKategori === 'keterampilan')>Keterampilan</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i data-feather="search" class="me-1" width="16" height="16"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Konversi Cards per Jabatan --}}
        @php
            $grouped = $jabatans->groupBy('kategori');
        @endphp

        @foreach ($grouped as $kategori => $jabatanGroup)
            <div class="d-flex align-items-center mb-3 mt-4">
                <span class="badge px-3 py-2 me-2 kategori-badge-{{ $kategori }}">
                    {{ $kategori === 'keahlian' ? 'Keahlian' : 'Keterampilan' }}
                </span>
                <hr class="flex-grow-1 m-0">
            </div>

            <div class="row">
                @foreach ($jabatanGroup as $jabatan)
                    @php
                        $konversis = $jabatan->konversiPredikat;
                        $hasKonversi = $konversis->isNotEmpty();
                        $predikatMap = $konversis->keyBy('predikat');
                    @endphp
                    <div class="col-12 col-xl-6 mb-4">
                        <div class="jabatan-card bg-white p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">
                                        {{ $jabatan->nama_jabatan }}
                                        <span class="text-muted fw-normal">— {{ $jabatan->jenjang }}</span>
                                    </h5>
                                    <div class="small text-muted">
                                        Koefisien Tahunan:
                                        <strong>{{ number_format((float) $jabatan->koefisien_tahunan, 2, ',', '.') }}</strong>
                                        &bull; Target Pangkat:
                                        <strong>{{ number_format((int) $jabatan->target_ak_kenaikan_pangkat, 0, ',', '.') }}</strong>
                                        @if ($jabatan->target_ak_kenaikan_jenjang)
                                            &bull; Target Jenjang:
                                            <strong>{{ number_format((int) $jabatan->target_ak_kenaikan_jenjang, 0, ',', '.') }}</strong>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    @if (!$hasKonversi)
                                        <form action="{{ route('konversi-predikats.generate') }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="jabatan_id" value="{{ $jabatan->id }}">
                                            <button type="submit" class="btn btn-sm btn-success"
                                                title="Generate otomatis dari koefisien">
                                                <i data-feather="zap" width="14" height="14"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('konversi-predikats.generate') }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="jabatan_id" value="{{ $jabatan->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                title="Regenerate dari koefisien"
                                                onclick="return confirm('Regenerate akan menimpa nilai yang ada. Lanjutkan?')">
                                                <i data-feather="refresh-cw" width="14" height="14"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Predikat Grid --}}
                            <div class="konversi-grid">
                                @foreach (\App\Models\KonversiPredikatKinerja::PREDIKAT_OPTIONS as $predikat)
                                    @php
                                        $konversi = $predikatMap->get($predikat);
                                        $label = \App\Models\KonversiPredikatKinerja::PREDIKAT_LABELS[$predikat];
                                    @endphp
                                    <div>
                                        <div class="predikat-header text-muted mb-1">{{ $label }}</div>
                                        @if ($konversi)
                                            <div class="konversi-cell filled">
                                                {{ number_format((float) $konversi->nilai_ak, 3, ',', '.') }}
                                                <div class="small fw-normal text-muted">
                                                    {{ number_format((float) $konversi->persentase, 0) }}%
                                                </div>
                                            </div>
                                            <div class="mt-1 d-flex justify-content-center gap-1">
                                                <a href="{{ route('konversi-predikats.edit', $konversi) }}"
                                                    class="btn btn-sm btn-link p-0 text-warning" title="Edit">
                                                    <i data-feather="edit-2" width="12" height="12"></i>
                                                </a>
                                                <form action="{{ route('konversi-predikats.destroy', $konversi) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus konversi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger"
                                                        title="Hapus">
                                                        <i data-feather="trash-2" width="12" height="12"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="konversi-cell empty">
                                                —
                                                <div class="small fw-normal">Kosong</div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        @if ($jabatans->isEmpty())
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i data-feather="inbox" class="text-muted mb-3" width="48" height="48"></i>
                    <p class="text-muted mb-0">Tidak ada data yang cocok dengan filter pencarian Anda.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
