@props([
    'title',
    'breadcrumbs' => [],
    'hasAction' => false,
])

<div class="page-breadcrumb">
    <div class="row align-items-center">
        <div class="col-12 {{ $hasAction ? 'col-md-6' : '' }}">
            <h3 class="page-title text-dark font-weight-medium mb-1">{{ $title }}</h3>
            
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
        
        @if($hasAction)
            <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end">
                {{ $action ?? '' }}
            </div>
        @endif
    </div>
</div>
