@extends('layouts.dashboard')

@section('title', 'Edit Konversi Predikat Kinerja')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-dark font-weight-medium mb-1">Edit Konversi Predikat</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('konversi-predikats.index') }}" class="text-muted">Konversi Predikat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="card-title mb-4">Edit Konversi Predikat Kinerja</h4>
                <div class="alert alert-light border mb-4">
                    <strong>{{ $konversi->jabatan->nama_jabatan }}</strong> — {{ $konversi->jabatan->jenjang }}
                    &bull; Predikat: <strong>{{ $konversi->predikat_label }}</strong>
                </div>

                @include('dashboard.master.konversi-predikat._form', [
                    'action' => route('konversi-predikats.update', $konversi),
                    'method' => 'PUT',
                    'submitLabel' => 'Simpan Perubahan',
                ])
            </div>
        </div>
    </div>
@endsection
