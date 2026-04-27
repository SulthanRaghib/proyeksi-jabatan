@extends('layouts.dashboard')

@section('title', 'Edit Riwayat PAK')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12">
                <h3 class="page-title text-dark font-weight-medium mb-1">Edit Riwayat PAK</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('riwayat-paks.index') }}">Riwayat PAK</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
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
