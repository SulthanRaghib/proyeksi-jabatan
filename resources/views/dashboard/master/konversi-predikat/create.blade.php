@extends('layouts.dashboard')

@section('title', 'Tambah Konversi Predikat Kinerja')

@section('content')
    <x-page-header title="Tambah Konversi Predikat" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Konversi Predikat', 'url' => route('konversi-predikats.index')],
        ['label' => 'Tambah'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
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
