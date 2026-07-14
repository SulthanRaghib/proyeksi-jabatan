@extends('layouts.dashboard')

@section('title', 'Riwayat PAK')

@section('content')
    <x-page-header title="Riwayat PAK" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Riwayat PAK'],
    ]">
        <x-slot:action>
            <a href="{{ route('riwayat-paks.create') }}" class="btn btn-primary">
                <i data-feather="plus" class="feather-icon me-1"></i>
                Tambah Riwayat PAK
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="container-fluid">
        <x-alert-flash />

        <x-filter-bar :action="route('riwayat-paks.index')" :searchValue="$search" placeholder="Cari nomor PAK, nama pegawai, atau NIP..." />

        @php
            $tableHeaders = [
                ['label' => 'No', 'attrs' => 'width="70"'],
                'Pegawai',
                'Nomor PAK',
                'Tanggal PAK',
                ['label' => 'Predikat', 'attrs' => 'class="text-center"'],
                ['label' => 'AK Tambahan', 'attrs' => 'class="text-end"'],
                ['label' => 'AK Total', 'attrs' => 'class="text-end"'],
                ['label' => 'Status', 'attrs' => 'class="text-center"'],
                ['label' => 'Aksi', 'attrs' => 'width="220" class="text-end"']
            ];
        @endphp

        <x-data-table 
            :headers="$tableHeaders"
            :paginator="$riwayatPaks"
            :isEmpty="$riwayatPaks->isEmpty()"
            emptyIcon="file-text"
            emptyTitle="Belum ada riwayat PAK"
            emptyDescription="Tambahkan riwayat PAK untuk memulai.">
            @foreach ($riwayatPaks as $riwayatPak)
                @php
                    $akTotal = (float) $riwayatPak->ak_total;
                    $akTambahan = (float) $riwayatPak->ak_tambahan;
                    $isLatest = $riwayatPak->is_computed_latest ?? false;
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($riwayatPaks->currentPage() - 1) * $riwayatPaks->perPage() }}</td>
                    <td>
                        <div>{{ $riwayatPak->pegawai->nama_lengkap }}</div>
                        <small class="text-muted">{{ $riwayatPak->pegawai->nip }}</small>
                    </td>
                    <td>{{ $riwayatPak->no_pak }}</td>
                    <td>{{ $riwayatPak->tanggal_pak?->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if ($riwayatPak->predikat_kinerja)
                            <span class="badge border {{ $riwayatPak->predikat_badge_class }} px-2 py-1">
                                {{ $riwayatPak->predikat_label }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($akTambahan > 0)
                            <span class="text-success fw-medium">+{{ number_format($akTambahan, 3, ',', '.') }}</span>
                        @else
                            <span class="text-muted">{{ number_format($akTambahan, 3, ',', '.') }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <strong>{{ number_format($akTotal, 3, ',', '.') }}</strong>
                    </td>
                    <td class="text-center">
                        <div class="d-flex flex-column gap-1 align-items-center justify-content-center">
                            @if ($isLatest)
                                <span class="badge bg-success">Terbaru</span>
                            @else
                                <span class="badge bg-secondary">Riwayat</span>
                            @endif
                            
                            @if (!$riwayatPak->is_konversi_baru)
                                <span class="badge" style="background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;" title="Angka Kredit Modal Awal / Baseline Konvensional">Baseline / Awal</span>
                            @else
                                <span class="badge" style="background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;" title="PAK Konversi Baru Permenpan-RB 1/2023">Konversi</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            <x-action-button type="edit" href="{{ route('riwayat-paks.edit', $riwayatPak) }}" />
                            <x-action-button type="delete_modal" 
                                action="{{ route('riwayat-paks.destroy', $riwayatPak) }}" 
                                message="Yakin ingin menghapus riwayat PAK ini?" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </div>
@endsection
