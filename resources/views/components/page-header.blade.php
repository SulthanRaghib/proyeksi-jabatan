{{--
    Reusable Page Header Component
    Standardizes page title, breadcrumb, and action button across ALL pages.
    
    Usage:
        <x-page-header title="Data Pegawai" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Pegawai'],
        ]">
            <x-slot:action>
                <a href="..." class="btn btn-primary">
                    <i data-feather="plus" class="feather-icon me-1"></i> Tambah
                </a>
            </x-slot:action>
        </x-page-header>
--}}

@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
    'hasAction' => false,
])

<div class="page-breadcrumb">
    <div class="row align-items-center">
        <div class="col-12 {{ ($hasAction || isset($action)) ? 'col-md-6' : '' }}">
            <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-muted mb-1 small">{{ $subtitle }}</p>
            @endif
            
            @if(!empty($breadcrumbs))
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 p-0">
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['label'] }}</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] }}" class="text-muted">{{ $breadcrumb['label'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endif
        </div>
        
        @if(isset($action))
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                {{ $action }}
            </div>
        @endif
    </div>
</div>
