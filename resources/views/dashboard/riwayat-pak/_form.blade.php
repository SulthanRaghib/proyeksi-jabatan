@php
    $currentRiwayatPak = $riwayatPak ?? null;
    $isEditMode = (bool) $currentRiwayatPak;
@endphp

@push('styles')
    <style>
        /* ─── Status AK Panel ──────────────────────────────────────── */
        .ak-status-panel {
            background: linear-gradient(135deg, #eef2ff 0%, #f0f9ff 50%, #f0fdf4 100%);
            border: 1px solid #c7d2fe;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .ak-status-panel .panel-header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ak-status-panel .panel-header h6 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .ak-stat-card {
            background: white;
            border-radius: 0.625rem;
            padding: 0.875rem;
            border: 1px solid #e5e7eb;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .ak-stat-card:hover {
            border-color: #a5b4fc;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.08);
        }

        .ak-stat-card .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ak-stat-card .stat-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .ak-stat-card .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .ak-progress-wrapper {
            background: white;
            border-radius: 0.625rem;
            padding: 0.875rem;
            border: 1px solid #e5e7eb;
        }

        .ak-progress-wrapper .progress {
            height: 10px;
            border-radius: 5px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .ak-progress-wrapper .progress-bar {
            border-radius: 5px;
            background: linear-gradient(90deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ak-progress-wrapper .progress-bar.bg-success {
            background: linear-gradient(90deg, #059669 0%, #10b981 50%, #34d399 100%);
        }

        .ak-info-row {
            background: white;
            border-radius: 0.625rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .ak-info-row .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ak-info-row .info-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ak-history-section {
            background: white;
            border-radius: 0.625rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .ak-history-section .history-header {
            padding: 0.625rem 1rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ak-history-section table {
            margin-bottom: 0;
        }

        .ak-history-section table thead th {
            background: transparent;
            border-top: none;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6b7280;
            font-weight: 600;
            padding: 0.5rem 1rem;
        }

        .ak-history-section table tbody td {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* ─── Konversi Indicator ───────────────────────────────────── */
        .konversi-indicator {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .konversi-indicator.active {
            background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%);
            border: 1px solid #c7d2fe;
        }

        .konversi-indicator .konversi-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4f46e5;
            transition: all 0.3s ease;
        }

        .konversi-indicator .konversi-source {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ─── AK Field & Auto Badge ───────────────────────────────── */
        .ak-field-wrapper {
            position: relative;
        }

        .ak-field-wrapper .auto-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translateY(-50%);
            font-size: 0.65rem;
            padding: 0.15rem 0.5rem;
            border-radius: 1rem;
            z-index: 2;
            transition: opacity 0.3s ease;
        }

        .form-select-predikat option {
            padding: 0.5rem;
        }

        .pulse-highlight {
            animation: pulseHighlight 0.6s ease-out;
        }

        @keyframes pulseHighlight {
            0% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(79, 70, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }

        /* ─── Predikat Chips ───────────────────────────────────────── */
        .predikat-info-strip {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .predikat-info-strip .predikat-chip {
            font-size: 0.72rem;
            padding: 0.2rem 0.6rem;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .predikat-info-strip .predikat-chip:hover {
            border-color: #a5b4fc;
            background: #eef2ff;
            color: #4338ca;
        }

        .predikat-info-strip .predikat-chip.selected {
            border-color: #4f46e5;
            background: #4f46e5;
            color: white;
        }
    </style>
@endpush

<form action="{{ $action }}" method="POST" class="row g-3" novalidate>
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
                    <!-- Options will be populated via AJAX -->
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
        {{-- spacer --}}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pegawaiSelect = document.getElementById('pegawai_id');
            const predikatSelect = document.getElementById('predikat_kinerja');
            const akTambahanInput = document.getElementById('ak_tambahan');
            const akStatusPanel = document.getElementById('akStatusPanel');
            const previewPanel = document.getElementById('previewPanel');
            const konversiIndicator = document.getElementById('konversiIndicator');
            const autoBadge = document.getElementById('autoBadge');
            const periodeAwalInput = document.getElementById('periode_awal');
            const periodeAkhirInput = document.getElementById('periode_akhir');

            // Display elements
            const displayCurrentAk = document.getElementById('displayCurrentAk');
            const displayTargetAk = document.getElementById('displayTargetAk');
            const displayDeficit = document.getElementById('displayDeficit');
            const displayLastPak = document.getElementById('displayLastPak');
            const displayProgressText = document.getElementById('displayProgressText');
            const displayProgressBar = document.getElementById('displayProgressBar');
            const displayJabatan = document.getElementById('displayJabatan');
            const displayGolongan = document.getElementById('displayGolongan');
            const displayKoefisien = document.getElementById('displayKoefisien');
            const historySection = document.getElementById('historySection');
            const historyTableBody = document.getElementById('historyTableBody');

            // Preview elements
            const previewNewTotal = document.getElementById('previewNewTotal');
            const previewDifference = document.getElementById('previewDifference');
            const previewProgressText = document.getElementById('previewProgressText');
            const previewProgressBar = document.getElementById('previewProgressBar');

            // Konversi elements
            const konversiValue = document.getElementById('konversiValue');
            const konversiFormula = document.getElementById('konversiFormula');
            const konversiSource = document.getElementById('konversiSource');

            const isKonversiBaruCheckbox = document.getElementById('is_konversi_baru');
            const predikatKinerjaContainer = document.getElementById('predikatKinerjaContainer');
            const predikatSpacer = document.getElementById('predikatSpacer');
            const akTambahanHint = document.getElementById('akTambahanHint');

            // Track whether AK was auto-filled (to show badge)
            let isAutoFilled = false;

            function formatNumber(num) {
                return num.toFixed(3).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            /**
             * Get the konversi data embedded in the selected pegawai option.
             * This is pre-loaded from the jabatan.konversiPredikat relationship.
             */
            function getKonversiForPegawai() {
                const selected = pegawaiSelect.options[pegawaiSelect.selectedIndex];
                if (!selected || !selected.value) return {};

                try {
                    return JSON.parse(selected.dataset.konversi || '{}');
                } catch (e) {
                    return {};
                }
            }

            /**
             * Fetch konversi AK via AJAX as fallback (when data isn't embedded).
             * Uses the /api/konversi-ak/{pegawai}/{predikat} endpoint.
             */
            async function fetchKonversiFromApi(pegawaiId, predikat) {
                try {
                    const response = await fetch(`/api/konversi-ak/${pegawaiId}/${predikat}`);
                    if (!response.ok) return null;
                    return await response.json();
                } catch (e) {
                    console.warn('Gagal mengambil data konversi:', e);
                    return null;
                }
            }

            /**
             * Fill all predikat chip values for quick visual comparison.
             */
            function fillPredikatChips() {
                const konversi = getKonversiForPegawai();
                const selected = pegawaiSelect.options[pegawaiSelect.selectedIndex];
                const koefisien = selected ? parseFloat(selected.dataset.koefisien || 0) : 0;

                const persentaseMap = {
                    'sangat_baik': 150,
                    'baik': 100,
                    'butuh_perbaikan': 75,
                    'kurang': 50,
                    'sangat_kurang': 25
                };

                document.querySelectorAll('.chip-value').forEach(chip => {
                    const predikat = chip.dataset.chipPredikat;
                    let value = konversi[predikat];

                    // Fallback calculate
                    if (value === undefined && koefisien > 0) {
                        value = koefisien * (persentaseMap[predikat] || 100) / 100;
                    }

                    chip.textContent = value !== undefined ? formatNumber(value) : '—';
                });

                // Highlight the currently selected predikat chip
                document.querySelectorAll('.predikat-chip').forEach(chip => {
                    chip.classList.toggle('selected', chip.dataset.predikat === predikatSelect
                        .value);
                });
            }

            /**
             * Handle predikat selection → auto-fill AK tambahan.
             */
            async function onPredikatChange() {
                const predikat = predikatSelect.value;
                const selected = pegawaiSelect.options[pegawaiSelect.selectedIndex];

                if (!predikat || !selected || !selected.value) {
                    konversiIndicator.style.display = 'none';
                    autoBadge.style.display = 'none';
                    return;
                }

                const pegawaiId = selected.value;
                const koefisien = parseFloat(selected.dataset.koefisien || 0);

                // Try embedded data first
                const konversi = getKonversiForPegawai();
                let nilaiAk = konversi[predikat];
                let source = 'database';

                // If not in embedded data, try API
                if (nilaiAk === undefined) {
                    const apiData = await fetchKonversiFromApi(pegawaiId, predikat);

                    if (apiData && apiData.success) {
                        nilaiAk = apiData.nilai_ak;
                        source = apiData.source;
                    } else {
                        // Ultimate fallback: calculate from koefisien
                        const persentaseMap = {
                            'sangat_baik': 150,
                            'baik': 100,
                            'butuh_perbaikan': 75,
                            'kurang': 50,
                            'sangat_kurang': 25
                        };
                        nilaiAk = koefisien * (persentaseMap[predikat] || 100) / 100;
                        source = 'calculated';
                    }
                }

                // Round to 3 decimals
                nilaiAk = Math.round(nilaiAk * 1000) / 1000;

                // Calculate calendar months if both dates are filled
                let months = 12;
                let isPeriodic = false;
                if (periodeAwalInput && periodeAkhirInput && periodeAwalInput.value && periodeAkhirInput.value) {
                    const startDate = new Date(periodeAwalInput.value);
                    const endDate = new Date(periodeAkhirInput.value);
                    if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime()) && endDate >= startDate) {
                        months = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth()) + 1;
                        if (months < 12 && months > 0) {
                            isPeriodic = true;
                        }
                    }
                }

                let finalNilaiAk = nilaiAk;
                if (isPeriodic) {
                    finalNilaiAk = nilaiAk * (months / 12);
                    // Round to 3 decimals again
                    finalNilaiAk = Math.round(finalNilaiAk * 1000) / 1000;
                }

                // Auto-fill AK tambahan
                akTambahanInput.value = finalNilaiAk.toFixed(3);
                isAutoFilled = true;

                // Pulse animation on the input
                akTambahanInput.classList.add('pulse-highlight');
                setTimeout(() => akTambahanInput.classList.remove('pulse-highlight'), 600);

                // Show auto badge
                autoBadge.style.display = 'inline-flex';
                if (typeof feather !== 'undefined') feather.replace();

                // Show konversi indicator
                konversiIndicator.style.display = 'block';
                konversiIndicator.querySelector('.konversi-indicator').classList.add('active');
                konversiValue.textContent = formatNumber(finalNilaiAk);

                const persentaseMap = {
                    'sangat_baik': 150,
                    'baik': 100,
                    'butuh_perbaikan': 75,
                    'kurang': 50,
                    'sangat_kurang': 25
                };
                
                let formulaText = koefisien.toFixed(2) + ' × ' + (persentaseMap[predikat] || 100) + '%';
                if (isPeriodic) {
                    formulaText += ' × (' + months + '/12 Bulan)';
                }
                formulaText += ' = ' + finalNilaiAk.toFixed(3);
                konversiFormula.textContent = formulaText;

                if (source === 'database') {
                    konversiSource.textContent = 'Dari Tabel Konversi';
                    konversiSource.className = 'badge konversi-source bg-success-subtle text-success';
                } else {
                    konversiSource.textContent = 'Dihitung Otomatis';
                    konversiSource.className = 'badge konversi-source bg-info-subtle text-info';
                }

                // Update chips
                fillPredikatChips();

                // Trigger preview update
                updatePreview();

                // Re-render feather icons
                if (typeof feather !== 'undefined') feather.replace();
            }

            /**
             * When user manually edits AK tambahan, remove the auto badge.
             */
            akTambahanInput.addEventListener('input', function() {
                if (isAutoFilled) {
                    autoBadge.style.display = 'none';
                    isAutoFilled = false;
                }
                updatePreview();
            });

            /**
             * Clicking a predikat chip selects that predikat.
             */
            document.querySelectorAll('.predikat-chip').forEach(chip => {
                chip.addEventListener('click', function() {
                    predikatSelect.value = this.dataset.predikat;
                    onPredikatChange();
                });
            });

            function updateAkStatus() {
                const selected = pegawaiSelect.options[pegawaiSelect.selectedIndex];

                if (!selected || selected.value === '') {
                    akStatusPanel.style.display = 'none';
                    previewPanel.style.display = 'none';
                    konversiIndicator.style.display = 'none';
                    return;
                }

                akStatusPanel.style.display = 'block';

                const currentAk = parseFloat(selected.dataset.currentAk || 0);
                const targetAk = parseFloat(selected.dataset.targetAk || 0);
                const progress = parseFloat(selected.dataset.progress || 0);
                const deficit = Math.max(0, targetAk - currentAk);

                // Fill display values
                displayCurrentAk.textContent = formatNumber(currentAk);
                displayTargetAk.textContent = targetAk.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                displayDeficit.textContent = formatNumber(deficit);
                displayDeficit.className = 'stat-value ' + (deficit <= 0 ? 'text-success' :
                    'text-danger');
                displayLastPak.textContent = selected.dataset.latestDate + ' (' + selected.dataset
                    .latestNopak + ')';
                displayProgressText.textContent = progress.toFixed(1) + '%';
                displayProgressBar.style.width = Math.min(progress, 100) + '%';
                displayProgressBar.className = 'progress-bar ' + (progress >= 100 ? 'bg-success' :
                    '');

                displayJabatan.textContent = selected.dataset.jabatan + ' (' + selected.dataset.jenjang +
                    ')';
                displayGolongan.textContent = selected.dataset.golongan;
                displayKoefisien.textContent = selected.dataset.koefisien;

                // History table
                let history = [];
                try {
                    history = JSON.parse(selected.dataset.history || '[]');
                } catch (e) {}

                if (history.length > 0) {
                    historySection.style.display = 'block';
                    historyTableBody.innerHTML = '';
                    history.forEach(function(row) {
                        const tr = document.createElement('tr');
                        tr.innerHTML =
                            '<td>' + row.tahun + '</td>' +
                            '<td>' + row.no_pak + '</td>' +
                            '<td class="text-end"><span class="text-success">+' + row
                            .ak_tambahan +
                            '</span></td>' +
                            '<td class="text-end fw-medium">' + row.ak_total + '</td>';
                        historyTableBody.appendChild(tr);
                    });
                } else {
                    historySection.style.display = 'none';
                }

                // Fill predikat chips for this pegawai
                fillPredikatChips();

                // If predikat is already selected, re-trigger konversi
                if (predikatSelect.value) {
                    onPredikatChange();
                }

                // Render feather icons in the newly visible panel
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }

                updatePreview(currentAk, targetAk);
            }

            function updatePreview(currentAk, targetAk) {
                const tambahan = parseFloat(akTambahanInput.value || 0);

                if (tambahan <= 0) {
                    previewPanel.style.display = 'none';
                    return;
                }

                previewPanel.style.display = 'block';

                if (typeof currentAk === 'undefined') {
                    const selected = pegawaiSelect.options[pegawaiSelect.selectedIndex];
                    if (!selected || selected.value === '') return;
                    currentAk = parseFloat(selected.dataset.currentAk || 0);
                    targetAk = parseFloat(selected.dataset.targetAk || 0);
                }

                const newTotal = currentAk + tambahan;
                const newProgress = targetAk > 0 ? Math.min(100, (newTotal / targetAk) * 100) : 100;

                previewNewTotal.textContent = formatNumber(newTotal);
                previewDifference.textContent = '+' + formatNumber(tambahan);
                previewDifference.className = 'fs-5 fw-bold text-success';
                previewProgressText.textContent = newProgress.toFixed(1) + '%';
                previewProgressBar.style.width = Math.min(newProgress, 100) + '%';
                previewProgressBar.className = 'progress-bar ' + (newProgress >= 100 ? 'bg-success' :
                    'bg-primary');

                // Re-render icons
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }

            // Event listeners
            pegawaiSelect.addEventListener('change', function() {
                updateAkStatus();
                fetchUnclaimedKinerjas();
            });
            predikatSelect.addEventListener('change', onPredikatChange);
            
            if (periodeAwalInput) {
                periodeAwalInput.addEventListener('change', onPredikatChange);
            }
            if (periodeAkhirInput) {
                periodeAkhirInput.addEventListener('change', onPredikatChange);
            }

            // Kinerja Tahunan Sync Logic
            const skpSyncContainer = document.getElementById('skpSyncContainer');
            const kinerjaSelect = document.getElementById('kinerja_tahunan_id');
            const currentPakId = '{{ $currentRiwayatPak?->id ?? '' }}';
            async function fetchUnclaimedKinerjas() {
                const isKonversi = isKonversiBaruCheckbox ? isKonversiBaruCheckbox.checked : true;
                if (!isKonversi) {
                    skpSyncContainer.style.display = 'none';
                    return;
                }
                if (!kinerjaSelect || !skpSyncContainer) return;
                
                const pegawaiId = pegawaiSelect.value;
                if (!pegawaiId) {
                    skpSyncContainer.style.display = 'none';
                    return;
                }
                
                try {
                    let url = `/api/pegawais/${pegawaiId}/unclaimed-kinerjas`;
                    if (currentPakId) {
                        url += `?current_pak_id=${currentPakId}`;
                    }
                    
                    const response = await fetch(url);
                    const result = await response.json();
                    
                    if (result.success && result.data.length > 0) {
                        // Keep the default option
                        kinerjaSelect.innerHTML = '<option value="">— Buat Kinerja Baru (Input Manual) —</option>';
                        
                        const oldValue = skpSyncContainer.getAttribute('data-old-value');
                        
                        result.data.forEach(kinerja => {
                            const option = document.createElement('option');
                            option.value = kinerja.id;
                            option.setAttribute('data-predikat', kinerja.predikat);
                            option.setAttribute('data-ak', kinerja.raw_ak);
                            option.setAttribute('data-tahun', kinerja.tahun);
                            option.textContent = `[Tahun ${kinerja.tahun}] Predikat: ${kinerja.predikat_label} (AK: ${kinerja.ak_didapat})`;
                            
                            if (oldValue && oldValue == kinerja.id) {
                                option.selected = true;
                            }
                            
                            kinerjaSelect.appendChild(option);
                        });
                        
                        skpSyncContainer.style.display = 'block';
                        
                        // Trigger change to apply any old-value locks
                        if (oldValue) {
                            kinerjaSelect.dispatchEvent(new Event('change'));
                        } else {
                            // If it was previously locked but now no old value, release lock
                            kinerjaSelect.value = "";
                            kinerjaSelect.dispatchEvent(new Event('change'));
                        }
                    } else {
                        skpSyncContainer.style.display = 'none';
                        kinerjaSelect.innerHTML = '<option value="">— Buat Kinerja Baru (Input Manual) —</option>';
                        // Release lock since it's hidden
                        kinerjaSelect.value = "";
                        kinerjaSelect.dispatchEvent(new Event('change'));
                    }
                } catch (error) {
                    console.error('Failed to fetch SKP data:', error);
                    skpSyncContainer.style.display = 'none';
                }
            }
            
            if (kinerjaSelect) {
                kinerjaSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];
                        const predikat = selectedOption.getAttribute('data-predikat');
                        const tahun = selectedOption.getAttribute('data-tahun');
                        
                        // Auto-select predikat
                        if (predikat) {
                            predikatSelect.value = predikat;
                            // Make it visually readonly (pointer-events-none + bg-light)
                            predikatSelect.style.pointerEvents = 'none';
                            predikatSelect.classList.add('bg-light');
                            onPredikatChange(); // trigger AK calculation
                        }
                        
                        // Auto-fill periode akhir to 31/12/[tahun]
                        if (tahun && periodeAkhirInput) {
                            periodeAkhirInput.value = `${tahun}-12-31`;
                            periodeAkhirInput.style.pointerEvents = 'none';
                            periodeAkhirInput.classList.add('bg-light');
                        }
                    } else {
                        // Release lock (Input Manual)
                        predikatSelect.style.pointerEvents = 'auto';
                        predikatSelect.classList.remove('bg-light');
                        
                        if (periodeAkhirInput) {
                            periodeAkhirInput.style.pointerEvents = 'auto';
                            periodeAkhirInput.classList.remove('bg-light');
                            periodeAkhirInput.value = '';
                        }
                    }
                });
                
                // Trigger initial fetches on page load
                if (pegawaiSelect.value) {
                    // updateAkStatus is already triggered below, we just trigger fetch
                    fetchUnclaimedKinerjas();
                }
            }

            // Event listener for generate No PAK button
            const btnGeneratePak = document.getElementById('btn-generate-pak');
            const inputNoPak = document.getElementById('no_pak');

            if (btnGeneratePak && inputNoPak) {
                btnGeneratePak.addEventListener('click', async function() {
                    const originalIcon = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    this.disabled = true;

                    try {
                        const response = await fetch('{{ route('api.generate-no-pak') }}');
                        const data = await response.json();

                        if (data.success) {
                            inputNoPak.value = data.no_pak;
                            inputNoPak.classList.remove('is-invalid');
                            inputNoPak.classList.add('is-valid');
                            
                            // Optional: show a quick toast or UI feedback if you have a toast library
                            // For now, the green border (is-valid) is a good feedback
                        } else {
                            alert('Gagal membuat nomor PAK. Silakan coba lagi.');
                        }
                    } catch (error) {
                        console.error('Error generating No PAK:', error);
                        alert('Terjadi kesalahan jaringan.');
                    } finally {
                        this.innerHTML = originalIcon;
                        this.disabled = false;
                        if (typeof feather !== 'undefined') feather.replace();
                    }
                });
            }

            function toggleKonversiFields() {
                const isKonversi = isKonversiBaruCheckbox ? isKonversiBaruCheckbox.checked : true;
                
                if (isKonversi) {
                    if (predikatKinerjaContainer) predikatKinerjaContainer.style.display = 'block';
                    if (predikatSpacer) predikatSpacer.style.display = 'block';
                    if (akTambahanHint) akTambahanHint.innerHTML = 'Nilai akan terisi otomatis saat predikat dipilih. Anda tetap bisa mengubahnya secara manual.';
                    
                    fetchUnclaimedKinerjas();
                    onPredikatChange();
                } else {
                    if (predikatKinerjaContainer) predikatKinerjaContainer.style.display = 'none';
                    if (predikatSpacer) predikatSpacer.style.display = 'none';
                    if (akTambahanHint) akTambahanHint.innerHTML = 'Masukkan jumlah angka kredit awal/baseline pegawai dari PAK fisik secara manual.';
                    
                    predikatSelect.value = '';
                    if (konversiIndicator) konversiIndicator.style.display = 'none';
                    if (autoBadge) autoBadge.style.display = 'none';
                    if (skpSyncContainer) skpSyncContainer.style.display = 'none';
                    
                    predikatSelect.style.pointerEvents = 'auto';
                    predikatSelect.classList.remove('bg-light');
                    if (periodeAkhirInput) {
                        periodeAkhirInput.style.pointerEvents = 'auto';
                        periodeAkhirInput.classList.remove('bg-light');
                    }
                    
                    updatePreview();
                }
            }

            if (isKonversiBaruCheckbox) {
                isKonversiBaruCheckbox.addEventListener('change', toggleKonversiFields);
            }

            // Initialize on page load
            updateAkStatus();
            if (isKonversiBaruCheckbox) {
                toggleKonversiFields();
            }
        });
    </script>
@endpush
