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
                    <x-btn href="{{ route('riwayat-paks.create', ['pegawai_id' => $pegawai->id, 'redirect_to' => url()->current()]) }}" variant="primary" icon="plus" size="sm">
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
                            ['label' => 'TMT Golongan', 'value' => \Carbon\Carbon::parse($pegawai->tmt_golongan)->format('d F Y')],
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
                                                        Target tercapai pada <span class="timeline-year-highlight text-primary">{{ $scenario['projected_period_label'] }}</span>
                                                    </div>
                                                    <div class="text-muted small mt-1">
                                                        Dibutuhkan <span class="fw-bold">{{ $scenario['estimated_time_text'] }}</span> lagi.
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
                                                         title="{{ $scenario['estimated_time_text'] }}">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <span class="timeline-year-label">Sekarang</span>
                                                    <span class="timeline-year-label">{{ $scenario['projected_period_label'] }}</span>
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
                                            $isPreJenjang = \Carbon\Carbon::parse($pak->tanggal_pak)->lt(\Carbon\Carbon::parse($pegawai->tmt_jabatan));
                                            $isSurplusKp = str_starts_with($pak->no_pak, 'SURPLUS-KP-');
                                        @endphp
                                        <tr class="{{ $isLatest ? 'table-primary' : '' }}">
                                            <td>{{ $pak->periode_penilaian_label }}</td>
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
                                                <div class="d-flex flex-column gap-1 align-items-center justify-content-center">
                                                    @if ($isLatest)
                                                        <span class="badge bg-primary">Terbaru</span>
                                                    @endif
                                                    @if (!$pak->is_konversi_baru)
                                                        <span class="badge" style="background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;" title="Angka Kredit Modal Awal / Baseline Konvensional">Baseline / Awal</span>
                                                    @endif
                                                    @if ($isPreJenjang)
                                                        <span class="badge" style="background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;" title="Tidak dihitung karena diperoleh sebelum TMT Jabatan saat ini">Pre-Jenjang</span>
                                                    @endif
                                                    @if ($isSurplusKp)
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" title="Hanya dihitung untuk proyeksi Pangkat, diabaikan untuk proyeksi Jenjang">Surplus Pangkat</span>
                                                    @endif
                                                    @if (!$isLatest && !$isPreJenjang && !$isSurplusKp && $pak->is_konversi_baru)
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2 no-print">
                                                    <x-action-button type="edit" :href="route('riwayat-paks.edit', ['riwayat_pak' => $pak, 'redirect_to' => url()->current()])" />
                                                    <x-action-button type="delete_modal" 
                                                        action="{{ route('riwayat-paks.destroy', ['riwayat_pak' => $pak, 'redirect_to' => url()->current()]) }}" 
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
                                                    :actionUrl="route('riwayat-paks.create', ['pegawai_id' => $pegawai->id, 'redirect_to' => url()->current()])"
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

    <!-- Modal Usulan Kenaikan Pangkat -->
    <div class="modal fade" id="usulanModal" tabindex="-1" aria-labelledby="usulanModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('usulan-pangkat.store') }}" method="POST" enctype="multipart/form-data" id="usulanForm">
                    @csrf
                    <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                    <input type="hidden" name="golongan_baru_id" id="modal_golongan_baru_id" value="">
                    <input type="hidden" name="saldo_ak_awal" id="modal_saldo_ak_awal" value="">
                    <input type="hidden" name="potongan_ak" id="modal_potongan_ak" value="">
                    <input type="hidden" name="sisa_ak" id="modal_sisa_ak" value="">
                    <input type="hidden" name="is_lintas_jenjang" id="modal_is_lintas_jenjang" value="0">
                    <input type="hidden" name="action_type" id="modal_action_type" value="draft">

                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                        <h4 class="modal-title fw-bold text-dark" id="usulanModalLabel">
                            Formulir Usulan Kenaikan Pangkat
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body py-4 px-4">
                        <!-- Bagian A: Ringkasan & Kalkulasi -->
                        <div class="card bg-light border-0 mb-4 shadow-sm">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-3 text-primary"><i data-feather="info" width="16" height="16" class="me-1"></i> Bagian A: Ringkasan Saldo Otomatis</h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1" id="modal_label_target_type">Target Pangkat/Golongan</div>
                                        <div class="fw-bold fs-5 text-dark"><span id="modal_text_current">-</span> <i data-feather="arrow-right" width="16" height="16" class="mx-1 text-muted"></i> <span id="modal_text_next" class="text-primary">-</span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1">Status Saldo Sisa</div>
                                        <div class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i data-feather="lock" width="12" height="12" class="me-1"></i> Diakumulasikan Otomatis</div>
                                    </div>
                                </div>
                                
                                <hr class="my-3 border-secondary-subtle">
                                
                                <div class="row text-center g-2">
                                    <div class="col-4 border-end border-secondary-subtle">
                                        <div class="text-muted small">Saldo AK Saat Ini</div>
                                        <div class="fw-bold fs-5" id="modal_text_ak">-</div>
                                    </div>
                                    <div class="col-4 border-end border-secondary-subtle">
                                        <div class="text-muted small">Potongan Syarat</div>
                                        <div class="fw-bold fs-5 text-danger" id="modal_text_target_ak">-</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small" id="modal_label_surplus">Sisa Modal Baru</div>
                                        <div class="fw-bold fs-5 text-success" id="modal_text_surplus">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bagian B: Upload Dokumen -->
                        <h6 class="fw-bold mb-3 text-primary"><i data-feather="upload" width="16" height="16" class="me-1"></i> Bagian B: Unggah Dokumen Pendukung (PDF)</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">SK Pangkat Terakhir <span class="text-danger">*</span></label>
                                <input class="form-control form-control-sm" type="file" name="sk_pangkat" accept="application/pdf" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">SK Jabatan Fungsional <span class="text-danger">*</span></label>
                                <input class="form-control form-control-sm" type="file" name="sk_jabatan" accept="application/pdf" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">PAK Konversi Terakhir <span class="text-danger">*</span></label>
                                <input class="form-control form-control-sm" type="file" name="pak_konversi" accept="application/pdf" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Evaluasi Kinerja (SKP) <span class="text-danger">*</span></label>
                                <input class="form-control form-control-sm" type="file" name="skp" accept="application/pdf" required>
                            </div>
                        </div>

                        <div id="lintasJenjangDocs" class="row g-3 mt-1" style="display: none;">
                            <div class="col-12"><hr class="my-1 border-secondary-subtle border-dashed"></div>
                            <div class="col-12"><span class="badge bg-warning-subtle text-warning-emphasis">Usulan Lintas Jenjang Terdeteksi</span></div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Sertifikat Lulus Ukom <span class="text-danger">*</span></label>
                                <input class="form-control form-control-sm" type="file" name="ukom" id="input_ukom" accept="application/pdf">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Surat Formasi BAPETEN <span class="text-danger">*</span></label>
                                <input class="form-control form-control-sm" type="file" name="formasi" id="input_formasi" accept="application/pdf">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4 bg-light rounded-bottom">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-secondary px-4 shadow-sm" onclick="submitUsulan('draft')">Simpan sebagai Draf</button>
                        <button type="button" class="btn btn-primary px-4 shadow-sm" onclick="submitUsulan('submit')">Kirim Usulan & Validasi</button>
                    </div>
                </form>
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

            // Usulan Modal Logic
            const usulanModal = document.getElementById('usulanModal');
            if (usulanModal) {
                usulanModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    
                    const type = button.getAttribute('data-type');
                    const currentName = button.getAttribute('data-current');
                    const nextName = button.getAttribute('data-next');
                    const currentAk = parseFloat(button.getAttribute('data-ak'));
                    const targetAk = parseFloat(button.getAttribute('data-target-ak'));
                    const surplus = parseFloat(button.getAttribute('data-surplus'));
                    const golonganBaruId = button.getAttribute('data-golongan-baru');
                    const isPangkatPuncak = button.getAttribute('data-is-pangkat-puncak') === '1';
                    
                    const isLintasJenjang = (type.toLowerCase() === 'jenjang') ? 1 : 0;
                    
                    // Update Modal Title & Target Label dynamically
                    const modalTitle = usulanModal.querySelector('.modal-title');
                    const targetTypeLabel = document.getElementById('modal_label_target_type');
                    
                    if (isPangkatPuncak || isLintasJenjang) {
                        modalTitle.textContent = isPangkatPuncak 
                            ? "Usulan Kenaikan Jenjang Jabatan & Kenaikan Pangkat" 
                            : "Lengkapi Dokumen Usulan Kenaikan Jenjang Jabatan";
                        if (targetTypeLabel) targetTypeLabel.textContent = "Target Jenjang Jabatan";
                    } else {
                        modalTitle.textContent = "Lengkapi Dokumen Usulan Kenaikan Pangkat";
                        if (targetTypeLabel) targetTypeLabel.textContent = "Target Pangkat/Golongan";
                    }
                    
                    let potongan = 0;
                    let sisa = currentAk;
                    
                    const targetAkElement = document.getElementById('modal_text_target_ak');
                    const surplusElement = document.getElementById('modal_text_surplus');
                    const surplusLabelElement = document.getElementById('modal_label_surplus');
                    
                    if (isPangkatPuncak || isLintasJenjang) {
                        potongan = targetAk;
                        sisa = surplus;
                        
                        targetAkElement.textContent = '-' + potongan.toFixed(2);
                        targetAkElement.className = "fw-bold fs-5 text-danger";
                        
                        surplusElement.textContent = '+' + sisa.toFixed(2);
                        surplusElement.className = "fw-bold fs-5 text-success";
                        if (surplusLabelElement) surplusLabelElement.textContent = "Sisa Modal Baru";
                    } else {
                        potongan = 0;
                        sisa = currentAk;
                        
                        targetAkElement.textContent = '0,00 (Tanpa Potongan)';
                        targetAkElement.className = "fw-bold fs-5 text-muted";
                        
                        surplusElement.textContent = '+' + sisa.toFixed(2);
                        surplusElement.className = "fw-bold fs-5 text-success";
                        if (surplusLabelElement) surplusLabelElement.textContent = "Saldo Terakumulasi";
                    }
                    
                    // Populating read-only UI
                    document.getElementById('modal_text_current').textContent = currentName;
                    document.getElementById('modal_text_next').textContent = nextName;
                    document.getElementById('modal_text_ak').textContent = currentAk.toFixed(2);
                    
                    // Populating hidden inputs
                    document.getElementById('modal_saldo_ak_awal').value = currentAk;
                    document.getElementById('modal_potongan_ak').value = potongan;
                    document.getElementById('modal_sisa_ak').value = sisa;
                    document.getElementById('modal_is_lintas_jenjang').value = isPangkatPuncak ? 1 : isLintasJenjang;
                    document.getElementById('modal_golongan_baru_id').value = golonganBaruId;
                    
                    // Simpan flag pangkat puncak untuk validasi
                    document.getElementById('modal_action_type').setAttribute('data-is-pangkat-puncak', isPangkatPuncak ? '1' : '0');
                    
                    const lintasJenjangDocs = document.getElementById('lintasJenjangDocs');
                    const inputUkom = document.getElementById('input_ukom');
                    const inputFormasi = document.getElementById('input_formasi');
                    
                    if (isPangkatPuncak || isLintasJenjang) {
                        lintasJenjangDocs.style.display = 'flex';
                        inputUkom.required = true;
                        inputFormasi.required = true;
                    } else {
                        lintasJenjangDocs.style.display = 'none';
                        inputUkom.required = false;
                        inputFormasi.required = false;
                    }
                });
            }
        });

        function submitUsulan(actionType) {
            const form = document.getElementById('usulanForm');
            const actionTypeInput = document.getElementById('modal_action_type');
            actionTypeInput.value = actionType;
            
            const isPangkatPuncak = actionTypeInput.getAttribute('data-is-pangkat-puncak') === '1';
            
            if (actionType === 'draft') {
                // Remove required attribute from all file inputs so HTML5 validation passes for draft
                form.querySelectorAll('input[type="file"]').forEach(input => {
                    input.required = false;
                });
            } else {
                // Custom Validation for Pangkat Puncak
                if (isPangkatPuncak) {
                    const inputUkom = document.getElementById('input_ukom');
                    const inputFormasi = document.getElementById('input_formasi');
                    
                    if (!inputUkom.files.length || !inputFormasi.files.length) {
                        event.preventDefault();
                        
                        // Show Error Toast
                        if (typeof bootstrap !== 'undefined') {
                            const toastHTML = `
                            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090">
                                <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" id="validationToast">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            <i data-feather="alert-circle" width="16" height="16" class="me-1"></i>
                                            Gagal Validasi: Pegawai berada di pangkat puncak. Dokumen Sertifikat Ukom dan Surat Ketersediaan Formasi wajib diunggah untuk melanjutkan proses perpindahan jenjang jabatannya.
                                        </div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>
                            </div>
                            `;
                            document.body.insertAdjacentHTML('beforeend', toastHTML);
                            if (typeof feather !== 'undefined') feather.replace();
                            const toastElement = document.getElementById('validationToast');
                            const toast = new bootstrap.Toast(toastElement, {delay: 6000});
                            toast.show();
                            
                            // Cleanup toast after it hides
                            toastElement.addEventListener('hidden.bs.toast', function () {
                                toastElement.parentElement.remove();
                            });
                        } else {
                            alert("Gagal Validasi: Pegawai berada di pangkat puncak. Dokumen Sertifikat Ukom dan Surat Ketersediaan Formasi wajib diunggah untuk melanjutkan proses perpindahan jenjang jabatannya.");
                        }
                        
                        return; // Stop submission
                    }
                }
                
                // Re-apply required attributes for submit
                form.querySelector('input[name="sk_pangkat"]').required = true;
                form.querySelector('input[name="sk_jabatan"]').required = true;
                form.querySelector('input[name="pak_konversi"]').required = true;
                form.querySelector('input[name="skp"]').required = true;
                
                // For lintas jenjang/puncak, re-apply conditionally
                const isLintasJenjang = document.getElementById('modal_is_lintas_jenjang').value == '1';
                if (isLintasJenjang) {
                    document.getElementById('input_ukom').required = true;
                    document.getElementById('input_formasi').required = true;
                }
            }

            // Trigger HTML5 validation first
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            form.submit();
        }
    </script>
@endpush
