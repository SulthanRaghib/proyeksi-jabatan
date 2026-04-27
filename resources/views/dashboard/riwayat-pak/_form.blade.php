@php
    $currentRiwayatPak = $riwayatPak ?? null;
@endphp

<form action="{{ $action }}" method="POST" class="row g-3" novalidate>
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="col-12 col-md-6">
        <label for="pegawai_id" class="form-label">Pegawai</label>
        <select id="pegawai_id" name="pegawai_id" class="form-select @error('pegawai_id') is-invalid @enderror">
            <option value="" disabled {{ old('pegawai_id', $currentRiwayatPak?->pegawai_id) ? '' : 'selected' }}>
                Pilih pegawai
            </option>
            @foreach ($pegawais as $pegawai)
                <option value="{{ $pegawai->id }}" @selected((string) old('pegawai_id', $currentRiwayatPak?->pegawai_id) === (string) $pegawai->id)>
                    {{ $pegawai->nama_lengkap }} - {{ $pegawai->nip }}
                </option>
            @endforeach
        </select>
        @error('pegawai_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="no_pak" class="form-label">Nomor PAK</label>
        <input type="text" id="no_pak" name="no_pak" class="form-control @error('no_pak') is-invalid @enderror"
            value="{{ old('no_pak', $currentRiwayatPak?->no_pak) }}" placeholder="Contoh: 90/KEP/4028/SK/PAK/2025">
        @error('no_pak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="tanggal_pak" class="form-label">Tanggal PAK</label>
        <input type="date" id="tanggal_pak" name="tanggal_pak"
            class="form-control @error('tanggal_pak') is-invalid @enderror"
            value="{{ old('tanggal_pak', $currentRiwayatPak?->tanggal_pak?->format('Y-m-d')) }}">
        @error('tanggal_pak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="ak_total" class="form-label">AK Total</label>
        <input type="number" step="0.001" min="0" id="ak_total" name="ak_total"
            class="form-control @error('ak_total') is-invalid @enderror"
            value="{{ old('ak_total', $currentRiwayatPak?->ak_total) }}" placeholder="Contoh: 245.125">
        @error('ak_total')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="form-check">
            <input type="hidden" name="is_latest" value="0">
            <input class="form-check-input @error('is_latest') is-invalid @enderror" type="checkbox" id="is_latest"
                name="is_latest" value="1" @checked((bool) old('is_latest', $currentRiwayatPak?->is_latest))>
            <label class="form-check-label" for="is_latest">
                Tandai sebagai data AK terbaru
            </label>
            @error('is_latest')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <a href="{{ route('riwayat-paks.index') }}" class="btn btn-light me-2">Batal</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>
