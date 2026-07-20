<div class="row text-center mb-4">
    <div class="col-3 border-end">
        <h2 class="mb-0 text-dark">{{ number_format($proj['current_ak'], 2, ',', '.') }}</h2>
        <span class="text-muted small">AK Saat Ini</span>
    </div>
    <div class="col-3 border-end">
        @if($type === 'Jenjang' && $proj['target_ak'] == 0 && $proj['next_target_name'] === 'Maksimal')
            <h4 class="mb-0 text-dark mt-1">Maksimal</h4>
        @else
            <h2 class="mb-0 text-dark">{{ number_format($proj['target_ak'], 0, ',', '.') }}</h2>
        @endif
        <span class="text-muted small">Target AK</span>
    </div>
    <div class="col-3 border-end">
        @if($type === 'Jenjang' && $proj['target_ak'] == 0 && $proj['next_target_name'] === 'Maksimal')
            <h4 class="mb-0 text-muted mt-1">-</h4>
        @else
            <h2 class="mb-0 {{ $proj['deficit_ak'] <= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($proj['deficit_ak'], 2, ',', '.') }}
            </h2>
        @endif
        <span class="text-muted small">Kebutuhan AK</span>
    </div>
    <div class="col-3">
        <h2 class="mb-0 text-primary">
            {{ number_format($proj['annual_ak'], 3, ',', '.') }}
        </h2>
        <span class="text-muted small">AK/Tahun ({{ $proj['predikat_label'] }})</span>
    </div>
</div>

<div class="mb-4">
    <div class="d-flex justify-content-between mb-1">
        <span class="text-muted">Progres Pencapaian</span>
        @if($type === 'Jenjang' && $proj['target_ak'] == 0 && $proj['next_target_name'] === 'Maksimal')
            <span class="fw-medium text-success">Maksimal</span>
        @else
            <span class="fw-medium">{{ number_format($proj['progress_percentage'], 1, ',', '.') }}%</span>
        @endif
    </div>
    <div class="progress" style="height: 10px;">
        @if($type === 'Jenjang' && $proj['target_ak'] == 0 && $proj['next_target_name'] === 'Maksimal')
            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        @else
            <div class="progress-bar {{ $proj['is_held_by_speedbump'] || $proj['is_held_by_ukom'] || ($proj['is_held_by_puncak'] ?? false) ? 'bg-warning' : ($proj['is_ready_mathematically'] ? 'bg-success' : 'bg-primary') }}"
                role="progressbar" style="width: {{ min($proj['progress_percentage'], 100) }}%"
                aria-valuenow="{{ min($proj['progress_percentage'], 100) }}" aria-valuemin="0"
                aria-valuemax="100"></div>
        @endif
    </div>
    
    @if($proj['surplus_ak'] > 0)
    <div class="mt-2 text-end">
        <span class="badge bg-success-subtle text-success border border-success-subtle">
            +{{ number_format($proj['surplus_ak'], 2, ',', '.') }} Surplus AK
        </span>
    </div>
    @endif
    
    @if(isset($proj['discarded_ak']) && $proj['discarded_ak'] > 0)
    <div class="mt-3 alert alert-warning border-warning-subtle py-2 px-3 d-flex align-items-center gap-2 mb-0" style="background-color: #fffbeb; color: #b45309;">
        <i data-feather="alert-circle" width="16" height="16" class="flex-shrink-0" style="color: #d97706;"></i>
        <span class="small">
            Terdapat nilai <strong style="color: #92400e;">{{ number_format($proj['discarded_ak'], 3, ',', '.') }} AK</strong> dari riwayat lama yang tidak diakumulasikan karena diperoleh sebelum 
            TMT Jabatan ({{ $proj['tmt_used'] }}).
        </span>
    </div>
    @endif
</div>

@php
    $boxYear = $proj['projected_year'];
    $boxYearsNeeded = $proj['estimated_years'];
    
    $isFast = $boxYearsNeeded <= 3;
    $alertClass = $isFast ? 'success' : 'primary';
    $icon = $isFast ? 'zap' : 'calendar';
@endphp

@if($type === 'Jenjang' && $proj['target_ak'] == 0 && $proj['next_target_name'] === 'Maksimal')
    <div class="estimation-alert-box success mb-2">
        <div class="icon-wrapper">
            <i data-feather="award" width="24" height="24"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="mb-1 text-dark fw-bold">Jenjang Puncak Kategori</h5>
                <span class="badge bg-white text-dark border shadow-sm">Target: {{ $proj['current_target_name'] }}</span>
            </div>
            <div class="text-dark opacity-75 mt-1" style="font-size: 0.9rem; line-height: 1.5;">
                ✨ Pegawai telah mencapai jenjang maksimal dalam kategori jabatannya saat ini. Tidak ada target kenaikan jenjang reguler selanjutnya.
            </div>
        </div>
    </div>
@elseif($proj['is_fully_ready'])
    <div class="estimation-alert-box success mb-2">
        <div class="icon-wrapper">
            <i data-feather="check-circle" width="24" height="24"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="mb-1 text-dark fw-bold">Siap untuk Kenaikan {{ $type }}!</h5>
                <span class="badge bg-white text-dark border shadow-sm">Target: {{ $proj['current_target_name'] }} <i data-feather="arrow-right" class="mx-1" width="10" height="10"></i> {{ $proj['next_target_name'] }}</span>
            </div>
            <div class="text-dark opacity-75 mt-1" style="font-size: 0.9rem; line-height: 1.5;">
                ✨ Target Angka Kredit telah tercapai, dan syarat masa jabatan telah terpenuhi.
            </div>
        </div>
    </div>
@elseif($proj['is_held_by_puncak'] ?? false)
    <div class="estimation-alert-box warning mb-2">
        <div class="icon-wrapper bg-warning text-dark">
            <i data-feather="lock" width="24" height="24"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <h5 class="mb-1 text-dark fw-bold">Kenaikan Pangkat Terkunci</h5>
                <span class="badge bg-white text-dark border shadow-sm">Target: {{ $proj['current_target_name'] }} <i data-feather="arrow-right" class="mx-1" width="10" height="10"></i> {{ $proj['next_target_name'] }}</span>
            </div>
            <div class="text-dark opacity-75 mt-1" style="font-size: 0.9rem; line-height: 1.5;">
                Target AK kenaikan pangkat telah tercapai. Namun pegawai berada di pangkat puncak pada jenjangnya saat ini ({{ $pegawai->jabatan->jenjang ?? '-' }}). Pegawai wajib diusulkan dan lulus <strong>Kenaikan Jenjang</strong> terlebih dahulu sebelum pangkat dapat diproses.
            </div>
        </div>
    </div>
@elseif($proj['is_held_by_ukom'])
    <div class="estimation-alert-box warning mb-2">
        <div class="icon-wrapper bg-warning text-dark">
            <i data-feather="alert-triangle" width="24" height="24"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="mb-1 text-dark fw-bold">Menunggu Uji Kompetensi</h5>
                <span class="badge bg-white text-dark border shadow-sm">Target: {{ $proj['current_target_name'] }} <i data-feather="arrow-right" class="mx-1" width="10" height="10"></i> {{ $proj['next_target_name'] }}</span>
            </div>
            <div class="text-dark opacity-75 mt-1" style="font-size: 0.9rem; line-height: 1.5;">
                Target AK sudah tercapai, namun pegawai belum dinyatakan lulus Uji Kompetensi (Ukom) untuk kenaikan jenjang.
            </div>
        </div>
    </div>
@else
    <div class="estimation-alert-box {{ $alertClass }} mb-2">
        <div class="icon-wrapper">
            <i data-feather="{{ $icon }}" width="24" height="24"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <h5 class="mb-1 text-dark fw-bold">Estimasi Kenaikan: {{ $proj['projected_period_label'] }}</h5>
                <span class="badge bg-white text-dark border shadow-sm">Target: {{ $proj['current_target_name'] }} <i data-feather="arrow-right" class="mx-1" width="10" height="10"></i> {{ $proj['next_target_name'] }}</span>
            </div>
            <div class="text-dark opacity-75 mt-1" style="font-size: 0.9rem; line-height: 1.5;">
                ✨ Dengan mempertahankan kinerja <strong class="fw-bold">{{ $proj['predikat_label'] }}</strong>, target diperkirakan akan tercapai dalam <strong>{{ $proj['estimated_time_text'] }}</strong> dari sekarang.
                @if($proj['is_held_by_speedbump'])
                <br><span class="text-danger fw-medium">Catatan: Terkena aturan minimal masa jabatan ({{ $proj['years_served'] }} tahun dijalani dari syarat 2 tahun).</span>
                @endif
            </div>
        </div>
    </div>
@endif

@if($proj['is_fully_ready'] || $proj['is_held_by_ukom'])
    <div class="mt-3 pt-3 border-top text-end">
        @if($pegawai->activeUsulan)
            @if($pegawai->activeUsulan->status === 'draft')
                <a href="{{ route('usulan-pangkat.index', ['tab' => 'draft']) }}" class="btn btn-info text-white shadow-sm px-4">
                    <i data-feather="edit-3" width="16" height="16" class="me-1"></i> Lanjutkan Draf Tertunda
                </a>
            @else
                <button class="btn btn-secondary shadow-sm px-4" disabled>
                    <i data-feather="loader" width="16" height="16" class="me-1"></i> SK Sedang Diproses
                </button>
            @endif
        @elseif($proj['is_sedang_hukuman'])
            <button class="btn btn-danger px-4" disabled>
                <i data-feather="slash" width="16" height="16" class="me-1"></i> Terblokir Hukuman Disiplin
            </button>
        @else
            <button class="btn btn-primary shadow px-4 py-2" data-bs-toggle="modal" data-bs-target="#usulanModal" 
                data-type="{{ $type }}"
                data-current="{{ $proj['current_target_name'] }}"
                data-next="{{ $proj['next_target_name'] }}"
                data-ak="{{ $proj['current_ak'] }}"
                data-target-ak="{{ $proj['target_ak'] }}"
                data-surplus="{{ $proj['surplus_ak'] }}"
                data-golongan-baru="{{ $proj['next_golongan_id'] ?? '' }}"
                data-is-pangkat-puncak="{{ $proj['is_pangkat_puncak'] ? '1' : '0' }}">
                <i data-feather="upload-cloud" width="18" height="18" class="me-1"></i>
                Usulkan Kenaikan {{ ucfirst($type) }}
            </button>
        @endif
    </div>
@endif
