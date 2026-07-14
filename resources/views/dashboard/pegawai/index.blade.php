@extends('layouts.dashboard')

@section('title', 'Data Pegawai')

@section('content')
    <x-page-header title="Data Pegawai" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Pegawai'],
    ]">
        <x-slot:action>
            <a href="{{ route('pegawais.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="feather-icon me-1"></i>
                Tambah Pegawai
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="container-fluid">
        <x-alert-flash />

        <x-filter-bar :action="route('pegawais.index')" :searchValue="$search" placeholder="Cari nama atau NIP..." />

        @php
            $tableHeaders = [
                'No',
                'NIP',
                'Nama Lengkap',
                'Unit Kerja',
                'Jabatan',
                'Golongan',
                'TMT Jabatan',
                ['label' => 'Aksi', 'attrs' => 'width="220" class="text-end"']
            ];
        @endphp

        <x-data-table 
            :headers="$tableHeaders"
            :paginator="$pegawais"
            :isEmpty="$pegawais->isEmpty()"
            emptyIcon="users"
            emptyTitle="Belum ada data pegawai"
            emptyDescription="Tambahkan data pegawai untuk memulai.">
            @foreach ($pegawais as $pegawai)
                <tr>
                    <td>{{ $loop->iteration + ($pegawais->currentPage() - 1) * $pegawais->perPage() }}</td>
                    <td>{{ $pegawai->nip }}</td>
                    <td>{{ $pegawai->nama_lengkap }}</td>
                    <td>{{ $pegawai->unitKerja->nama_unit }}</td>
                    <td>
                        <div class="fw-medium text-dark">{{ $pegawai->jabatan->nama_jabatan }}</div>
                        <div class="small text-muted">{{ $pegawai->jabatan->jenjang }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $pegawai->golongan->nama_golongan }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($pegawai->tmt_jabatan)->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            <x-action-button type="edit" :href="route('pegawais.edit', $pegawai)" />
                            <x-action-button type="delete_modal" 
                                action="{{ route('pegawais.destroy', $pegawai) }}" 
                                message="Yakin ingin menghapus pegawai {{ $pegawai->nama_lengkap }}?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </div>
@endsection
