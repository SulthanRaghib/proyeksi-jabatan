@props([
    'title',
    'value',
    'icon' => 'trending-up',
    'color' => 'primary',
])

@php
    $colorClasses = [
        'primary' => 'bg-primary-subtle text-primary',
        'success' => 'bg-success-subtle text-success',
        'warning' => 'bg-warning-subtle text-warning',
        'danger' => 'bg-danger-subtle text-danger',
        'info' => 'bg-info-subtle text-info',
        'secondary' => 'bg-secondary-subtle text-secondary',
    ];
    $iconColorClass = $colorClasses[$color] ?? $colorClasses['primary'];
@endphp

<div class="stat-card">
    <div class="stat-icon {{ $iconColorClass }}">
        <i data-feather="{{ $icon }}" width="24" height="24"></i>
    </div>
    <div>
        <p class="text-muted mb-0 small fw-medium text-uppercase letter-spacing-1">{{ $title }}</p>
        <h3 class="mb-0 text-dark fw-bolder">{{ $value }}</h3>
    </div>
    <i data-feather="{{ $icon }}" width="100" height="100" class="stat-bg-icon text-{{ $color }}"></i>
</div>
