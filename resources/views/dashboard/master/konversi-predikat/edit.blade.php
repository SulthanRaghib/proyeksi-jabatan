@extends('layouts.dashboard')

@section('title', 'Edit Konversi Predikat Kinerja')

@section('content')
    <x-page-header title="Edit Konversi Predikat" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Konversi Predikat', 'url' => route('konversi-predikats.index')],
        ['label' => 'Edit'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
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
