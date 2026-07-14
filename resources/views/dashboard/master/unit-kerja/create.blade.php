@extends('layouts.dashboard')

@section('title', 'Tambah Unit Kerja')

@section('content')
    <x-page-header title="Tambah Unit Kerja" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Unit Kerja', 'url' => route('unit-kerjas.index')],
        ['label' => 'Tambah'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
            <div class="card-body">
                <form action="{{ route('unit-kerjas.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-12 col-md-8">
                        <label for="nama_unit" class="form-label">Nama Unit</label>
                        <input type="text" id="nama_unit" name="nama_unit"
                            class="form-control @error('nama_unit') is-invalid @enderror" value="{{ old('nama_unit') }}"
                            placeholder="Contoh: P2STPIBN">
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
