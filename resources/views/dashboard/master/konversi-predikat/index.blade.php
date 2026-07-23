@extends('layouts.dashboard')

@section('title', 'Data Master - Konversi Predikat Kinerja')

@section('content')
    <x-page-header title="Konversi Predikat Kinerja" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Data Master', 'url' => '#'],
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

        {{-- Group Tables by Category (Keahlian vs Keterampilan) --}}
        @php
            $grouped = $jabatans->groupBy('kategori');
        @endphp

        @forelse ($grouped as $kategori => $jabatanGroup)
            <div class="d-flex align-items-center mb-3 mt-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge px-3 py-2 fs-6 {{ $kategori === 'keahlian' ? 'bg-primary text-dark' : 'bg-success text-dark' }}">
                        <i data-feather="{{ $kategori === 'keahlian' ? 'award' : 'tool' }}" width="16" height="16" class="me-1"></i>
                        Kategori {{ $kategori === 'keahlian' ? 'Keahlian' : 'Keterampilan' }}
                    </span>
                    <span class="text-muted small fw-bold">({{ $jabatanGroup->count() }} Jabatan)</span>
                </div>
                <hr class="flex-grow-1 ms-3 my-0">
            </div>

            <div class="table-card mb-4">
                <div class="table-responsive">
                    <table class="table modern-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 220px;">Jabatan & Jenjang</th>
                                <th class="text-center" style="width: 100px;">Koefisien</th>
                                <th class="text-center" style="background-color: #f0fdf4 !important; color: #15803d !important;">Sangat Baik (150%)</th>
                                <th class="text-center" style="background-color: #eff6ff !important; color: #1d4ed8 !important;">Baik (100%)</th>
                                <th class="text-center" style="background-color: #fefce8 !important; color: #a16207 !important;">Butuh Perbaikan (75%)</th>
                                <th class="text-center" style="background-color: #fff1f2 !important; color: #be123c !important;">Kurang (50%)</th>
                                <th class="text-center" style="background-color: #f8fafc !important; color: #475569 !important;">Sangat Kurang (25%)</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jabatanGroup as $jabatan)
                                @php
                                    $konversis = $jabatan->konversiPredikat;
                                    $hasKonversi = $konversis->isNotEmpty();
                                    $predikatMap = $konversis->keyBy('predikat');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark font-15">{{ $jabatan->nama_jabatan }}</div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge bg-light text-dark border">{{ $jabatan->jenjang }}</span>
                                            <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($jabatan->kategori) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ number_format((float) $jabatan->koefisien_tahunan, 2, ',', '.') }}</span>
                                    </td>
                                    @foreach (['sangat_baik', 'baik', 'butuh_perbaikan', 'kurang', 'sangat_kurang'] as $pred)
                                        @php
                                            $konversi = $predikatMap->get($pred);
                                            $label = \App\Models\KonversiPredikatKinerja::PREDIKAT_LABELS[$pred] ?? $pred;
                                        @endphp
                                        <td class="text-center">
                                            @if($konversi)
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <span class="fw-bold text-dark fs-6">{{ number_format((float) $konversi->nilai_ak, 3, ',', '.') }}</span>
                                                    <div class="d-inline-flex gap-1 ms-1 opacity-75">
                                                        <a href="{{ route('konversi-predikats.edit', $konversi) }}" class="action-btn action-btn--edit" style="width: 24px; height: 24px;" title="Edit {{ $label }}">
                                                            <i data-feather="edit-2" width="11" height="11"></i>
                                                        </a>
                                                        <button type="button" class="action-btn action-btn--delete" style="width: 24px; height: 24px;" title="Hapus {{ $label }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#globalConfirmModal"
                                                            data-bs-action="{{ route('konversi-predikats.destroy', $konversi) }}"
                                                            data-bs-method="DELETE"
                                                            data-bs-btn-text="Hapus"
                                                            data-bs-btn-class="btn-danger"
                                                            data-bs-show-subtext="false"
                                                            data-bs-message="Yakin ingin menghapus nilai konversi {{ $label }} untuk {{ $jabatan->nama_jabatan }}?">
                                                            <i data-feather="trash-2" width="11" height="11"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            @if (!$hasKonversi)
                                                <form action="{{ route('konversi-predikats.generate') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="jabatan_id" value="{{ $jabatan->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success px-2.5 py-1" title="Generate otomatis 5 predikat dari koefisien">
                                                        <i data-feather="zap" width="13" height="13" class="me-1"></i> Generate
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('konversi-predikats.generate') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="jabatan_id" value="{{ $jabatan->id }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary px-2 py-1"
                                                        title="Regenerate dari koefisien"
                                                        onclick="return confirm('Regenerate akan menimpa seluruh nilai predikat yang ada. Lanjutkan?')">
                                                        <i data-feather="refresh-cw" width="13" height="13" class="me-1"></i> Reset
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <x-empty-state icon="inbox" title="Tidak ada data" description="Tidak ada data jabatan yang cocok dengan filter pencarian Anda." />
        @endforelse
    </div>
@endsection
