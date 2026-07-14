@extends('layouts.dashboard')

@section('title', 'Edit Golongan')

@section('content')
    <x-page-header title="Edit Golongan" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Golongan', 'url' => route('golongans.index')],
        ['label' => 'Edit'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
            <div class="card-body">
                <form action="{{ route('golongans.update', $golongan) }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-12 col-md-6">
                        <label for="nama_golongan" class="form-label">Nama Golongan</label>
                        <input type="text" id="nama_golongan" name="nama_golongan"
                            class="form-control @error('nama_golongan') is-invalid @enderror"
                            value="{{ old('nama_golongan', $golongan->nama_golongan) }}" placeholder="Contoh: III/a">
                        @error('nama_golongan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-8">
                        <label for="pangkat" class="form-label">Pangkat</label>
                        <input type="text" id="pangkat" name="pangkat"
                            class="form-control @error('pangkat') is-invalid @enderror"
                            value="{{ old('pangkat', $golongan->pangkat) }}" placeholder="Contoh: Penata Muda">
                        @error('pangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <a href="{{ route('golongans.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
