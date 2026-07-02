@props([
    'size' => 'normal', // small, normal, large
    'color' => 'primary',
    'text' => '',
])

@php
    $sizeClasses = [
        'small' => 'spinner-border-sm',
        'normal' => '',
        'large' => '',
    ];
    $sizeStyle = [
        'small' => '',
        'normal' => 'width: 2rem; height: 2rem;',
        'large' => 'width: 3rem; height: 3rem;',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['normal'];
    $style = $sizeStyle[$size] ?? $sizeStyle['normal'];
@endphp

<div class="text-center {{ $attributes->get('class') }}">
    <div class="spinner-border text-{{ $color }} {{ $sizeClass }}" role="status" style="{{ $style }}">
        <span class="visually-hidden">Loading...</span>
    </div>
    @if($text)
        <p class="mt-3 text-muted mb-0">{{ $text }}</p>
    @endif
</div>
