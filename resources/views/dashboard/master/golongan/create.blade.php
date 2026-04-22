@extends('layouts.dashboard')

@section('title', 'Tambah Golongan')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12">
                <h3 class="page-title text-dark font-weight-medium mb-1">Tambah Golongan</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('golongans.index') }}">Golongan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('golongans.store') }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-12 col-md-6">
                        <label for="nama_golongan" class="form-label">Nama Golongan</label>
                        <input type="text" id="nama_golongan" name="nama_golongan"
                            class="form-control @error('nama_golongan') is-invalid @enderror"
                            value="{{ old('nama_golongan') }}" placeholder="Contoh: III/a" required>
                        @error('nama_golongan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-8">
                        <label for="pangkat" class="form-label">Pangkat</label>
                        <input type="text" id="pangkat" name="pangkat"
                            class="form-control @error('pangkat') is-invalid @enderror" value="{{ old('pangkat') }}"
                            placeholder="Contoh: Penata Muda" required>
                        @error('pangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <a href="{{ route('golongans.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
