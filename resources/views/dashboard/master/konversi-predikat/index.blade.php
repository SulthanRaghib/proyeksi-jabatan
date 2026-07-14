@extends('layouts.dashboard')

@section('title', 'Data Master - Konversi Predikat Kinerja')

@push('styles')
    <style>
        /* Jabatan Cards — unique to this page */
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
    <x-page-header title="Konversi Predikat Kinerja" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Data Master'],
        ['label' => 'Konversi Predikat'],
    ]">
        <x-slot:action>
            <a href="{{ route('konversi-predikats.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="feather-icon me-1"></i>
                Tambah Konversi
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="container-fluid">
        <x-alert-flash />

        {{-- Stats Row --}}
        <div class="row mb-4 g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Total Konversi" :value="$stats['total_konversi']" icon="repeat" color="primary" />
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Total Jabatan" :value="$stats['total_jabatan']" icon="briefcase" color="info" />
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Jabatan Terisi" :value="$stats['jabatan_terisi']" icon="check-circle" color="success" />
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-stat-card title="Belum Terisi" :value="$stats['jabatan_belum_terisi']" icon="alert-circle" color="warning" />
            </div>
        </div>

        {{-- Filter & Search --}}
        <x-filter-bar :action="route('konversi-predikats.index')" :searchValue="$search" placeholder="Cari nama jabatan atau jenjang...">
            <div class="col-12 col-md-3">
                <select name="kategori" class="form-select custom-input">
                    <option value="" @selected($filterKategori === '')>Semua Kategori</option>
                    <option value="keahlian" @selected($filterKategori === 'keahlian')>Keahlian</option>
                    <option value="keterampilan" @selected($filterKategori === 'keterampilan')>Keterampilan</option>
                </select>
            </div>
        </x-filter-bar>

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
                                        <form action="{{ route('konversi-predikats.generate') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="jabatan_id" value="{{ $jabatan->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Generate otomatis dari koefisien">
                                                <i data-feather="zap" width="14" height="14"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('konversi-predikats.generate') }}" method="POST" class="d-inline">
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
                                                <x-action-button type="delete_modal" 
                                                    action="{{ route('konversi-predikats.destroy', $konversi) }}" 
                                                    message="Hapus konversi ini?" />
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
            <x-empty-state icon="inbox" title="Tidak ada data" description="Tidak ada data yang cocok dengan filter pencarian Anda." />
        @endif
    </div>
@endsection
