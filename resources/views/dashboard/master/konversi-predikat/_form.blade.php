@php
    $currentKonversi = $konversi ?? null;
    $isEditMode = (bool) $currentKonversi;
@endphp

<form action="{{ $action }}" method="POST" class="row g-3" novalidate>
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    {{-- Jabatan Selection --}}
    <div class="col-12 col-md-6">
        <label for="jabatan_id" class="form-label">Jabatan Fungsional</label>
        <select id="jabatan_id" name="jabatan_id" class="form-select @error('jabatan_id') is-invalid @enderror"
            {{ $isEditMode ? 'disabled' : '' }}>
            <option value="" disabled {{ old('jabatan_id', $currentKonversi?->jabatan_id) ? '' : 'selected' }}>
                Pilih jabatan...
            </option>
            @foreach ($jabatans as $jabatan)
                <option value="{{ $jabatan->id }}"
                    @selected((string) old('jabatan_id', $currentKonversi?->jabatan_id) === (string) $jabatan->id)
                    data-koefisien="{{ $jabatan->koefisien_tahunan }}"
                    data-kategori="{{ $jabatan->kategori }}">
                    [{{ ucfirst($jabatan->kategori) }}] {{ $jabatan->nama_jabatan }} — {{ $jabatan->jenjang }}
                    (Koef: {{ number_format((float) $jabatan->koefisien_tahunan, 2, ',', '.') }})
                </option>
            @endforeach
        </select>
        @if ($isEditMode)
            <input type="hidden" name="jabatan_id" value="{{ $currentKonversi->jabatan_id }}">
        @endif
        @error('jabatan_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Predikat Selection --}}
    <div class="col-12 col-md-6">
        <label for="predikat" class="form-label">Predikat Kinerja</label>
        <select id="predikat" name="predikat" class="form-select @error('predikat') is-invalid @enderror"
            {{ $isEditMode ? 'disabled' : '' }}>
            <option value="" disabled {{ old('predikat', $currentKonversi?->predikat) ? '' : 'selected' }}>
                Pilih predikat...
            </option>
            @foreach ($predikatOptions as $key)
                <option value="{{ $key }}" @selected((string) old('predikat', $currentKonversi?->predikat) === $key)
                    data-persentase="{{ $predikatPersentase[$key] ?? 100 }}">
                    {{ $predikatLabels[$key] }}
                    ({{ number_format($predikatPersentase[$key] ?? 100, 0) }}%)
                </option>
            @endforeach
        </select>
        @if ($isEditMode)
            <input type="hidden" name="predikat" value="{{ $currentKonversi->predikat }}">
        @endif
        @error('predikat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Persentase --}}
    <div class="col-12 col-md-6">
        <label for="persentase" class="form-label">
            Persentase Koefisien <small class="text-muted">(%)</small>
        </label>
        <div class="input-group">
            <input type="number" step="0.01" min="0" max="999.99" id="persentase" name="persentase"
                class="form-control @error('persentase') is-invalid @enderror"
                value="{{ old('persentase', $currentKonversi?->persentase) }}" placeholder="Contoh: 150.00">
            <span class="input-group-text">%</span>
            @error('persentase')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text">Persentase dari koefisien tahunan jabatan. Sangat Baik = 150%, Baik = 100%, dsb.</div>
    </div>

    {{-- Nilai AK --}}
    <div class="col-12 col-md-6">
        <label for="nilai_ak" class="form-label">
            Nilai AK Tahunan <small class="text-muted">(hasil konversi)</small>
        </label>
        <input type="number" step="0.001" min="0" id="nilai_ak" name="nilai_ak"
            class="form-control @error('nilai_ak') is-invalid @enderror"
            value="{{ old('nilai_ak', $currentKonversi?->nilai_ak) }}" placeholder="Contoh: 18.750">
        @error('nilai_ak')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Nilai AK = Koefisien Tahunan × (Persentase / 100)</div>
    </div>

    {{-- Auto-calculate Preview --}}
    <div class="col-12" id="calcPreview" style="display: none;">
        <div class="alert alert-info d-flex align-items-center mb-0 py-2">
            <i data-feather="info" class="me-2 flex-shrink-0" width="18" height="18"></i>
            <div>
                <strong>Perhitungan Otomatis:</strong>
                <span id="calcKoefisien">0</span> × (<span id="calcPersentase">0</span>% / 100) =
                <strong id="calcResult">0</strong>
            </div>
        </div>
    </div>

    <div class="col-12">
        <a href="{{ route('konversi-predikats.index') }}" class="btn btn-light me-2">Batal</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        <button type="button" class="btn btn-outline-info ms-2" id="btnAutoCalc">
            <i data-feather="cpu" class="me-1" width="16" height="16"></i>
            Hitung Otomatis
        </button>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jabatanSelect = document.getElementById('jabatan_id');
            const predikatSelect = document.getElementById('predikat');
            const persentaseInput = document.getElementById('persentase');
            const nilaiAkInput = document.getElementById('nilai_ak');
            const calcPreview = document.getElementById('calcPreview');
            const calcKoefisien = document.getElementById('calcKoefisien');
            const calcPersentase = document.getElementById('calcPersentase');
            const calcResult = document.getElementById('calcResult');
            const btnAutoCalc = document.getElementById('btnAutoCalc');

            function getKoefisien() {
                const selected = jabatanSelect.options[jabatanSelect.selectedIndex];
                return selected && selected.value ? parseFloat(selected.dataset.koefisien || 0) : 0;
            }

            function getSelectedPersentase() {
                const selected = predikatSelect.options[predikatSelect.selectedIndex];
                return selected && selected.value ? parseFloat(selected.dataset.persentase || 0) : 0;
            }

            // Auto-fill persentase when predikat changes
            predikatSelect.addEventListener('change', function() {
                const persen = getSelectedPersentase();
                if (persen > 0) {
                    persentaseInput.value = persen.toFixed(2);
                    autoCalculate();
                }
            });

            function autoCalculate() {
                const koef = getKoefisien();
                const persen = parseFloat(persentaseInput.value || 0);

                if (koef > 0 && persen > 0) {
                    const result = (koef * (persen / 100)).toFixed(3);
                    calcKoefisien.textContent = koef.toFixed(2);
                    calcPersentase.textContent = persen.toFixed(0);
                    calcResult.textContent = result;
                    calcPreview.style.display = 'block';
                    nilaiAkInput.value = result;
                } else {
                    calcPreview.style.display = 'none';
                }
            }

            btnAutoCalc.addEventListener('click', autoCalculate);
            persentaseInput.addEventListener('input', autoCalculate);
            jabatanSelect.addEventListener('change', function() {
                if (predikatSelect.value) {
                    autoCalculate();
                }
            });

            // Re-render feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endpush
