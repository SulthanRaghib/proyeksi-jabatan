@props([
    'variant' => 'primary', // primary, secondary, soft, outline, danger, success, warning
    'size' => 'md',         // sm, md, lg
    'icon' => null,         // feather icon name
    'href' => null,         // if set, renders as <a> instead of <button>
    'type' => 'button',     // button type if not link
])

@php
    $baseClass = 'btn btn-modern d-inline-flex align-items-center justify-content-center gap-2 fw-medium rounded-pill transition-all';
    
    $sizeClass = match($size) {
        'sm' => 'btn-sm px-3 py-1.5 fs-7',
        'lg' => 'btn-lg px-4 py-2.5',
        default => 'px-4 py-2', // md
    };

    $variantClass = match($variant) {
        'primary' => 'btn-primary shadow-sm',
        'secondary' => 'btn-secondary shadow-sm',
        'danger' => 'btn-danger shadow-sm',
        'success' => 'btn-success shadow-sm',
        'warning' => 'btn-warning shadow-sm',
        'soft' => 'btn-light border-0 bg-secondary-subtle text-dark hover-soft',
        'outline' => 'btn-outline-secondary bg-white shadow-sm',
        default => 'btn-primary shadow-sm',
    };

    $classes = "{$baseClass} {$sizeClass} {$variantClass}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-feather="{{ $icon }}" width="{{ $size === 'sm' ? '14' : '18' }}" height="{{ $size === 'sm' ? '14' : '18' }}"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i data-feather="{{ $icon }}" width="{{ $size === 'sm' ? '14' : '18' }}" height="{{ $size === 'sm' ? '14' : '18' }}"></i>
        @endif
        {{ $slot }}
    </button>
@endif
