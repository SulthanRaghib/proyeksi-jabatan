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

    {{-- Pegawai Selection --}}
    <div class="col-12 col-md-6">
        <label for="pegawai_id" class="form-label">Pegawai</label>
        <select id="pegawai_id" name="pegawai_id" class="form-select @error('pegawai_id') is-invalid @enderror"
            {{ $currentRiwayatPak ? 'disabled' : '' }}>
            <option value="" disabled {{ old('pegawai_id', $currentRiwayatPak?->pegawai_id) ? '' : 'selected' }}>
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
                            'tahun' => $pak->tanggal_pak->format('Y'),
                            'ak_total' => number_format((float) $pak->ak_total, 3, ',', '.'),
                            'ak_tambahan' => number_format((float) $pak->ak_tambahan, 3, ',', '.'),
                            'no_pak' => $pak->no_pak,
                        ];
                    })->values()->toArray();
                @endphp
                <option value="{{ $pegawai->id }}" @selected((string) old('pegawai_id', $currentRiwayatPak?->pegawai_id) === (string) $pegawai->id)
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

    <div class="col-12 col-md-6">
        <label for="tanggal_pak" class="form-label">Tanggal PAK</label>
        <input type="date" id="tanggal_pak" name="tanggal_pak"
            class="form-control @error('tanggal_pak') is-invalid @enderror"
            value="{{ old('tanggal_pak', $currentRiwayatPak?->tanggal_pak?->format('Y-m-d')) }}">
        @error('tanggal_pak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Predikat Kinerja — placed BEFORE AK Tambahan so it drives the auto-fill --}}
    <div class="col-12 col-md-6">
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

                // Auto-fill AK tambahan
                akTambahanInput.value = nilaiAk.toFixed(3);
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
                konversiValue.textContent = formatNumber(nilaiAk);

                const persentaseMap = {
                    'sangat_baik': 150,
                    'baik': 100,
                    'butuh_perbaikan': 75,
                    'kurang': 50,
                    'sangat_kurang': 25
                };
                konversiFormula.textContent = koefisien.toFixed(2) + ' × ' + (persentaseMap[predikat] ||
                    100) + '% = ' + nilaiAk.toFixed(3);

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
            pegawaiSelect.addEventListener('change', updateAkStatus);
            predikatSelect.addEventListener('change', onPredikatChange);

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

            // Initialize on page load
            updateAkStatus();
        });
    </script>
@endpush
