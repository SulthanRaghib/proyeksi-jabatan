@extends('layouts.dashboard')

@section('title', 'Data Master - Unit Kerja')

@section('content')
    <x-page-header title="Data Master Unit Kerja" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Unit Kerja'],
    ]">
        <x-slot:action>
            <a href="{{ route('unit-kerjas.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="feather-icon me-1"></i>
                Tambah Unit Kerja
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="container-fluid">
        <x-alert-flash />

        <x-filter-bar :action="route('unit-kerjas.index')" :searchValue="$search" placeholder="Cari nama unit kerja..." />

        @php
            $tableHeaders = [
                ['label' => 'No', 'attrs' => 'width="80"'],
                'Nama Unit',
                ['label' => 'Aksi', 'attrs' => 'width="220" class="text-end"']
            ];
        @endphp

        <x-data-table 
            :headers="$tableHeaders"
            :paginator="$unitKerjas"
            :isEmpty="$unitKerjas->isEmpty()"
            emptyIcon="grid"
            emptyTitle="Belum ada data unit kerja"
            emptyDescription="Tambahkan data unit kerja untuk memulai.">
            @foreach ($unitKerjas as $unitKerja)
                <tr>
                    <td>{{ $loop->iteration + ($unitKerjas->currentPage() - 1) * $unitKerjas->perPage() }}</td>
                    <td>{{ $unitKerja->nama_unit }}</td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            <x-action-button type="edit" :href="route('unit-kerjas.edit', $unitKerja)" />
                            <x-action-button type="delete_modal" 
                                action="{{ route('unit-kerjas.destroy', $unitKerja) }}" 
                                message="Yakin ingin menghapus unit kerja {{ $unitKerja->nama_unit }}?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </div>
@endsection
