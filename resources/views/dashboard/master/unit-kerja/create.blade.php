@extends('layouts.dashboard')

@section('title', 'Tambah Unit Kerja')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12">
                <h3 class="page-title text-dark font-weight-medium mb-1">Tambah Unit Kerja</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('unit-kerjas.index') }}">Unit Kerja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('unit-kerjas.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-12 col-md-8">
                        <label for="nama_unit" class="form-label">Nama Unit</label>
                        <input type="text" id="nama_unit" name="nama_unit"
                            class="form-control @error('nama_unit') is-invalid @enderror" value="{{ old('nama_unit') }}"
                            placeholder="Contoh: P2STPIBN" required>
                        @error('nama_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <a href="{{ route('unit-kerjas.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
