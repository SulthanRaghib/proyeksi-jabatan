{{--
    Reusable Data Table Component
    Wraps table-card, table-responsive, modern-table, empty-state, and pagination footer.

    Usage:
        <x-data-table :headers="['No', 'Nama', 'Aksi']" :paginator="$pegawais"
            emptyIcon="users" emptyTitle="Belum ada data pegawai" 
            emptyDescription="Tambahkan data pegawai untuk memulai.">
            @foreach($pegawais as $item)
                <tr>...</tr>
            @endforeach
        </x-data-table>
--}}

@props([
    'headers' => [],
    'paginator' => null,
    'emptyIcon' => 'inbox',
    'emptyTitle' => 'Belum ada data',
    'emptyDescription' => '',
    'emptyActionUrl' => null,
    'emptyActionText' => null,
    'isEmpty' => false,
])

<div class="table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table modern-table table-hover mb-0">
                @if(!empty($headers))
                    <thead>
                        <tr>
                            @foreach($headers as $header)
                                @if(is_array($header))
                                    <th {!! $header['attrs'] ?? '' !!}>{{ $header['label'] }}</th>
                                @else
                                    <th>{{ $header }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                @endif
                <tbody>
                    @if($isEmpty)
                        <tr>
                            <td colspan="{{ count($headers) }}">
                                <x-empty-state 
                                    :icon="$emptyIcon" 
                                    :title="$emptyTitle" 
                                    :description="$emptyDescription"
                                    :actionUrl="$emptyActionUrl"
                                    :actionText="$emptyActionText" />
                            </td>
                        </tr>
                    @else
                        {{ $slot }}
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    @if($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
        <div class="card-footer bg-white border-top p-3">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
