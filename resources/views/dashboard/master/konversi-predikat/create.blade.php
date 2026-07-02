@extends('layouts.dashboard')

@section('title', 'Tambah Konversi Predikat Kinerja')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-dark font-weight-medium mb-1">Tambah Konversi Predikat</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('konversi-predikats.index') }}" class="text-muted">Konversi Predikat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="card-title mb-4">Data Konversi Predikat Kinerja</h4>

                @include('dashboard.master.konversi-predikat._form', [
                    'action' => route('konversi-predikats.store'),
                    'method' => 'POST',
                    'submitLabel' => 'Simpan',
                ])
            </div>
        </div>
    </div>
@endsection
