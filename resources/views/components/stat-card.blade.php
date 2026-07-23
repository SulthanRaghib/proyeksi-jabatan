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
        <i data-feather="{{ $icon }}" width="22" height="22"></i>
    </div>
    <div>
        <p class="text-muted mb-1 small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">{{ $title }}</p>
        <h3 class="mb-0 text-dark fw-extrabold" style="font-size: 1.45rem; font-weight: 800;">{{ $value }}</h3>
    </div>
    <i data-feather="{{ $icon }}" width="80" height="80" class="stat-bg-icon"></i>
</div>
