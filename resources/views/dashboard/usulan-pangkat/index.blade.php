@extends('layouts.dashboard')

@section('title', 'Daftar Usulan Kenaikan Pangkat')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Manajemen Usulan Kenaikan Pangkat</h1>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="fw-semibold">NIP / Nama Pegawai</th>
                            <th class="fw-semibold text-center">Usulan Pangkat</th>
                            <th class="fw-semibold text-center">Status</th>
                            <th class="fw-semibold text-end">Sisa Saldo Baru</th>
                            <th class="fw-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usulans as $usulan)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $usulan->pegawai->nama_lengkap }}</div>
                                    <div class="small text-muted">{{ $usulan->pegawai->nip }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $usulan->golonganLama->nama_golongan ?? '-' }}</span>
                                    <i data-feather="arrow-right" class="mx-1 text-muted" width="14" height="14"></i>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $usulan->golonganBaru->nama_golongan ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($usulan->status === 'sedang_diproses')
                                        <span class="badge bg-warning text-dark"><i data-feather="loader" width="12" height="12" class="me-1"></i> Sedang Diproses</span>
                                    @elseif($usulan->status === 'selesai')
                                        <span class="badge bg-success"><i data-feather="check-circle" width="12" height="12" class="me-1"></i> Selesai</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($usulan->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">
                                    +{{ number_format($usulan->sisa_ak, 2, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- TODO: add view document links if needed -->
                                        @if($usulan->status === 'sedang_diproses')
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
                            
                            @if($usulan->status === 'sedang_diproses')
                                <!-- Approve Modal -->
                                <div class="modal fade" id="approveModal{{ $usulan->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form action="{{ route('usulan-pangkat.approve', $usulan) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-dark">Persetujuan SK Kenaikan Pangkat</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <div class="alert alert-info border-0 mb-4">
                                                        <i data-feather="info" width="16" height="16" class="me-1"></i> 
                                                        Menyetujui usulan ini akan memotong AK pegawai sebesar <strong>{{ number_format($usulan->potongan_ak, 2, ',', '.') }}</strong> dan memperbarui golongan pegawai menjadi <strong>{{ $usulan->golonganBaru->nama_golongan }}</strong>.
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">Nomor SK Baru <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="nomor_sk_baru" required placeholder="Contoh: 821.2/KEP/123/2026">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium">TMT Golongan Baru <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="tmt_golongan_baru" required value="{{ date('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 bg-light">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Setujui & Terbitkan SK</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i data-feather="inbox" width="40" height="40" class="mb-3 opacity-50"></i>
                                        <p class="mb-0">Belum ada usulan kenaikan pangkat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $usulans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
