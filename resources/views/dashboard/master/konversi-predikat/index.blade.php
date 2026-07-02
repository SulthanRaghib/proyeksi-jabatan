@extends('layouts.dashboard')

@section('title', 'Data Master - Konversi Predikat Kinerja')

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

        /* Modern Filters */
        .filter-card {
            border-radius: 1rem;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .custom-input {
            border-radius: 0.75rem;
            padding: 0.6rem 1rem;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            transition: all 0.2s;
        }
        
        .custom-input:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        /* Jabatan Cards Overhaul */
        .jabatan-card {
            border: 1px solid #f3f4f6;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .jabatan-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
            border-color: #e5e7eb;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.6rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 500;
            background: #f3f4f6;
            color: #4b5563;
            margin-right: 0.5rem;
            margin-bottom: 0.25rem;
            border: 1px solid #e5e7eb;
        }
        
        .kategori-badge-keahlian {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            box-shadow: 0 4px 6px rgba(124, 58, 237, 0.2);
        }

        .kategori-badge-keterampilan {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
            box-shadow: 0 4px 6px rgba(14, 165, 233, 0.2);
        }

        .konversi-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.75rem;
        }

        .konversi-item {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .konversi-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.875rem 0.5rem;
            border-radius: 0.625rem;
            text-align: center;
            flex-grow: 1;
            transition: all 0.2s ease;
            border-width: 1px;
            border-style: solid;
        }

        .konversi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .konversi-card.empty {
            background: #f9fafb;
            border-color: #e5e7eb;
            border-style: dashed;
            color: #9ca3af;
        }

        .predikat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
            height: 1.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ak-value {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.35rem;
        }

        .persentase-badge {
            font-size: 0.65rem;
            padding: 0.15rem 0.4rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.5);
            font-weight: 600;
        }

        /* Action buttons inherited from shared-styles, just add opacity hover effect */
        .konversi-item .d-flex.justify-content-center {
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .konversi-item:hover .d-flex.justify-content-center {
            opacity: 1;
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
        <div class="row mb-4 g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i data-feather="repeat" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Total Konversi</p>
                        <h3 class="mb-0 text-dark fw-bolder">{{ $stats['total_konversi'] }}</h3>
                    </div>
                    <i data-feather="repeat" width="100" height="100" class="stat-bg-icon text-primary"></i>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i data-feather="briefcase" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Total Jabatan</p>
                        <h3 class="mb-0 text-dark fw-bolder">{{ $stats['total_jabatan'] }}</h3>
                    </div>
                    <i data-feather="briefcase" width="100" height="100" class="stat-bg-icon text-info"></i>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i data-feather="check-circle" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Jabatan Terisi</p>
                        <h3 class="mb-0 text-success fw-bolder">{{ $stats['jabatan_terisi'] }}</h3>
                    </div>
                    <i data-feather="check-circle" width="100" height="100" class="stat-bg-icon text-success"></i>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i data-feather="alert-circle" width="24" height="24"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">Belum Terisi</p>
                        <h3 class="mb-0 text-warning fw-bolder">{{ $stats['jabatan_belum_terisi'] }}</h3>
                    </div>
                    <i data-feather="alert-circle" width="100" height="100" class="stat-bg-icon text-warning"></i>
                </div>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="filter-card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('konversi-predikats.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                                <i data-feather="search" width="16" height="16"></i>
                            </span>
                            <input type="text" name="q" value="{{ $search }}" class="form-control custom-input border-start-0 ps-0"
                                placeholder="Cari nama jabatan atau jenjang...">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <select name="kategori" class="form-select custom-input">
                            <option value="" @selected($filterKategori === '')>Semua Kategori</option>
                            <option value="keahlian" @selected($filterKategori === 'keahlian')>Keahlian</option>
                            <option value="keterampilan" @selected($filterKategori === 'keterampilan')>Keterampilan</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm">
                            Terapkan Filter
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
                                    <h5 class="mb-2 fw-bold text-dark d-flex align-items-center gap-2">
                                        <div class="bg-light rounded p-1 text-primary">
                                            <i data-feather="user" width="18" height="18"></i>
                                        </div>
                                        {{ $jabatan->nama_jabatan }}
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border fw-normal">{{ $jabatan->jenjang }}</span>
                                    </h5>
                                    <div class="d-flex flex-wrap mt-2">
                                        <div class="meta-pill" title="Koefisien Tahunan">
                                            <i data-feather="hash" width="12" height="12" class="text-primary"></i>
                                            Koefisien: <strong class="ms-1">{{ number_format((float) $jabatan->koefisien_tahunan, 2, ',', '.') }}</strong>
                                        </div>
                                        <div class="meta-pill" title="Target Kenaikan Pangkat">
                                            <i data-feather="trending-up" width="12" height="12" class="text-success"></i>
                                            Target Pangkat: <strong class="ms-1">{{ number_format((int) $jabatan->target_ak_kenaikan_pangkat, 0, ',', '.') }}</strong>
                                        </div>
                                        @if ($jabatan->target_ak_kenaikan_jenjang)
                                            <div class="meta-pill" title="Target Kenaikan Jenjang">
                                                <i data-feather="star" width="12" height="12" class="text-warning"></i>
                                                Target Jenjang: <strong class="ms-1">{{ number_format((int) $jabatan->target_ak_kenaikan_jenjang, 0, ',', '.') }}</strong>
                                            </div>
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
                            <div class="konversi-grid mt-3">
                                @foreach (\App\Models\KonversiPredikatKinerja::PREDIKAT_OPTIONS as $predikat)
                                    @php
                                        $konversi = $predikatMap->get($predikat);
                                        $label = \App\Models\KonversiPredikatKinerja::PREDIKAT_LABELS[$predikat];
                                        $badgeClass = \App\Models\KonversiPredikatKinerja::PREDIKAT_BADGE_CLASSES[$predikat] ?? 'bg-light border-secondary text-secondary';
                                    @endphp
                                    <div class="konversi-item">
                                        @if ($konversi)
                                            <div class="konversi-card {{ $badgeClass }}">
                                                <div class="predikat-label opacity-75">{{ $label }}</div>
                                                <div class="ak-value">{{ number_format((float) $konversi->nilai_ak, 3, ',', '.') }}</div>
                                                <div class="persentase-badge">
                                                    {{ number_format((float) $konversi->persentase, 0) }}%
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center gap-2 mt-2">
                                                <x-action-button type="edit" :href="route('konversi-predikats.edit', $konversi)" />
                                                <form action="{{ route('konversi-predikats.destroy', $konversi) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus konversi ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-action-button type="delete" />
                                                </form>
                                            </div>
                                        @else
                                            <div class="konversi-card empty">
                                                <div class="predikat-label">{{ $label }}</div>
                                                <div class="ak-value text-muted">—</div>
                                                <div class="small fw-normal mt-1">Kosong</div>
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
