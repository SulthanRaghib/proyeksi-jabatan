@props([
    'type' => 'text', // text, title, card, table, stat-card
    'rows' => 3,
    'columns' => 3,
])

@if($type === 'text')
    @for($i = 0; $i < $rows; $i++)
        <div class="skeleton skeleton-text" style="width: {{ 100 - ($i * 10) }}%;"></div>
    @endfor

@elseif($type === 'title')
    <div class="skeleton skeleton-title"></div>

@elseif($type === 'card')
    <div class="skeleton skeleton-card"></div>

@elseif($type === 'stat-card')
    <div class="row g-3">
        @for($i = 0; $i < 4; $i++)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="skeleton skeleton-text mb-2" style="width: 60%;"></div>
                        <div class="skeleton skeleton-title" style="width: 40%;"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

@elseif($type === 'table')
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    @for($i = 0; $i < $columns; $i++)
                        <th><div class="skeleton skeleton-text"></div></th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < $rows; $i++)
                    <tr>
                        @for($j = 0; $j < $columns; $j++)
                            <td><div class="skeleton skeleton-text"></div></td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

@elseif($type === 'list')
    @for($i = 0; $i < $rows; $i++)
        <div class="border rounded p-3 mb-3">
            <div class="skeleton skeleton-title mb-2"></div>
            <div class="skeleton skeleton-text" style="width: 80%;"></div>
            <div class="skeleton skeleton-text" style="width: 60%;"></div>
        </div>
    @endfor

@else
    {{-- Custom skeleton via slot --}}
    {{ $slot }}
@endif

