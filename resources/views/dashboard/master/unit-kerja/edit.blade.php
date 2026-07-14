@extends('layouts.dashboard')

@section('title', 'Edit Unit Kerja')

@section('content')
    <x-page-header title="Edit Unit Kerja" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Unit Kerja', 'url' => route('unit-kerjas.index')],
        ['label' => 'Edit'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
            <div class="card-body">
                <form action="{{ route('unit-kerjas.update', $unitKerja) }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-12 col-md-8">
                        <label for="nama_unit" class="form-label">Nama Unit</label>
                        <input type="text" id="nama_unit" name="nama_unit"
                            class="form-control @error('nama_unit') is-invalid @enderror"
                            value="{{ old('nama_unit', $unitKerja->nama_unit) }}" placeholder="Contoh: P2STPIBN">
                        @error('nama_unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <a href="{{ route('unit-kerjas.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
