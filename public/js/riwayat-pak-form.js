document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('riwayatPakForm');
    const currentPakId = form ? form.dataset.currentPakId : '';
    const generateNoPakUrl = form ? form.dataset.generateNoPakUrl : '';

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
            chip.classList.toggle('selected', chip.dataset.predikat === predikatSelect.value);
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
        displayDeficit.className = 'stat-value ' + (deficit <= 0 ? 'text-success' : 'text-danger');
        displayLastPak.textContent = selected.dataset.latestDate + ' (' + selected.dataset.latestNopak + ')';
        displayProgressText.textContent = progress.toFixed(1) + '%';
        displayProgressBar.style.width = Math.min(progress, 100) + '%';
        displayProgressBar.className = 'progress-bar ' + (progress >= 100 ? 'bg-success' : '');

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
                    '<td class="text-end"><span class="text-success">+' + row.ak_tambahan + '</span></td>' +
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
        previewProgressBar.className = 'progress-bar ' + (newProgress >= 100 ? 'bg-success' : 'bg-primary');

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
                const response = await fetch(generateNoPakUrl);
                const data = await response.json();

                if (data.success) {
                    inputNoPak.value = data.no_pak;
                    inputNoPak.classList.remove('is-invalid');
                    inputNoPak.classList.add('is-valid');
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
