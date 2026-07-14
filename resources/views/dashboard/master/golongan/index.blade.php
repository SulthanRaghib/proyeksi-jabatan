@extends('layouts.dashboard')

@section('title', 'Data Master - Golongan')

@section('content')
    <x-page-header title="Data Master Golongan" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Golongan'],
    ]">
        <x-slot:action>
            <a href="{{ route('golongans.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="feather-icon me-1"></i>
                Tambah Golongan
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="container-fluid">
        <x-alert-flash />

        <x-filter-bar :action="route('golongans.index')" :searchValue="$search" placeholder="Cari nama golongan atau pangkat..." />

        @php
            $tableHeaders = [
                ['label' => 'No', 'attrs' => 'width="80"'],
                'Nama Golongan',
                'Pangkat',
                ['label' => 'Aksi', 'attrs' => 'width="220" class="text-end"']
            ];
        @endphp

        <x-data-table 
            :headers="$tableHeaders"
            :paginator="$golongans"
            :isEmpty="$golongans->isEmpty()"
            emptyIcon="layers"
            emptyTitle="Belum ada data golongan"
            emptyDescription="Tambahkan data golongan untuk memulai.">
            @foreach ($golongans as $golongan)
                <tr>
                    <td>{{ $loop->iteration + ($golongans->currentPage() - 1) * $golongans->perPage() }}</td>
                    <td>{{ $golongan->nama_golongan }}</td>
                    <td>{{ $golongan->pangkat }}</td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            <x-action-button type="edit" :href="route('golongans.edit', $golongan)" />
                            <x-action-button type="delete_modal" 
                                action="{{ route('golongans.destroy', $golongan) }}" 
                                message="Yakin ingin menghapus golongan {{ $golongan->nama_golongan }}?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </div>
@endsection
