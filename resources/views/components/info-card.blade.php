@props([
    'title' => '',
    'items' => [],
    'class' => '',
])

<div class="card shadow-sm border-0 h-100 {{ $class }}">
    <div class="card-body">
        @if($title)
            <h4 class="card-title mb-4">{{ $title }}</h4>
        @endif

        @if(!empty($items))
            @foreach($items as $item)
                <div class="info-card-item">
                    <small class="info-card-label">{{ $item['label'] }}</small>
                    <div class="info-card-value">
                        {!! $item['value'] !!}
                    </div>
                </div>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </div>
</div>
