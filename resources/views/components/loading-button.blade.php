@props([
    'type' => 'submit',
    'color' => 'primary',
    'size' => '',
    'loading' => false,
    'disabled' => false,
    'loadingText' => 'Memproses...',
])

@php
    $sizeClass = $size ? 'btn-' . $size : '';
    $isDisabled = $disabled || $loading;
@endphp

<button 
    type="{{ $type }}" 
    class="btn btn-{{ $color }} {{ $sizeClass }} {{ $loading ? 'btn-loading' : '' }}"
    {{ $isDisabled ? 'disabled' : '' }}
    {{ $attributes }}
>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        {{ $loadingText }}
    @else
        {{ $slot }}
    @endif
</button>
