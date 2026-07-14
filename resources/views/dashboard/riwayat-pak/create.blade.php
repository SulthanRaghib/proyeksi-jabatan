@extends('layouts.dashboard')

@section('title', 'Tambah Riwayat PAK')

@section('content')
    <x-page-header title="Tambah Riwayat PAK" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Riwayat PAK', 'url' => route('riwayat-paks.index')],
        ['label' => 'Tambah'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
            <div class="card-body">
                @include('dashboard.riwayat-pak._form', [
                    'action' => route('riwayat-paks.store'),
                    'method' => 'POST',
                    'submitLabel' => 'Simpan',
                    'riwayatPak' => null,
                ])
            </div>
        </div>
    </div>
@endsection
