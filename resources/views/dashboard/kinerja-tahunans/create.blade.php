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

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('kinerja-tahunans.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tahun" class="form-label">Tahun Penilaian <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun"
                                name="tahun" value="{{ old('tahun', date('Y')) }}" required min="2000" max="2099">
                            @error('tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="predikat" class="form-label">Predikat Kinerja <span class="text-danger">*</span></label>
                            <select class="form-select @error('predikat') is-invalid @enderror" id="predikat" name="predikat" required>
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
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" width="16" height="16" class="me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
