@extends('layouts.dashboard')

@section('title', 'Tambah Pegawai')

@section('content')
    <x-page-header title="Tambah Data Pegawai" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Pegawai', 'url' => route('pegawais.index')],
        ['label' => 'Tambah'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <div class="card">
            <div class="card-body">

                <form action="{{ route('pegawais.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" id="nip" name="nip"
                                class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}"
                                required>
                            @error('nip')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap
                                <span class="text-danger">*</span></label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap"
                                class="form-control @error('nama_lengkap') is-invalid @enderror"
                                value="{{ old('nama_lengkap') }}" required>
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
                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
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
                                    <option value="{{ $unit->id }}" @selected(old('unit_kerja_id') == $unit->id)>
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
                                    <option value="{{ $jabatan->id }}" @selected(old('jabatan_id') == $jabatan->id)>
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
                                    <option value="{{ $golongan->id }}" @selected(old('golongan_id') == $golongan->id)>
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
                                value="{{ old('tmt_jabatan') }}" required>
                            @error('tmt_jabatan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="tmt_golongan" class="form-label">Tanggal TMT Golongan
                                <span class="text-danger">*</span></label>
                            <input type="date" id="tmt_golongan" name="tmt_golongan"
                                class="form-control @error('tmt_golongan') is-invalid @enderror"
                                value="{{ old('tmt_golongan') }}" required>
                            @error('tmt_golongan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" id="status_ukom" name="status_ukom" value="1"
                                    class="form-check-input @error('status_ukom') is-invalid @enderror"
                                    @checked(old('status_ukom'))>
                                <label class="form-check-label" for="status_ukom">
                                    Status UKOM (Sudah Lulus Tes Kompetensi)
                                </label>
                                @error('status_ukom')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" id="sedang_hukuman_disiplin" name="sedang_hukuman_disiplin" value="1"
                                    class="form-check-input @error('sedang_hukuman_disiplin') is-invalid @enderror"
                                    @checked(old('sedang_hukuman_disiplin'))>
                                <label class="form-check-label text-danger fw-medium" for="sedang_hukuman_disiplin">
                                    Sedang Menjalani Hukuman Disiplin (Blokir Usulan)
                                </label>
                                @error('sedang_hukuman_disiplin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <a href="{{ route('pegawais.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const golonganSelect = document.getElementById('golongan_id');
        const jabatanSelect = document.getElementById('jabatan_id');
        
        function updateJenjang() {
            if (!golonganSelect.value) return;
            
            const selectedOption = golonganSelect.options[golonganSelect.selectedIndex];
            const text = selectedOption.text;
            let expectedJenjang = '';
            
            if (text.includes('III/a') || text.includes('III/b')) {
                expectedJenjang = 'Pertama';
            } else if (text.includes('III/c') || text.includes('III/d')) {
                expectedJenjang = 'Muda';
            } else if (text.includes('IV/a') || text.includes('IV/b') || text.includes('IV/c')) {
                expectedJenjang = 'Madya';
            } else if (text.includes('IV/d') || text.includes('IV/e')) {
                expectedJenjang = 'Utama';
            } else if (text.includes('II/a')) {
                expectedJenjang = 'Pemula';
            } else if (text.includes('II/b') || text.includes('II/c') || text.includes('II/d')) {
                expectedJenjang = 'Terampil';
            }
            
            if (expectedJenjang) {
                // Find matching option in jabatan
                let found = false;
                for (let i = 0; i < jabatanSelect.options.length; i++) {
                    const opt = jabatanSelect.options[i];
                    if (opt.text.includes('(' + expectedJenjang + ')')) {
                        jabatanSelect.value = opt.value;
                        found = true;
                        break; // If found one, we just select it
                    }
                }
            }
        }
        
        golonganSelect.addEventListener('change', updateJenjang);
        
        // Run once on load
        updateJenjang();
    });
</script>
@endpush
