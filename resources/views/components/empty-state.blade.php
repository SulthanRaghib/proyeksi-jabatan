@props([
    'icon' => 'inbox',
    'title' => 'Belum ada data',
    'description' => '',
    'actionUrl' => null,
    'actionText' => null,
    'size' => 'normal', // normal, small, large
])

@php
    $sizeClasses = [
        'small' => 'py-2',
        'normal' => 'py-4',
        'large' => 'py-5',
    ];
    $iconSizes = [
        'small' => ['width' => '24', 'height' => '24', 'container' => '60px'],
        'normal' => ['width' => '36', 'height' => '36', 'container' => '80px'],
        'large' => ['width' => '48', 'height' => '48', 'container' => '100px'],
    ];
    
    $paddingClass = $sizeClasses[$size] ?? $sizeClasses['normal'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['normal'];
@endphp

<div class="empty-state {{ $paddingClass }}">
    <div class="empty-state-icon" style="width: {{ $iconSize['container'] }}; height: {{ $iconSize['container'] }};">
        <i data-feather="{{ $icon }}" width="{{ $iconSize['width'] }}" height="{{ $iconSize['height'] }}"></i>
    </div>
    <h5 class="empty-state-title">{{ $title }}</h5>
    @if($description)
        <p class="empty-state-description">{{ $description }}</p>
    @endif
    @if($actionUrl && $actionText)
        <a href="{{ $actionUrl }}" class="btn btn-primary">
            <i data-feather="plus" class="feather-icon me-1"></i>
            {{ $actionText }}
        </a>
    @endif
    {{ $slot }}
</div>

@once
    @push('scripts')
        <script>
            // Re-render feather icons for dynamically loaded empty states
            if (typeof feather !== 'undefined') {
                const observer = new MutationObserver(() => {
                    feather.replace();
                });
                observer.observe(document.body, { childList: true, subtree: true });
            }
        </script>
    @endpush
@endonce
