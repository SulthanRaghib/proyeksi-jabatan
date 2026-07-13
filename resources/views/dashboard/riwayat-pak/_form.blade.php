@php
    $currentRiwayatPak = $riwayatPak ?? null;
    $isEditMode = (bool) $currentRiwayatPak;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/riwayat-pak-form.css') }}">
@endpush

<form id="riwayatPakForm" action="{{ $action }}" method="POST" class="row g-3" novalidate
      data-current-pak-id="{{ $currentRiwayatPak?->id ?? '' }}"
      data-generate-no-pak-url="{{ route('api.generate-no-pak') }}">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    @if(request()->has('redirect_to'))
        <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
    @endif

    {{-- Pegawai Selection --}}
    <div class="col-12 col-md-6">
        <label for="pegawai_id" class="form-label">Pegawai</label>
        <select id="pegawai_id" name="pegawai_id" class="form-select @error('pegawai_id') is-invalid @enderror"
            {{ $currentRiwayatPak ? 'disabled' : '' }}>
            <option value="" disabled {{ old('pegawai_id', $currentRiwayatPak?->pegawai_id ?? request('pegawai_id')) ? '' : 'selected' }}>
                Pilih pegawai
            </option>
            @foreach ($pegawais as $pegawai)
                @php
                    $latestPak = $pegawai->riwayatPaks->first();
                    // For edit mode, exclude current record to get previous AK
                    if ($currentRiwayatPak && $pegawai->id === $currentRiwayatPak->pegawai_id) {
                        $latestPak = $pegawai->riwayatPaks->where('id', '!=', $currentRiwayatPak->id)->first();
                    }
                    $pegawaiCurrentAk = $latestPak ? (float) $latestPak->ak_total : 0;
                    $targetAk = (float) ($pegawai->jabatan->target_ak_kenaikan_pangkat ?? 0);
                    $progress = $targetAk > 0 ? min(100, ($pegawaiCurrentAk / $targetAk) * 100) : 0;

                    // Konversi predikat data for this pegawai's jabatan
                    $konversiData = [];
                    if ($pegawai->jabatan && $pegawai->jabatan->konversiPredikat) {
                        foreach ($pegawai->jabatan->konversiPredikat as $k) {
                            $konversiData[$k->predikat] = (float) $k->nilai_ak;
                        }
                    }

                    // Recent history (last 5 records, for edit mode exclude current)
                    $history = $pegawai->riwayatPaks;
                    if ($currentRiwayatPak && $pegawai->id === $currentRiwayatPak->pegawai_id) {
                        $history = $history->where('id', '!=', $currentRiwayatPak->id);
                    }
                    $historyData = $history->take(5)->map(function ($pak) {
                        return [
                            'tanggal' => $pak->tanggal_pak->format('d/m/Y'),
                            'tahun' => $pak->periode_penilaian_label,
                            'ak_total' => number_format((float) $pak->ak_total, 3, ',', '.'),
                            'ak_tambahan' => number_format((float) $pak->ak_tambahan, 3, ',', '.'),
                            'no_pak' => $pak->no_pak,
                        ];
                    })->values()->toArray();
                @endphp
                <option value="{{ $pegawai->id }}" @selected((string) old('pegawai_id', $currentRiwayatPak?->pegawai_id ?? request('pegawai_id')) === (string) $pegawai->id)
                    data-pegawai-name="{{ $pegawai->nama_lengkap }}"
                    data-current-ak="{{ $pegawaiCurrentAk }}"
                    data-target-ak="{{ $targetAk }}"
                    data-progress="{{ round($progress, 2) }}"
                    data-jabatan="{{ $pegawai->jabatan->nama_jabatan ?? '-' }}"
                    data-jabatan-id="{{ $pegawai->jabatan->id ?? '' }}"
                    data-jenjang="{{ $pegawai->jabatan->jenjang ?? '-' }}"
                    data-golongan="{{ $pegawai->golongan->nama_golongan ?? '-' }}"
                    data-koefisien="{{ $pegawai->jabatan->koefisien_tahunan ?? 0 }}"
                    data-latest-date="{{ $latestPak?->tanggal_pak?->format('d/m/Y') ?? '-' }}"
                    data-latest-nopak="{{ $latestPak?->no_pak ?? '-' }}"
                    data-konversi='@json($konversiData)'
                    data-history='@json($historyData)'>
                    {{ $pegawai->nama_lengkap }} - {{ $pegawai->nip }}
                </option>
            @endforeach
        </select>
        @if ($currentRiwayatPak)
            <input type="hidden" name="pegawai_id" value="{{ $currentRiwayatPak->pegawai_id }}">
        @endif
        @error('pegawai_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="no_pak" class="form-label">Nomor PAK</label>
        <div class="input-group has-validation">
            <input type="text" id="no_pak" name="no_pak" class="form-control custom-input @error('no_pak') is-invalid @enderror"
                value="{{ old('no_pak', $currentRiwayatPak?->no_pak) }}" placeholder="Contoh: 90/KEP/4028/SK/PAK/2025">
            <button type="button" id="btn-generate-pak" class="btn btn-outline-primary d-flex align-items-center gap-1" title="Generate Nomor PAK Otomatis" style="border-radius: 0 0.5rem 0.5rem 0;">
                <i data-feather="cpu" width="16" height="16"></i> <span class="d-none d-sm-inline">Generate</span>
            </button>
            @error('no_pak')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text text-muted small"><i data-feather="info" width="12" height="12" class="me-1"></i>Klik tombol Generate untuk membuat nomor PAK otomatis berdasarkan tahun ini.</div>
    </div>

    {{-- AK Status Panel — shown after pegawai selected --}}
    <div class="col-12" id="akStatusPanel" style="display: none;">
        <div class="ak-status-panel">
            {{-- Gradient Header --}}
            <div class="panel-header">
                <i data-feather="trending-up" width="18" height="18"></i>
                <h6>Status Angka Kredit Saat Ini</h6>
            </div>

            <div class="p-3">
                {{-- Stats Cards Row --}}
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="ak-stat-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="stat-icon bg-primary-subtle">
                                    <i data-feather="award" class="text-primary" width="16" height="16"></i>
                                </div>
                                <div class="stat-label">AK Saat Ini</div>
                            </div>
                            <div class="stat-value text-primary" id="displayCurrentAk">0,000</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="ak-stat-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="stat-icon bg-dark bg-opacity-10">
                                    <i data-feather="target" class="text-dark" width="16" height="16"></i>
                                </div>
                                <div class="stat-label">Target AK</div>
                            </div>
                            <div class="stat-value text-dark" id="displayTargetAk">0</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="ak-stat-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="stat-icon bg-danger-subtle">
                                    <i data-feather="alert-circle" class="text-danger" width="16"
                                        height="16"></i>
                                </div>
                                <div class="stat-label">Sisa Kebutuhan</div>
                            </div>
                            <div class="stat-value" id="displayDeficit">0,000</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="ak-stat-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="stat-icon bg-success-subtle">
                                    <i data-feather="file-text" class="text-success" width="16"
                                        height="16"></i>
                                </div>
                                <div class="stat-label">PAK Terakhir</div>
                            </div>
                            <div class="stat-value text-dark" style="font-size: 0.9rem;" id="displayLastPak">-
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="ak-progress-wrapper mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i data-feather="bar-chart" class="text-muted" width="14" height="14"></i>
                            <span class="small text-muted fw-medium">Progres Menuju Target</span>
                        </div>
                        <span class="small fw-bold text-primary" id="displayProgressText">0%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" id="displayProgressBar" style="width: 0%"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                {{-- Info Row --}}
                <div class="ak-info-row mb-3">
                    <div class="info-item">
                        <div class="info-icon bg-primary-subtle">
                            <i data-feather="briefcase" class="text-primary" width="12" height="12"></i>
                        </div>
                        <span class="small"><span class="text-muted">Jabatan:</span> <strong
                                id="displayJabatan">-</strong></span>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-success-subtle">
                            <i data-feather="layers" class="text-success" width="12" height="12"></i>
                        </div>
                        <span class="small"><span class="text-muted">Golongan:</span> <strong
                                id="displayGolongan">-</strong></span>
                    </div>
                    <div class="info-item">
                        <div class="info-icon bg-warning-subtle">
                            <i data-feather="hash" class="text-warning" width="12" height="12"></i>
                        </div>
                        <span class="small"><span class="text-muted">Koefisien/Tahun:</span> <strong
                                id="displayKoefisien">-</strong></span>
                    </div>
                </div>

                {{-- History Table --}}
                <div id="historySection" style="display: none;">
                    <div class="ak-history-section">
                        <div class="history-header">
                            <i data-feather="clock" class="text-muted" width="14" height="14"></i>
                            <span class="small fw-medium text-muted">Riwayat AK Terakhir</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Tahun</th>
                                        <th>No. PAK</th>
                                        <th class="text-end">AK Tambahan</th>
                                        <th class="text-end">AK Total</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kinerja Tahunan Sync (CSR / AJAX) --}}
    <div id="skpSyncContainer" class="col-12 mb-4" style="display: none;" data-old-value="{{ old('kinerja_tahunan_id', $currentRiwayatPak?->kinerjas->first()?->id) }}">
        <div class="alert alert-primary border-primary border-opacity-25 d-flex align-items-sm-center flex-column flex-lg-row gap-3 py-3 px-4 mb-0 rounded-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="bg-primary text-white p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; box-shadow: 0 2px 6px rgba(13, 110, 253, 0.2);">
                    <i data-feather="link" width="18" height="18"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-primary" style="letter-spacing: -0.2px;">Smart Sync SKP</h6>
                    <p class="mb-0 text-primary opacity-75" style="font-size: 0.85rem; line-height: 1.3;">Pilih Riwayat Kinerja untuk mengisi form secara otomatis.</p>
                </div>
            </div>
            
            <div class="flex-shrink-0" style="width: 100%; max-width: 450px;">
                <select id="kinerja_tahunan_id" name="kinerja_tahunan_id" class="form-select border-primary border-opacity-50 shadow-sm fw-medium text-primary bg-white" style="cursor: pointer;">
                    <option value="">— Buat Kinerja Baru (Input Manual) —</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <label for="tanggal_pak" class="form-label">Tanggal PAK</label>
        <input type="date" id="tanggal_pak" name="tanggal_pak"
            class="form-control @error('tanggal_pak') is-invalid @enderror"
            value="{{ old('tanggal_pak', $currentRiwayatPak?->tanggal_pak?->format('Y-m-d')) }}">
        @error('tanggal_pak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="col-12 col-md-4">
        <label for="periode_awal" class="form-label">Periode Awal <span class="text-muted fw-normal">(Opsional)</span></label>
        <input type="date" id="periode_awal" name="periode_awal"
            class="form-control @error('periode_awal') is-invalid @enderror"
            value="{{ old('periode_awal', $currentRiwayatPak?->periode_awal?->format('Y-m-d')) }}">
        @error('periode_awal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label for="periode_akhir" class="form-label">Periode Akhir <span class="text-muted fw-normal">(Opsional)</span></label>
        <input type="date" id="periode_akhir" name="periode_akhir"
            class="form-control @error('periode_akhir') is-invalid @enderror"
            value="{{ old('periode_akhir', $currentRiwayatPak?->periode_akhir?->format('Y-m-d')) }}">
        @error('periode_akhir')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mt-2 mb-3">
        <div class="alert alert-info border-info-subtle d-flex align-items-start gap-2 mb-0 py-2 px-3" style="background-color: #f0f9ff; color: #0369a1; border-radius: 0.5rem;">
            <i data-feather="info" width="18" height="18" class="mt-1 flex-shrink-0" style="color: #0284c7;"></i>
            <div class="small" style="line-height: 1.5;">
                <strong style="color: #075985;">Tips Sinkronisasi Cerdas:</strong> 
                Pastikan Anda mengisi <strong>Periode Akhir</strong> (misal: 31/12/2023) agar sistem dapat melacak dan mengaitkan data dengan Riwayat Kinerja Tahunan secara presisi. Jika dibiarkan kosong, sistem akan mencoba menebak tahun berdasarkan Tanggal PAK.
            </div>
        </div>
    </div>

    {{-- Switch is_konversi_baru --}}
    <div class="col-12 col-md-6 mb-3">
        <div class="form-check form-switch pt-2">
            <input type="hidden" name="is_konversi_baru" value="0">
            <input class="form-check-input" type="checkbox" id="is_konversi_baru" name="is_konversi_baru" value="1" 
                @checked(old('is_konversi_baru', $currentRiwayatPak ? $currentRiwayatPak->is_konversi_baru : true))>
            <label class="form-check-label fw-bold text-dark" for="is_konversi_baru">
                PAK Konversi Baru (Permenpan-RB 1/2023)
            </label>
            <div class="form-text small text-muted">
                Aktifkan jika dokumen PAK ini berdasarkan konversi Predikat Kinerja SKP. Matikan jika ini merupakan PAK konvensional lama / integrasi (saldo awal/baseline).
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 mb-3" id="predikatSpacer">
        {{-- Spacer --}}
    </div>

    {{-- Predikat Kinerja — placed BEFORE AK Tambahan so it drives the auto-fill --}}
    <div class="col-12 col-md-6" id="predikatKinerjaContainer">
        <label for="predikat_kinerja" class="form-label">
            Predikat Kinerja
        </label>
        <select id="predikat_kinerja" name="predikat_kinerja"
            class="form-select form-select-predikat @error('predikat_kinerja') is-invalid @enderror">
            <option value="" @selected(!old('predikat_kinerja', $currentRiwayatPak?->predikat_kinerja))>
                — Pilih Predikat —
            </option>
            @foreach (\App\Models\KonversiPredikatKinerja::PREDIKAT_LABELS as $key => $label)
                <option value="{{ $key }}" @selected(old('predikat_kinerja', $currentRiwayatPak?->predikat_kinerja) === $key)>
                    {{ $label }}
                    ({{ number_format(\App\Models\KonversiPredikatKinerja::PREDIKAT_PERSENTASE[$key] ?? 0, 0) }}%)
                </option>
            @endforeach
        </select>
        @error('predikat_kinerja')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            Pilih predikat untuk mengisi AK Tambahan secara otomatis berdasarkan jabatan pegawai.
        </div>
    </div>

    {{-- Konversi Indicator — shows the conversion source when predikat is selected --}}
    <div class="col-12" id="konversiIndicator" style="display: none;">
        <div class="konversi-indicator p-3">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i data-feather="zap" class="text-primary" width="20" height="20"></i>
                </div>
                <div class="col">
                    <div class="small text-muted">Konversi AK dari tabel predikat kinerja</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="konversi-value" id="konversiValue">0,000</span>
                        <span class="text-muted small" id="konversiFormula">—</span>
                    </div>
                </div>
                <div class="col-auto">
                    <span class="badge konversi-source" id="konversiSource">—</span>
                </div>
            </div>

            {{-- Quick predikat chips for comparison --}}
            <div class="predikat-info-strip" id="predikatChips">
                @foreach (\App\Models\KonversiPredikatKinerja::PREDIKAT_LABELS as $key => $label)
                    <span class="predikat-chip" data-predikat="{{ $key }}"
                        title="Klik untuk memilih {{ $label }}">
                        {{ $label }}: <strong class="chip-value" data-chip-predikat="{{ $key }}">—</strong>
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- AK Tambahan — auto-filled by predikat selection, but still editable --}}
    <div class="col-12 col-md-6">
        <div class="ak-field-wrapper">
            <label for="ak_tambahan" class="form-label">
                AK Tambahan <small class="text-muted">(dari konversi predikat)</small>
            </label>
            <span class="auto-badge bg-primary-subtle text-primary border border-primary-subtle" id="autoBadge"
                style="display: none;">
                <i data-feather="cpu" class="me-1" width="10" height="10"></i>
                Terisi Otomatis
            </span>
            <input type="number" step="0.001" min="0" id="ak_tambahan" name="ak_tambahan"
                class="form-control @error('ak_tambahan') is-invalid @enderror"
                value="{{ old('ak_tambahan', $currentRiwayatPak?->ak_tambahan) }}"
                placeholder="Pilih predikat untuk mengisi otomatis...">
            @error('ak_tambahan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text" id="akTambahanHint">
                Nilai akan terisi otomatis saat predikat dipilih. Anda tetap bisa mengubahnya secara manual.
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        {{-- Spacer --}}
    </div>

    {{-- Live Preview Panel --}}
    <div class="col-12" id="previewPanel" style="display: none;">
        <div class="card border-primary border mb-0">
            <div class="card-body py-3">
                <div class="d-flex align-items-center mb-2">
                    <i data-feather="eye" class="text-primary me-2" width="18" height="18"></i>
                    <h6 class="card-title mb-0 text-primary">Preview Setelah Input</h6>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <div class="small text-muted">AK Total Baru</div>
                        <span class="fs-5 fw-bold text-primary" id="previewNewTotal">-</span>
                    </div>
                    <div class="col-md-3">
                        <div class="small text-muted">Perubahan</div>
                        <span class="fs-5 fw-bold" id="previewDifference">-</span>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">Progres Baru</span>
                            <span class="small fw-bold" id="previewProgressText">-</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" id="previewProgressBar"
                                style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <a href="{{ route('riwayat-paks.index') }}" class="btn btn-light me-2">Batal</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

@push('scripts')
    <script src="{{ asset('js/riwayat-pak-form.js') }}"></script>
@endpush
