@extends('layouts.dashboard')

@section('title', 'Tambah Kinerja Tahunan')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Tambah Kinerja Tahunan</h1>
                <p class="text-muted">Pegawai: <strong>{{ $pegawai->nama_lengkap }}</strong></p>
            </div>
            <a href="{{ route('projections.show', $pegawai) }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left" width="16" height="16" class="me-1"></i> Kembali
            </a>
        </div>

        <div class="row">
            {{-- Info Pegawai Sidebar --}}
            <div class="col-md-5 col-lg-4 mb-4">
                <div class="position-sticky" style="top: 90px;">
                    @php
                        $infoItems = [
                            ['label' => 'Unit Kerja', 'value' => $pegawai->unitKerja->nama_unit ?? '-'],
                            ['label' => 'Golongan Saat Ini', 'value' => $pegawai->golongan->nama_golongan ?? '-'],
                            ['label' => 'Jabatan Saat Ini', 'value' => $pegawai->jabatan->nama_jabatan ?? '-'],
                            [
                                'label' => 'Jenjang / Kategori',
                                'value' => ($pegawai->jabatan->jenjang ?? '-') .
                                    ($pegawai->jabatan?->kategori
                                        ? ' <span class="badge bg-light text-dark border ms-1">' . ucfirst($pegawai->jabatan->kategori) . '</span>'
                                        : '')
                            ],
                            ['label' => 'TMT Jabatan', 'value' => $pegawai->tmt_jabatan ? \Carbon\Carbon::parse($pegawai->tmt_jabatan)->format('d F Y') : '-'],
                            [
                                'label' => 'Koefisien AK Tahunan',
                                'value' => '<span class="text-primary fs-5 fw-bold">' .
                                    number_format((float)($pegawai->jabatan->koefisien_tahunan ?? 0), 2, ',', '.') .
                                    '</span>'
                            ],
                        ];
                    @endphp
                    <x-info-card title="Informasi Pegawai" :items="$infoItems" />
                </div>
            </div>

            {{-- Form Kinerja --}}
            <div class="col-md-7 col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Form Kinerja Tahunan</h4>
                        </div>
                        
                        <div class="alert alert-info border-info-subtle d-flex align-items-start gap-2 mb-4 py-2 px-3" style="background-color: #f0f9ff; color: #0369a1; border-radius: 0.5rem;">
                            <i data-feather="info" width="18" height="18" class="mt-1 flex-shrink-0" style="color: #0284c7;"></i>
                            <div class="small" style="line-height: 1.5;">
                                Nilai <strong>Angka Kredit (AK)</strong> akan dikonversi secara otomatis berdasarkan Predikat Kinerja yang Anda pilih, 
                                dikalikan dengan <strong>Koefisien AK Tahunan</strong> jabatan pegawai saat ini.
                            </div>
                        </div>

                        <form action="{{ route('kinerja-tahunans.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="tahun" class="form-label text-muted fw-medium small text-uppercase">Tahun Penilaian <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg @error('tahun') is-invalid @enderror" id="tahun"
                                        name="tahun" value="{{ old('tahun', date('Y')) }}" required min="2000" max="2099">
                                    @error('tahun')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="predikat" class="form-label text-muted fw-medium small text-uppercase">Predikat Kinerja <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg @error('predikat') is-invalid @enderror" id="predikat" name="predikat" required>
                                        <option value="" disabled selected>Pilih Predikat...</option>
                                        @foreach($predikatOptions as $option)
                                            <option value="{{ $option }}" {{ old('predikat') === $option ? 'selected' : '' }}>
                                                {{ $predikatLabels[$option] ?? ucfirst($option) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('predikat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i data-feather="save" width="16" height="16" class="me-1"></i> Simpan Kinerja
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
