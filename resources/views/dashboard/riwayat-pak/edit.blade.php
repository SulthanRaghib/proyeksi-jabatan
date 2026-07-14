@extends('layouts.dashboard')

@section('title', 'Edit Riwayat PAK')

@section('content')
    <x-page-header title="Edit Riwayat PAK" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Riwayat PAK', 'url' => route('riwayat-paks.index')],
        ['label' => 'Edit'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
            <div class="card-body">
                @include('dashboard.riwayat-pak._form', [
                    'action' => route('riwayat-paks.update', $riwayatPak),
                    'method' => 'PUT',
                    'submitLabel' => 'Perbarui',
                    'riwayatPak' => $riwayatPak,
                ])
            </div>
        </div>
    </div>
@endsection
