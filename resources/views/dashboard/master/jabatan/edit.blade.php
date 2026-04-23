@extends('layouts.dashboard')

@section('title', 'Edit Jabatan')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12">
                <h3 class="page-title text-dark font-weight-medium mb-1">Edit Jabatan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jabatans.index') }}">Jabatan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('jabatans.update', $jabatan) }}" method="POST" class="row g-3" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="col-12 col-md-8">
                        <label for="nama_jabatan" class="form-label">Nama Jabatan</label>
                        <input type="text" id="nama_jabatan" name="nama_jabatan"
                            class="form-control @error('nama_jabatan') is-invalid @enderror"
                            value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" placeholder="Contoh: Pengawas Radiasi"
                            required>
                        @error('nama_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="jenjang" class="form-label">Jenjang</label>
                        <select id="jenjang" name="jenjang" class="form-select @error('jenjang') is-invalid @enderror"
                            required>
                            <option value="" disabled>Pilih jenjang</option>
                            @foreach ($jenjangOptions as $jenjang)
                                <option value="{{ $jenjang }}" @selected(old('jenjang', $jabatan->jenjang) === $jenjang)>{{ $jenjang }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="koefisien_tahunan" class="form-label">Koefisien Tahunan</label>
                        <input type="number" step="0.01" min="0" id="koefisien_tahunan" name="koefisien_tahunan"
                            class="form-control @error('koefisien_tahunan') is-invalid @enderror"
                            value="{{ old('koefisien_tahunan', $jabatan->koefisien_tahunan) }}" required>
                        @error('koefisien_tahunan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="target_ak_kenaikan_pangkat" class="form-label">Target AK Kenaikan Pangkat</label>
                        <input type="number" step="1" min="0" id="target_ak_kenaikan_pangkat"
                            name="target_ak_kenaikan_pangkat"
                            class="form-control @error('target_ak_kenaikan_pangkat') is-invalid @enderror"
                            value="{{ old('target_ak_kenaikan_pangkat', $jabatan->target_ak_kenaikan_pangkat) }}" required>
                        @error('target_ak_kenaikan_pangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="target_ak_kenaikan_jenjang" class="form-label">Target AK Kenaikan Jenjang</label>
                        <input type="number" step="1" min="0" id="target_ak_kenaikan_jenjang"
                            name="target_ak_kenaikan_jenjang"
                            class="form-control @error('target_ak_kenaikan_jenjang') is-invalid @enderror"
                            value="{{ old('target_ak_kenaikan_jenjang', $jabatan->target_ak_kenaikan_jenjang) }}" required>
                        @error('target_ak_kenaikan_jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <a href="{{ route('jabatans.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
