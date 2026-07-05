{{-- 
    Reusable Action Button Component
    Usage:
        <x-action-button type="edit" :href="route('xxx.edit', $model)" />
        <x-action-button type="delete" />
        <x-action-button type="view" :href="route('xxx.show', $model)" />
--}}

@props([
    'type' => 'edit',
    'href' => '#',
    'title' => '',
    'size' => 'sm',
    'action' => '',
    'message' => 'Apakah Anda yakin ingin melanjutkan aksi ini?',
])

@php
    $sizeClass = $size === 'lg' ? 'action-btn-lg' : '';

    $config = match($type) {
        'edit' => [
            'class' => 'action-btn--edit',
            'icon' => 'edit-2',
            'title' => 'Edit',
        ],
        'delete' => [
            'class' => 'action-btn--delete',
            'icon' => 'trash-2',
            'title' => 'Hapus',
        ],
        'delete_modal' => [
            'class' => 'action-btn--delete',
            'icon' => 'trash-2',
            'title' => 'Hapus',
        ],
        'view' => [
            'class' => 'action-btn--view',
            'icon' => 'eye',
            'title' => 'Lihat',
        ],
        default => [
            'class' => 'action-btn--default',
            'icon' => 'more-horizontal',
            'title' => 'Aksi',
        ],
    };

    $finalTitle = $title ?: $config['title'];
@endphp

@if($type === 'delete')
    <button type="submit" {{ $attributes->merge(['class' => 'action-btn ' . $config['class'] . ' ' . $sizeClass]) }} title="{{ $finalTitle }}">
        <i data-feather="{{ $config['icon'] }}" width="14" height="14"></i>
    </button>
@elseif($type === 'delete_modal')
    <button type="button" {{ $attributes->merge(['class' => 'action-btn ' . $config['class'] . ' ' . $sizeClass]) }} 
        title="{{ $finalTitle }}"
        data-bs-toggle="modal" 
        data-bs-target="#globalConfirmModal"
        data-bs-action="{{ $action }}"
        data-bs-message="{{ $message }}">
        <i data-feather="{{ $config['icon'] }}" width="14" height="14"></i>
    </button>
@else
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'action-btn ' . $config['class'] . ' ' . $sizeClass]) }} title="{{ $finalTitle }}">
        <i data-feather="{{ $config['icon'] }}" width="14" height="14"></i>
    </a>
@endif
