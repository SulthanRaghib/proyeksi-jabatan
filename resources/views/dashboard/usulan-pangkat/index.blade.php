@extends('layouts.dashboard')

@section('title', 'Daftar Usulan Kenaikan Pangkat & Jenjang')

@section('content')
    <x-page-header title="Manajemen Usulan Kenaikan Pangkat & Jenjang" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Usulan Pangkat & Jenjang'],
    ]" />

    <div class="container-fluid">
        <x-alert-flash />

        <!-- Tabs -->
        @php $currentTab = request('tab', 'sedang_diproses'); @endphp
        <ul class="nav nav-tabs mb-4 border-bottom-0 gap-2" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{ route('usulan-pangkat.index', ['tab' => 'sedang_diproses']) }}" class="nav-link {{ $currentTab === 'sedang_diproses' ? 'active fw-bold shadow-sm border-bottom-0 rounded-top' : 'text-muted bg-light border-0' }}">
                    <i data-feather="loader" width="16" height="16" class="me-1"></i> Sedang Diproses
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('usulan-pangkat.index', ['tab' => 'draft']) }}" class="nav-link {{ $currentTab === 'draft' ? 'active fw-bold shadow-sm border-bottom-0 rounded-top' : 'text-muted bg-light border-0' }}">
                    <i data-feather="edit-3" width="16" height="16" class="me-1"></i> Draf Tertunda
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('usulan-pangkat.index', ['tab' => 'selesai']) }}" class="nav-link {{ $currentTab === 'selesai' ? 'active fw-bold shadow-sm border-bottom-0 rounded-top' : 'text-muted bg-light border-0' }}">
                    <i data-feather="check-circle" width="16" height="16" class="me-1"></i> Selesai
                </a>
            </li>
            <li class="nav-item ms-auto" role="presentation">
                <a href="{{ route('usulan-pangkat.index', ['tab' => 'semua']) }}" class="nav-link {{ $currentTab === 'semua' ? 'active fw-bold shadow-sm border-bottom-0 rounded-top' : 'text-muted bg-light border-0' }}">
                    Semua
                </a>
            </li>
        </ul>

        @php
            $tableHeaders = [
                'NIP / Nama Pegawai', 
                ['label' => 'Target Kenaikan', 'attrs' => 'class="text-center"'], 
                ['label' => 'Status', 'attrs' => 'class="text-center"'], 
                ['label' => 'Sisa Saldo Baru', 'attrs' => 'class="text-end"'], 
                ['label' => 'Aksi', 'attrs' => 'class="text-center"']
            ];
        @endphp

        <x-data-table
            :headers="$tableHeaders"
            :paginator="$usulans"
            :isEmpty="$usulans->isEmpty()"
            emptyIcon="inbox"
            emptyTitle="Tidak ada usulan ditemukan"
            emptyDescription="Belum ada usulan kenaikan pangkat atau jenjang pada tab ini.">
            @foreach($usulans as $usulan)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $usulan->pegawai->nama_lengkap }}</div>
                        <div class="small text-muted">{{ $usulan->pegawai->nip }}</div>
                    </td>
                    <td class="text-center">
                        @if($usulan->is_lintas_jenjang)
                            @php
                                if ($usulan->status === 'selesai') {
                                    $targetJabatan = $usulan->pegawai->jabatan;
                                    $oldJabatan = $targetJabatan ? \App\Models\Jabatan::where('kategori', $targetJabatan->kategori)
                                        ->where('id', '<', $targetJabatan->id)
                                        ->orderBy('id', 'desc')
                                        ->first() : null;
                                    $oldJenjang = $oldJabatan ? $oldJabatan->jenjang : '-';
                                    $targetJenjang = $targetJabatan ? $targetJabatan->jenjang : '-';
                                } else {
                                    $oldJenjang = $usulan->pegawai->jabatan->jenjang ?? '-';
                                    $nextJabatan = $usulan->pegawai->jabatan ? \App\Models\Jabatan::where('kategori', $usulan->pegawai->jabatan->kategori)
                                        ->where('id', '>', $usulan->pegawai->jabatan_id)
                                        ->orderBy('id')
                                        ->first() : null;
                                    $targetJenjang = $nextJabatan ? $nextJabatan->jenjang : 'Maksimal';
                                }
                            @endphp
                            <span class="badge bg-light text-dark border">{{ $oldJenjang }}</span>
                            <i data-feather="arrow-right" class="mx-1 text-muted" width="14" height="14"></i>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $targetJenjang }}</span>
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">Kenaikan Jenjang</div>
                        @else
                            <span class="badge bg-light text-dark border">{{ $usulan->golonganLama->nama_golongan ?? '-' }}</span>
                            <i data-feather="arrow-right" class="mx-1 text-muted" width="14" height="14"></i>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $usulan->golonganBaru->nama_golongan ?? '-' }}</span>
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">Kenaikan Pangkat</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(in_array($usulan->status, ['sedang_diproses', 'PROSES_KP_REGULER', 'PROSES_KENAIKAN_JENJANG']))
                            <span class="badge bg-warning text-dark"><i data-feather="loader" width="12" height="12" class="me-1"></i> Sedang Diproses</span>
                        @elseif($usulan->status === 'selesai')
                            <span class="badge bg-success"><i data-feather="check-circle" width="12" height="12" class="me-1"></i> Selesai</span>
                        @elseif($usulan->status === 'draft')
                            <span class="badge bg-secondary"><i data-feather="edit-3" width="12" height="12" class="me-1"></i> Draf</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $usulan->status)) }}</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold text-success">
                        +{{ number_format($usulan->sisa_ak, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            @if($usulan->status === 'draft')
                                <button class="btn btn-sm btn-info text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $usulan->id }}">
                                    <i data-feather="upload-cloud" width="14" height="14" class="me-1"></i> Lengkapi Dokumen
                                </button>
                            @elseif(in_array($usulan->status, ['sedang_diproses', 'PROSES_KP_REGULER', 'PROSES_KENAIKAN_JENJANG']))
                                <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#approveModal{{ $usulan->id }}">
                                    <i data-feather="check-square" width="14" height="14" class="me-1"></i> Proses SK
                                </button>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    Selesai
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                
                @if($usulan->status === 'draft')
                    <!-- Upload Dokumen Modal -->
                    <div class="modal fade" id="uploadModal{{ $usulan->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow">
                                <form action="{{ route('usulan-pangkat.update', $usulan) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark">Lengkapi Dokumen Usulan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-4">
                                        <div class="alert alert-info border-0 mb-4">
                                            <i data-feather="info" width="16" height="16" class="me-1"></i> 
                                            Silakan unggah dokumen pendukung (PDF) untuk melanjutkan usulan <strong>{{ $usulan->pegawai->nama_lengkap }}</strong> ke tahap pemrosesan.
                                        </div>
                                        
                                        <div class="row g-3 text-start">
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
                                            
                                            @if($usulan->is_lintas_jenjang)
                                                <div class="col-12"><hr class="my-1 border-secondary-subtle border-dashed"></div>
                                                <div class="col-12"><span class="badge bg-warning-subtle text-warning-emphasis">Usulan Lintas Jenjang</span></div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium">Sertifikat Lulus Ukom <span class="text-danger">*</span></label>
                                                    <input class="form-control form-control-sm" type="file" name="ukom" accept="application/pdf" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-medium">Surat Formasi BAPETEN <span class="text-danger">*</span></label>
                                                    <input class="form-control form-control-sm" type="file" name="formasi" accept="application/pdf" required>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 bg-light">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Kirim Usulan & Validasi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif(in_array($usulan->status, ['sedang_diproses', 'PROSES_KP_REGULER', 'PROSES_KENAIKAN_JENJANG']))
                    @php
                        $nextJabatan = null;
                        if ($usulan->is_lintas_jenjang) {
                            $nextJabatan = \App\Models\Jabatan::where('kategori', $usulan->pegawai->jabatan->kategori)
                                ->where('id', '>', $usulan->pegawai->jabatan_id)
                                ->orderBy('id')
                                ->first();
                        }
                        
                        $isLintasJenjangPangkat = false;
                        if (!$usulan->is_lintas_jenjang && $usulan->pegawai) {
                            $projectionService = app(\App\Services\ProjectionService::class);
                            $pangkats = $projectionService->getPangkatListForJenjang(
                                $usulan->pegawai->jabatan->jenjang ?? '',
                                $usulan->pegawai->jabatan->kategori ?? ''
                            );
                            $isLintasJenjangPangkat = $usulan->golonganLama && !in_array($usulan->golonganLama->nama_golongan, $pangkats);
                        }
                    @endphp
                    <!-- Approve Modal -->
                    <div class="modal fade" id="approveModal{{ $usulan->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow">
                                <form action="{{ route('usulan-pangkat.approve', $usulan) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark">
                                            @if($usulan->is_lintas_jenjang)
                                                Persetujuan SK Kenaikan Jenjang Jabatan
                                            @else
                                                Persetujuan SK Kenaikan Pangkat
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-4 text-start">
                                        <div class="alert alert-info border-0 mb-4">
                                            <i data-feather="info" width="16" height="16" class="me-1"></i> 
                                            @if($usulan->is_lintas_jenjang)
                                                Menyetujui usulan ini akan memperbarui jenjang jabatan pegawai menjadi <strong>{{ $nextJabatan ? $nextJabatan->jenjang : 'Maksimal' }}</strong>. Sesuai regulasi BAPETEN, Angka Kredit (AK) pegawai <strong>dipertahankan sepenuhnya</strong> (tidak ada pemotongan) dan golongan tetap <strong>{{ $usulan->golonganLama->nama_golongan }}</strong>.
                                            @elseif($isLintasJenjangPangkat)
                                                Menyetujui usulan ini akan memperbarui golongan pegawai menjadi <strong>{{ $usulan->golonganBaru->nama_golongan }}</strong>. Karena ini merupakan kenaikan pangkat lintas-jenjang pertama, Angka Kredit (AK) pegawai akan <strong>direset menjadi 0,00</strong> (memotong <strong>{{ number_format($usulan->potongan_ak, 2, ',', '.') }}</strong> AK saat ini).
                                            @else
                                                Menyetujui usulan ini akan memperbarui golongan pegawai menjadi <strong>{{ $usulan->golonganBaru->nama_golongan }}</strong>. Angka Kredit (AK) pegawai <strong>dipertahankan sepenuhnya</strong> (tidak ada pemotongan) dan akumulasi berjalan tetap berlanjut.
                                            @endif
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">
                                                    @if($usulan->is_lintas_jenjang)
                                                        Nomor SK Kenaikan Jenjang Jabatan Baru <span class="text-danger">*</span>
                                                    @else
                                                        Nomor SK Baru <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" name="nomor_sk_baru" id="nomor_sk_{{ $usulan->id }}" required placeholder="Contoh: Kpts-001/B.2/KP.01.01/07/2026">
                                                    <button class="btn btn-outline-primary btn-sm" type="button" onclick="generateNoSk('{{ $usulan->id }}')">
                                                        <i data-feather="refresh-cw" width="12" height="12" class="me-1"></i> Generate
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">
                                                    @if($usulan->is_lintas_jenjang)
                                                        TMT Jabatan Baru <span class="text-danger">*</span>
                                                    @else
                                                        TMT Golongan Baru <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="date" class="form-control form-control-sm" name="tmt_golongan_baru" required value="{{ date('Y-m-d') }}">
                                            </div>

                                            @if($usulan->is_lintas_jenjang)
                                                <div class="col-12"><hr class="my-1 border-secondary-subtle border-dashed"></div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium">Nomor Sertifikat Kelulusan Ukom <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control form-control-sm" name="no_sertifikat_ukom" id="nomor_ukom_{{ $usulan->id }}" required placeholder="Contoh: Cert-Ukom/BAPETEN/07/2026/001">
                                                        <button class="btn btn-outline-primary btn-sm" type="button" onclick="generateNoUkom('{{ $usulan->id }}')">
                                                            <i data-feather="refresh-cw" width="12" height="12" class="me-1"></i> Generate
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-medium">Tanggal Kelulusan Ukom <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control form-control-sm" name="tgl_lulus_ukom" required value="{{ date('Y-m-d') }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 bg-light">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Setujui & Terbitkan SK</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </x-data-table>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    function generateNoSk(id) {
        const input = document.getElementById('nomor_sk_' + id);
        if (!input) return;
        
        input.value = 'Mencari...';
        input.disabled = true;

        fetch('{{ route('api.generate-no-sk') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.value = data.nomor_sk;
                } else {
                    input.value = '';
                    alert('Gagal menghasilkan nomor SK.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                input.value = '';
                alert('Terjadi kesalahan koneksi.');
            })
            .finally(() => {
                input.disabled = false;
            });
    }

    function generateNoUkom(id) {
        const input = document.getElementById('nomor_ukom_' + id);
        if (!input) return;
        
        input.value = 'Mencari...';
        input.disabled = true;

        fetch('{{ route('api.generate-no-ukom') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.value = data.nomor_ukom;
                } else {
                    input.value = '';
                    alert('Gagal menghasilkan nomor sertifikat.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                input.value = '';
                alert('Terjadi kesalahan koneksi.');
            })
            .finally(() => {
                input.disabled = false;
            });
    }
</script>
@endpush
