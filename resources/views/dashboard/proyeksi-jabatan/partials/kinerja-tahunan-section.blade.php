        {{-- Card 4: Kinerja Tahunan --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">Riwayat Kinerja Tahunan</h4>
                                <p class="text-muted small mb-0">Evaluasi historis riil setelah penetapan PAK terakhir</p>
                            </div>
                            <x-btn :href="route('kinerja-tahunans.create', ['pegawai_id' => $pegawai->id])" icon="plus" size="sm" type="primary">
                                Tambah Kinerja
                            </x-btn>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Predikat</th>
                                        <th>Koefisien Saat Itu</th>
                                        <th class="text-end">Angka Kredit Didapat</th>
                                        <th class="text-center no-print">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pegawai->kinerjaTahunans as $kinerja)
                                        <tr>
                                            <td class="fw-medium">
                                                @if($kinerja->pak)
                                                    {{ $kinerja->pak->periode_penilaian_label }}
                                                @else
                                                    {{ $kinerja->tahun }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge border {{ $predikatBadgeClasses[$kinerja->predikat] ?? 'bg-secondary' }} px-2 py-1">
                                                    {{ $predikatLabels[$kinerja->predikat] ?? $kinerja->predikat }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($kinerja->koefisien_saat_itu, 3, ',', '.') }}</td>
                                            <td class="text-end fw-medium text-success">+{{ number_format($kinerja->ak_didapat, 3, ',', '.') }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2 no-print">
                                                    <x-action-button type="delete_modal" 
                                                        action="{{ route('kinerja-tahunans.destroy', $kinerja) }}" 
                                                        message="Yakin ingin menghapus riwayat kinerja ini?" />
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <x-empty-state 
                                                    icon="calendar"
                                                    title="Belum ada riwayat Kinerja Tahunan"
                                                    description="Tambahkan data evaluasi kinerja jika ada yang dilakukan setelah penerbitan PAK terakhir"
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
