@extends('layouts.dashboard')

@section('title', 'Detail Proyeksi Jabatan - ' . $pegawai->nama_lengkap)

@section('content')
    {{-- Page Header with Breadcrumbs --}}
    <x-page-header 
        title="Detail Proyeksi Jabatan"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Proyeksi Jabatan', 'url' => route('projections.index')],
            ['label' => $pegawai->nama_lengkap]
        ]"
        :hasAction="true">
        <x-slot:action>
            <x-btn href="{{ route('projections.index') }}" variant="soft" icon="arrow-left" size="sm">
                Kembali
            </x-btn>
        </x-slot:action>
    </x-page-header>

    {{-- Sticky Action Bar --}}
    <div class="sticky-action-bar no-print" id="stickyActionBar">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <x-btn href="{{ route('riwayat-paks.create', ['pegawai_id' => $pegawai->id]) }}" variant="primary" icon="plus" size="sm">
                        Tambah Riwayat PAK
                    </x-btn>
                    
                    <x-btn href="{{ route('pegawais.edit', $pegawai) }}" variant="soft" icon="edit-2" size="sm">
                        Edit Pegawai
                    </x-btn>
                    
                    <x-btn type="button" variant="soft" icon="printer" size="sm" onclick="window.print()">
                        Cetak
                    </x-btn>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Terakhir diperbarui: {{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Employee Header with Status Badge --}}
        <div class="row mb-4 align-items-center">
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h2 class="mb-0 me-2">{{ $pegawai->nama_lengkap }}</h2>
                    @php
                        $headerProj = $full_projection['pangkat'];
                    @endphp
                    @if ($headerProj['is_fully_ready'])
                        <span class="badge bg-success-subtle text-dark border border-success-subtle fs-6 px-3 py-2">
                            <i data-feather="check-circle" width="16" height="16" class="me-1"></i>
                            Siap AK & Syarat
                        </span>
                    @elseif ($headerProj['is_held_by_speedbump'])
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle fs-6 px-3 py-2">
                            <i data-feather="clock" width="16" height="16" class="me-1"></i>
                            Tertahan Waktu
                        </span>
                    @elseif ($headerProj['is_ready_mathematically'])
                        <span class="badge bg-info-subtle text-dark border border-info-subtle fs-6 px-3 py-2">
                            <i data-feather="check-circle" width="16" height="16" class="me-1"></i>
                            Siap AK
                        </span>
                    @else
                        <span class="badge bg-primary-subtle text-dark border border-primary-subtle fs-6 px-3 py-2">
                            <i data-feather="trending-up" width="16" height="16" class="me-1"></i>
                            Proses AK
                        </span>
                    @endif
                </div>
                <p class="text-muted mt-1 mb-0">
                    <i data-feather="user" width="16" height="16" class="me-1"></i>
                    NIP: {{ $pegawai->nip }}
                </p>
            </div>
        </div>

        <div class="row">
            {{-- Card 1: Profile Info - Using info-card component --}}
            <div class="col-md-5 col-lg-4 mb-4">
                <div class="position-sticky" style="top: 90px;">
                    @php
                        $infoItems = [
                            ['label' => 'Unit Kerja', 'value' => $pegawai->unitKerja->nama_unit ?? '-'],
                            ['label' => 'Golongan Saat Ini', 'value' => $pegawai->golongan->nama_golongan ?? '-'],
                            ['label' => 'Jabatan Saat Ini', 'value' => $pegawai->jabatan->nama_jabatan ?? '-'],
                            [
                                'label' => 'Jenjang / Kategori',
                                'value' => ($pegawai->jabatan->jenjang ?? '-') .
                                    ($pegawai->jabatan?->kategori
                                        ? ' <span class="badge bg-light text-dark border ms-1">' . ucfirst($pegawai->jabatan->kategori) . '</span>'
                                        : '')
                            ],
                            ['label' => 'TMT Jabatan', 'value' => \Carbon\Carbon::parse($pegawai->tmt_jabatan)->format('d F Y')],
                            [
                                'label' => 'Koefisien AK Tahunan',
                                'value' => '<span class="text-primary fs-5 fw-bold">' .
                                    number_format((float)($pegawai->jabatan->koefisien_tahunan ?? 0), 2, ',', '.') .
                                    '</span>'
                            ],
                        ];
                    @endphp
                    <x-info-card title="Informasi Pegawai" :items="$infoItems" />
                </div>
            </div>

            {{-- Card 2: Projection Summary --}}
            <div class="col-md-7 col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Ringkasan Proyeksi</h4>
                            <div class="d-flex align-items-center">
                                <span class="me-2 small text-muted">Surplus AK:</span>
                                <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#surplusModal">
                                    <i data-feather="settings" width="14" height="14"></i>
                                    {{ ucfirst($surplusBehavior) }}
                                </button>
                            </div>
                        </div>

                        <!-- Tabs for Pangkat and Jenjang -->
                        <ul class="nav nav-tabs mb-4" id="projectionTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-medium" id="pangkat-tab" data-bs-toggle="tab" data-bs-target="#pangkat-tab-pane" type="button" role="tab">Kenaikan Pangkat</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-medium" id="jenjang-tab" data-bs-toggle="tab" data-bs-target="#jenjang-tab-pane" type="button" role="tab">Kenaikan Jenjang</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="projectionTabsContent">
                            <!-- Tab Pangkat -->
                            <div class="tab-pane fade show active" id="pangkat-tab-pane" role="tabpanel" tabindex="0">
                                @include('dashboard.proyeksi-jabatan.partials.projection-card', ['proj' => $full_projection['pangkat'], 'type' => 'Pangkat'])
                            </div>
                            
                            <!-- Tab Jenjang -->
                            <div class="tab-pane fade" id="jenjang-tab-pane" role="tabpanel" tabindex="0">
                                @include('dashboard.proyeksi-jabatan.partials.projection-card', ['proj' => $full_projection['jenjang'], 'type' => 'Jenjang'])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Konversi Predikat Kinerja Table --}}
        @if (!empty($konversiSummary))
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="card-title mb-1">Tabel Konversi Predikat Kinerja</h4>
                                    <p class="text-muted small mb-0">
                                        Nilai AK tahunan berdasarkan predikat kinerja untuk
                                        <strong>{{ $pegawai->jabatan->nama_jabatan }}
                                            ({{ $pegawai->jabatan->jenjang }})</strong>
                                    </p>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    Koefisien:
                                    {{ number_format((float) $pegawai->jabatan->koefisien_tahunan, 2, ',', '.') }}
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="border-top-0">Predikat</th>
                                            <th class="border-top-0">Persentase</th>
                                            <th class="border-top-0">AK Tahunan</th>
                                            <th class="border-top-0">AK per Periode (6×/tahun)</th>
                                            <th class="border-top-0">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($konversiSummary as $key => $data)
                                            <tr class="{{ $key === $full_projection['pangkat']['predikat'] ? 'active-predikat' : '' }}">
                                                <td>
                                                    <span
                                                        class="badge border {{ $data['badge_class'] }} px-2 py-1">{{ $data['label'] }}</span>
                                                </td>
                                                <td class="fw-medium">{{ number_format($data['persentase'], 0) }}%</td>
                                                <td class="fw-bold fs-5">
                                                    {{ number_format($data['nilai_ak'], 3, ',', '.') }}</td>
                                                <td>{{ number_format($data['nilai_ak'] / 6, 3, ',', '.') }}</td>
                                                <td>
                                                    @if ($key === $full_projection['pangkat']['predikat'])
                                                        <span class="badge bg-primary">Aktif</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Card 4: Estimation Timeline UI --}}
        @if (!empty($estimationScenarios) && !empty($estimationScenarios['scenarios']))
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h4 class="card-title mb-0">Timeline Estimasi Kenaikan Pangkat</h4>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    Target AK: {{ number_format($estimationScenarios['target_ak'], 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="text-muted small mb-4">
                                Berapa lama waktu yang dibutuhkan dari kondisi AK saat ini ({{ number_format($estimationScenarios['current_ak'], 2, ',', '.') }}) 
                                untuk mencapai target berdasarkan konsistensi predikat kinerja setiap tahunnya.
                            </p>

                            <div class="row g-3">
                                @foreach ($estimationScenarios['scenarios'] as $predikat => $scenario)
                                    <div class="col-12 col-md-6 col-xl">
                                        <div class="estimation-card p-3 {{ $scenario['is_active'] ? 'active' : '' }} h-100 d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge border {{ $scenario['badge_class'] }} px-2 py-1 mb-1">{{ $scenario['label'] }}</span>
                                                    @if ($scenario['is_fastest'])
                                                        <span class="timeline-badge bg-success-subtle text-success ms-1">Tercepat</span>
                                                    @endif
                                                    @if ($scenario['is_slowest'])
                                                        <span class="timeline-badge bg-danger-subtle text-danger ms-1">Terlama</span>
                                                    @endif
                                                </div>
                                                @if ($scenario['is_active'])
                                                    <span class="badge bg-primary rounded-pill"><i data-feather="check" width="12" height="12"></i> Aktif</span>
                                                @endif
                                            </div>

                                            <div class="mb-auto mt-2">
                                                @if ($scenario['years_needed'] === null)
                                                    <div class="text-danger small fw-bold">
                                                        <i data-feather="alert-circle" width="14" height="14" class="me-1"></i>
                                                        Tidak Dapat Diproyeksikan
                                                    </div>
                                                    <div class="text-muted small mt-1">Nilai AK tahunan 0.</div>
                                                @elseif ($scenario['is_ready'])
                                                    <div class="text-success small fw-bold">
                                                        <i data-feather="check-circle" width="14" height="14" class="me-1"></i>
                                                        Target Telah Tercapai
                                                    </div>
                                                    <div class="text-muted small mt-1">Siap untuk diusulkan.</div>
                                                @else
                                                    <div class="text-dark small">
                                                        Target tercapai tahun <span class="timeline-year-highlight text-primary">{{ $scenario['projected_year'] }}</span>
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        Dibutuhkan <span class="fw-bold">{{ $scenario['years_needed'] }} tahun</span> lagi.
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($scenario['years_needed'] !== null && !$scenario['is_ready'])
                                                @php
                                                    // Calculate width percentage relative to the max years in all scenarios
                                                    // We give a min width of 15% so it's visible, and max 100%
                                                    $widthPercent = min(100, max(15, ($scenario['years_needed'] / $estimationScenarios['max_years']) * 100));
                                                @endphp
                                                <div class="scenario-bar-container mt-3">
                                                    <div class="scenario-bar {{ $scenario['is_active'] ? 'active-scenario' : '' }}" 
                                                         style="width: {{ $widthPercent }}%; background-color: {{ $scenario['color'] }};"
                                                         title="{{ $scenario['years_needed'] }} tahun">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="timeline-year-label">Sekarang</span>
                                                    <span class="timeline-year-label">{{ $scenario['projected_year'] }}</span>
                                                </div>
                                            @elseif ($scenario['is_ready'])
                                                <div class="scenario-bar-container mt-3">
                                                    <div class="scenario-bar active-scenario" style="width: 100%; background-color: #10b981;"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="timeline-year-label">Sekarang</span>
                                                    <span class="timeline-year-label">Tercapai</span>
                                                </div>
                                            @endif
                                            
                                            <div class="mt-3 pt-2 border-top">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small text-muted">AK Tahunan:</span>
                                                    <span class="fw-bold fs-6">{{ number_format($scenario['annual_ak'], 3, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- Card 5: Chart --}}
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body overflow-hidden">
                        <h4 class="card-title mb-4">Tren Angka Kredit</h4>
                        <div id="ak-trend-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Card 6: History Table with predikat column --}}
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Riwayat Angka Kredit</h4>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-top-0">Tahun</th>
                                        <th class="border-top-0">No. PAK</th>
                                        <th class="border-top-0">Predikat</th>
                                        <th class="border-top-0 text-end">AK Tambahan</th>
                                        <th class="border-top-0 text-end">Total AK</th>
                                        <th class="border-top-0 text-center">Perubahan</th>
                                        <th class="border-top-0 text-center">Status</th>
                                        <th class="border-top-0 text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $previousAk = null;
                                        $latestId = $pegawai->riwayatPaks->sortBy([
                                            ['tanggal_pak', 'desc'],
                                            ['id', 'desc']
                                        ])->first()?->id;

                                        // Calculate differences chronologically first
                                        $processedPaks = collect();
                                        foreach ($pegawai->riwayatPaks as $pak) {
                                            $akTotal = (float) $pak->ak_total;
                                            $difference = $previousAk !== null ? $akTotal - $previousAk : null;
                                            
                                            // Store difference temporarily on the object
                                            $pak->calculated_difference = $difference;
                                            $processedPaks->push($pak);
                                            
                                            $previousAk = $akTotal;
                                        }
                                        
                                        // Reverse for rendering newest first
                                        $processedPaks = $processedPaks->reverse();
                                    @endphp
                                    @forelse ($processedPaks as $pak)
                                        @php
                                            $akTotal = (float) $pak->ak_total;
                                            $akTambahan = (float) $pak->ak_tambahan;
                                            $difference = $pak->calculated_difference;
                                            $isLatest = $pak->id === $latestId;
                                        @endphp
                                        <tr class="{{ $isLatest ? 'table-primary' : '' }}">
                                            <td>{{ \Carbon\Carbon::parse($pak->tanggal_pak)->format('d/m/Y') }}</td>
                                            <td>{{ $pak->no_pak ?? '-' }}</td>
                                            <td>
                                                @if ($pak->predikat_kinerja)
                                                    <span class="badge border {{ $pak->predikat_badge_class }} px-2 py-1">
                                                        {{ $pak->predikat_label }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($akTambahan > 0)
                                                    <span
                                                        class="text-success fw-medium">+{{ number_format($akTambahan, 3, ',', '.') }}</span>
                                                @else
                                                    <span
                                                        class="text-muted">{{ number_format($akTambahan, 3, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-medium">{{ number_format($akTotal, 3, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if ($difference !== null)
                                                    @if ($difference > 0)
                                                        <span
                                                            class="badge bg-success">+{{ number_format($difference, 3, ',', '.') }}</span>
                                                    @elseif ($difference < 0)
                                                        <span
                                                            class="badge bg-danger">{{ number_format($difference, 3, ',', '.') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">0</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-info">Awal</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($isLatest)
                                                    <span class="badge bg-primary">Terbaru</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2 no-print">
                                                    <x-action-button type="edit" :href="route('riwayat-paks.edit', $pak)" />
                                                    <x-action-button type="delete_modal" 
                                                        action="{{ route('riwayat-paks.destroy', $pak) }}" 
                                                        message="Yakin ingin menghapus riwayat PAK ini?" />
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <x-empty-state 
                                                    icon="file-text"
                                                    title="Belum ada riwayat PAK"
                                                    description="Mulai tambahkan riwayat PAK untuk melihat proyeksi yang akurat"
                                                    :actionUrl="route('riwayat-paks.create', ['pegawai_id' => $pegawai->id])"
                                                    actionText="Tambah Riwayat PAK"
                                                />
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('dashboard.proyeksi-jabatan.partials.kinerja-tahunan-section')

    </div>

    <!-- Modal Konfigurasi Surplus -->
    <div class="modal fade" id="surplusModal" tabindex="-1" aria-labelledby="surplusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="surplusModalLabel">
                        <i data-feather="settings" class="me-2 text-primary" width="20" height="20"></i> Pengaturan Surplus AK
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <p class="text-muted mb-4 small text-center">Silakan pilih metode yang digunakan untuk menangani kelebihan Angka Kredit setelah target kenaikan tercapai.</p>
                    
                    <div class="d-grid gap-3">
                        <a href="?surplus_behavior=hangus" class="text-decoration-none d-block">
                            <div class="card transition-base border-2 shadow-sm {{ $surplusBehavior === 'hangus' ? 'border-danger bg-danger-subtle' : 'border-secondary-subtle bg-white' }}" style="border-radius: 0.75rem; cursor: pointer;" onmouseover="this.classList.add('shadow-md');" onmouseout="this.classList.remove('shadow-md');">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="bg-white rounded d-flex align-items-center justify-content-center me-3 shadow-sm border border-light" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i data-feather="trash-2" class="{{ $surplusBehavior === 'hangus' ? 'text-danger' : 'text-secondary' }}" width="20" height="20"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold {{ $surplusBehavior === 'hangus' ? 'text-danger' : 'text-dark' }}">Dihanguskan (Default)</h6>
                                            <p class="mb-0 small {{ $surplusBehavior === 'hangus' ? 'text-danger-emphasis' : 'text-muted' }} lh-sm">
                                                Kelebihan AK dibuang, tidak dibawa ke jenjang/pangkat selanjutnya sesuai aturan konvensional.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                        <a href="?surplus_behavior=akumulasi" class="text-decoration-none d-block">
                            <div class="card transition-base border-2 shadow-sm {{ $surplusBehavior === 'akumulasi' ? 'border-success bg-success-subtle' : 'border-secondary-subtle bg-white' }}" style="border-radius: 0.75rem; cursor: pointer;" onmouseover="this.classList.add('shadow-md');" onmouseout="this.classList.remove('shadow-md');">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="bg-white rounded d-flex align-items-center justify-content-center me-3 shadow-sm border border-light" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i data-feather="plus-circle" class="{{ $surplusBehavior === 'akumulasi' ? 'text-success' : 'text-secondary' }}" width="20" height="20"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold {{ $surplusBehavior === 'akumulasi' ? 'text-success' : 'text-dark' }}">Diakumulasikan</h6>
                                            <p class="mb-0 small {{ $surplusBehavior === 'akumulasi' ? 'text-success-emphasis' : 'text-muted' }} lh-sm">
                                                Kelebihan AK disimpan sebagai saldo tabungan awal untuk target jenjang/pangkat selanjutnya.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sticky action bar scroll effect
            const stickyBar = document.getElementById('stickyActionBar');
            if (stickyBar) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 100) {
                        stickyBar.classList.add('scrolled');
                    } else {
                        stickyBar.classList.remove('scrolled');
                    }
                });
            }

            // Chart initialization
            if (typeof c3 !== 'undefined') {
                var chartYears = {!! json_encode($chartYears) !!};
                var chartAk = {!! json_encode($chartAk) !!};
                var chartPredikat = {!! json_encode($chartPredikat) !!};
                var chartAkTambahan = {!! json_encode($chartAkTambahan) !!};

                // Only generate chart if there's data
                if (chartYears.length > 0 && chartAk.length > 0) {
                    var chart = c3.generate({
                        bindto: '#ak-trend-chart',
                        data: {
                            x: 'x',
                            columns: [
                                ['x', ...chartYears],
                                ['Total AK', ...chartAk]
                            ],
                            type: 'line',
                            colors: {
                                'Total AK': '#4f46e5'
                            }
                        },
                        padding: {
                            right: 30,
                            left: 40
                        },
                        axis: {
                            x: {
                                type: 'category',
                                tick: {
                                    centered: true
                                }
                            },
                            y: {
                                tick: {
                                    format: d3.format(".2f")
                                }
                            }
                        },
                        grid: {
                            y: {
                                show: true
                            }
                        },
                        point: {
                            r: 4
                        },
                        tooltip: {
                            contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                                var idx = d[0].index;
                                var year = chartYears[idx];
                                var totalAk = d[0].value.toFixed(3);
                                var predikat = chartPredikat[idx];
                                var akTambahan = parseFloat(chartAkTambahan[idx]).toFixed(3);
                                
                                return `
                                    <div class="card border-0 shadow custom-chart-tooltip" style="min-width: 160px; font-family: inherit;">
                                        <div class="card-header bg-light py-2 px-3 border-bottom border-secondary-subtle">
                                            <span class="fw-bold small text-dark">Tahun ${year}</span>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="small text-muted me-3"><span style="display:inline-block;width:10px;height:10px;background-color:${color(d[0].id)};border-radius:50%;margin-right:6px;"></span>Total AK</span>
                                                <span class="fw-bold text-dark">${totalAk}</span>
                                            </div>
                                            <hr class="my-2 border-secondary-subtle opacity-50">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small text-muted me-3">Predikat</span>
                                                <span class="fw-medium text-dark small">${predikat}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small text-muted me-3">AK Konversi</span>
                                                <span class="fw-bold text-primary small">+${akTambahan}</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    });

                    // Fix for C3 graph overflow on initial load (wait for layout to settle)
                    setTimeout(function() {
                        chart.resize();
                    }, 300);
                } else {
                    document.getElementById('ak-trend-chart').innerHTML =
                        '<div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted">' +
                        '<i data-feather="bar-chart-2" width="48" height="48" class="mb-3 opacity-50"></i>' +
                        '<p class="mb-0">Data trend tidak tersedia</p>' +
                        '</div>';
                }

                // Re-render icons if feather is available
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }

            // Smooth scroll to sections
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
@endpush
