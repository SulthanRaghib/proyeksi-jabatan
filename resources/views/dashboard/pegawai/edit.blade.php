@extends('layouts.dashboard')

@section('title', 'Edit Pegawai')

@section('content')
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Edit Data Pegawai</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pegawais.index') }}">Pegawai</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pegawais.update', $pegawai) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" id="nip" name="nip"
                                class="form-control @error('nip') is-invalid @enderror"
                                value="{{ old('nip', $pegawai->nip) }}" required>
                            @error('nip')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap
                                <span class="text-danger">*</span></label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap"
                                class="form-control @error('nama_lengkap') is-invalid @enderror"
                                value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}" required>
                            @error('nama_lengkap')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <label for="user_id" class="form-label">Akun Pengguna (Opsional)</label>
                            <select id="user_id" name="user_id"
                                class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">-- Tidak ada akun --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id', $pegawai->user_id) == $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="unit_kerja_id" class="form-label">Unit Kerja
                                <span class="text-danger">*</span></label>
                            <select id="unit_kerja_id" name="unit_kerja_id"
                                class="form-select @error('unit_kerja_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach ($unitKerjas as $unit)
                                    <option value="{{ $unit->id }}" @selected(old('unit_kerja_id', $pegawai->unit_kerja_id) == $unit->id)>
                                        {{ $unit->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_kerja_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <label for="jabatan_id" class="form-label">Jabatan
                                <span class="text-danger">*</span></label>
                            <select id="jabatan_id" name="jabatan_id"
                                class="form-select @error('jabatan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach ($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}" @selected(old('jabatan_id', $pegawai->jabatan_id) == $jabatan->id)>
                                        {{ $jabatan->nama_jabatan }} ({{ $jabatan->jenjang }})
                                    </option>
                                @endforeach
                            </select>
                            @error('jabatan_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="golongan_id" class="form-label">Golongan
                                <span class="text-danger">*</span></label>
                            <select id="golongan_id" name="golongan_id"
                                class="form-select @error('golongan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Golongan --</option>
                                @foreach ($golongans as $golongan)
                                    <option value="{{ $golongan->id }}" @selected(old('golongan_id', $pegawai->golongan_id) == $golongan->id)>
                                        {{ $golongan->nama_golongan }} ({{ $golongan->pangkat }})
                                    </option>
                                @endforeach
                            </select>
                            @error('golongan_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <label for="tmt_jabatan" class="form-label">Tanggal TMT Jabatan
                                <span class="text-danger">*</span></label>
                            <input type="date" id="tmt_jabatan" name="tmt_jabatan"
                                class="form-control @error('tmt_jabatan') is-invalid @enderror"
                                value="{{ old('tmt_jabatan', $pegawai->tmt_jabatan?->format('Y-m-d')) }}" required>
                            @error('tmt_jabatan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="tmt_golongan" class="form-label">Tanggal TMT Golongan
                                <span class="text-danger">*</span></label>
                            <input type="date" id="tmt_golongan" name="tmt_golongan"
                                class="form-control @error('tmt_golongan') is-invalid @enderror"
                                value="{{ old('tmt_golongan', $pegawai->tmt_golongan?->format('Y-m-d')) }}" required>
                            @error('tmt_golongan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <div class="form-check">
                                <input type="checkbox" id="status_ukom" name="status_ukom" value="1"
                                    class="form-check-input @error('status_ukom') is-invalid @enderror"
                                    @checked(old('status_ukom', $pegawai->status_ukom))>
                                <label class="form-check-label" for="status_ukom">
                                    Status UKOM (Sudah Lulus Tes Kompetensi)
                                </label>
                                @error('status_ukom')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('pegawais.index') }}" class="btn btn-outline-secondary me-2">
                                <i data-feather="x" class="feather-icon me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="feather-icon me-1"></i>
                                Perbarui
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
