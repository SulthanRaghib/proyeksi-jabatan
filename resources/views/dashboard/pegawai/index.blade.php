@extends('layouts.dashboard')

@section('title', 'Data Pegawai')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Data Pegawai</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                <a href="{{ route('pegawais.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="feather-icon me-1"></i>
                    Tambah Pegawai
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('pegawais.index') }}" class="row g-2 mb-4">
                    <div class="col-12 col-md-10">
                        <input type="text" name="q" value="{{ $search }}" class="form-control"
                            placeholder="Cari nama atau NIP...">
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-outline-primary">Cari</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="70">No</th>
                                <th>NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Unit Kerja</th>
                                <th>Jabatan</th>
                                <th>Golongan</th>
                                <th>TMT Jabatan</th>
                                <th width="220" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pegawais as $pegawai)
                                <tr>
                                    <td>{{ $loop->iteration + ($pegawais->currentPage() - 1) * $pegawais->perPage() }}</td>
                                    <td>{{ $pegawai->nip }}</td>
                                    <td>{{ $pegawai->nama_lengkap }}</td>
                                    <td>{{ $pegawai->unitKerja->nama_unit }}</td>
                                    <td>{{ $pegawai->jabatan->nama_jabatan }} ({{ $pegawai->jabatan->jenjang }})</td>
                                    <td>{{ $pegawai->golongan->nama_golongan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pegawai->tmt_jabatan)->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('pegawais.edit', $pegawai) }}"
                                            class="btn btn-sm btn-outline-warning">Edit</a>
                                        <form action="{{ route('pegawais.destroy', $pegawai) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada data pegawai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $pegawais->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
