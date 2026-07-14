@extends('layouts.dashboard')

@section('title', 'Data Master - Jabatan')

@section('content')
    <x-page-header title="Data Master Jabatan" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Jabatan'],
    ]">
        <x-slot:action>
            <a href="{{ route('jabatans.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="feather-icon me-1"></i>
                Tambah Jabatan
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="container-fluid">
        <x-alert-flash />

        <x-filter-bar :action="route('jabatans.index')" :searchValue="$search" placeholder="Cari nama jabatan atau jenjang..." />

        @php
            $tableHeaders = [
                ['label' => 'No', 'attrs' => 'width="70"'],
                'Nama Jabatan',
                'Jenjang',
                'Koefisien Tahunan',
                'Target AK Pangkat',
                'Target AK Jenjang',
                ['label' => 'Aksi', 'attrs' => 'width="220" class="text-end"']
            ];
        @endphp

        <x-data-table 
            :headers="$tableHeaders"
            :paginator="$jabatans"
            :isEmpty="$jabatans->isEmpty()"
            emptyIcon="briefcase"
            emptyTitle="Belum ada data jabatan"
            emptyDescription="Tambahkan data jabatan untuk memulai.">
            @foreach ($jabatans as $jabatan)
                <tr>
                    <td>{{ $loop->iteration + ($jabatans->currentPage() - 1) * $jabatans->perPage() }}</td>
                    <td>{{ $jabatan->nama_jabatan }}</td>
                    <td>{{ $jabatan->jenjang }}</td>
                    <td><span class="badge bg-light text-dark border">{{ number_format((float) $jabatan->koefisien_tahunan, 2, ',', '.') }}</span></td>
                    <td><span class="badge bg-success bg-opacity-25 text-dark border-0 fw-bold">{{ number_format((int) $jabatan->target_ak_kenaikan_pangkat, 0, ',', '.') }}</span></td>
                    <td>
                        @if($jabatan->target_ak_kenaikan_jenjang)
                            <span class="badge bg-warning bg-opacity-25 text-dark border-0 fw-bold">{{ number_format((int) $jabatan->target_ak_kenaikan_jenjang, 0, ',', '.') }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            <x-action-button type="edit" :href="route('jabatans.edit', $jabatan)" />
                            <x-action-button type="delete_modal" 
                                action="{{ route('jabatans.destroy', $jabatan) }}" 
                                message="Yakin ingin menghapus jabatan {{ $jabatan->nama_jabatan }}?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </div>
@endsection
