@php
    $currentRiwayatPak = $riwayatPak ?? null;
    $isEditMode = (bool) $currentRiwayatPak;
@endphp

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
                    data-jenjang="{{ $pegawai->jabatan->jenjang ?? '-' }}"
                    data-golongan="{{ $pegawai->golongan->nama_golongan ?? '-' }}"
                    data-koefisien="{{ $pegawai->jabatan->koefisien_tahunan ?? 0 }}"
                    data-latest-date="{{ $latestPak?->tanggal_pak?->format('d/m/Y') ?? '-' }}"
                    data-latest-nopak="{{ $latestPak?->no_pak ?? '-' }}"
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
        <input type="text" id="no_pak" name="no_pak" class="form-control @error('no_pak') is-invalid @enderror"
            value="{{ old('no_pak', $currentRiwayatPak?->no_pak) }}" placeholder="Contoh: 90/KEP/4028/SK/PAK/2025">
        @error('no_pak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- AK Status Panel —  shown after pegawai selected --}}
    <div class="col-12" id="akStatusPanel" style="display: none;">
        <div class="card border bg-light mb-0">
            <div class="card-body pb-2">
                <div class="d-flex align-items-center mb-3">
                    <i data-feather="bar-chart-2" class="text-primary me-2" width="20" height="20"></i>
                    <h6 class="card-title mb-0">Status Angka Kredit Saat Ini</h6>
                </div>

                {{-- Top Stats Row --}}
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="bg-white border rounded p-2 h-100">
                            <div class="small text-muted mb-1">AK Saat Ini</div>
                            <p class="mb-0 fw-bold fs-5 text-primary" id="displayCurrentAk">0,000</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white border rounded p-2 h-100">
                            <div class="small text-muted mb-1">Target AK</div>
                            <p class="mb-0 fw-bold fs-5 text-dark" id="displayTargetAk">0</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white border rounded p-2 h-100">
                            <div class="small text-muted mb-1">Sisa Kebutuhan</div>
                            <p class="mb-0 fw-bold fs-5" id="displayDeficit">0,000</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white border rounded p-2 h-100">
                            <div class="small text-muted mb-1">PAK Terakhir</div>
                            <p class="mb-0 fw-bold text-dark small" id="displayLastPak">-</p>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Progres Menuju Target</span>
                        <span class="small fw-bold" id="displayProgressText">0%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" id="displayProgressBar"
                            style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                {{-- Info row --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="small"><span class="text-muted">Jabatan:</span> <strong
                                id="displayJabatan">-</strong></div>
                    </div>
                    <div class="col-md-4">
                        <div class="small"><span class="text-muted">Golongan:</span> <strong
                                id="displayGolongan">-</strong></div>
                    </div>
                    <div class="col-md-4">
                        <div class="small"><span class="text-muted">Koefisien/Tahun:</span> <strong
                                id="displayKoefisien">-</strong></div>
                    </div>
                </div>

                {{-- Recent History Table --}}
                <div id="historySection" style="display: none;">
                    <h6 class="small text-muted mb-2">
                        <i data-feather="clock" class="me-1" width="14" height="14"></i>
                        Riwayat AK Terakhir
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered bg-white mb-0 small">
                            <thead class="table-light">
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
        <label for="ak_tambahan" class="form-label">
            AK Tambahan <small class="text-muted">(Delta yang didapat)</small>
        </label>
        <input type="number" step="0.001" min="0" id="ak_tambahan" name="ak_tambahan"
            class="form-control @error('ak_tambahan') is-invalid @enderror"
            value="{{ old('ak_tambahan', $currentRiwayatPak?->ak_tambahan) }}" placeholder="Contoh: 12.500">
        @error('ak_tambahan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
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
            const akTambahanInput = document.getElementById('ak_tambahan');
            const akStatusPanel = document.getElementById('akStatusPanel');
            const previewPanel = document.getElementById('previewPanel');

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

            function formatNumber(num) {
                return num.toFixed(3).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function updateAkStatus() {
                const selected = pegawaiSelect.options[pegawaiSelect.selectedIndex];

                if (!selected || selected.value === '') {
                    akStatusPanel.style.display = 'none';
                    previewPanel.style.display = 'none';
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
                displayDeficit.className = 'mb-0 fw-bold fs-5 ' + (deficit <= 0 ? 'text-success' : 'text-danger');
                displayLastPak.textContent = selected.dataset.latestDate + ' (' + selected.dataset.latestNopak + ')';
                displayProgressText.textContent = progress.toFixed(1) + '%';
                displayProgressBar.style.width = Math.min(progress, 100) + '%';
                displayProgressBar.className = 'progress-bar ' + (progress >= 100 ? 'bg-success' : 'bg-primary');

                displayJabatan.textContent = selected.dataset.jabatan + ' (' + selected.dataset.jenjang + ')';
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
                            '<td class="text-end"><span class="text-success">+' + row.ak_tambahan +
                            '</span></td>' +
                            '<td class="text-end fw-medium">' + row.ak_total + '</td>';
                        historyTableBody.appendChild(tr);
                    });
                } else {
                    historySection.style.display = 'none';
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

            pegawaiSelect.addEventListener('change', updateAkStatus);
            akTambahanInput.addEventListener('input', function() {
                updatePreview();
            });

            // Initialize on page load
            updateAkStatus();
        });
    </script>
@endpush
