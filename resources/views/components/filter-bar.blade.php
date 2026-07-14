{{--
    Reusable Filter/Search Bar Component
    Usage:
        <x-filter-bar :action="route('pegawais.index')" :searchValue="$search" placeholder="Cari nama atau NIP..." />
        
        With extra filters (slot):
        <x-filter-bar :action="route('pegawais.index')" :searchValue="$search">
            <div class="col-12 col-md-3">
                <select name="status" class="form-select custom-input">...</select>
            </div>
        </x-filter-bar>
--}}

@props([
    'action',
    'searchValue' => '',
    'placeholder' => 'Cari data...',
    'searchName' => 'q',
])

<div class="filter-card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ $action }}" class="row g-2 align-items-center">
            <div class="col-12 {{ $slot->isEmpty() ? 'col-md-9' : 'col-md-5' }}">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                        <i data-feather="search" width="16" height="16"></i>
                    </span>
                    <input type="text" name="{{ $searchName }}" value="{{ $searchValue }}" 
                        class="form-control custom-input border-start-0 ps-0"
                        placeholder="{{ $placeholder }}">
                </div>
            </div>
            
            {{ $slot }}
            
            <div class="col-12 col-md-{{ $slot->isEmpty() ? '3' : '2' }} d-grid">
                <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>
