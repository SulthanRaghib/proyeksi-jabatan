{{--
    Reusable Flash Alert Component
    Automatically renders session('success') and session('error') messages.
    Usage: <x-alert-flash />
--}}

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0" role="alert" style="border-left: 4px solid #198754 !important;">
        <i data-feather="check-circle" width="18" height="18" class="flex-shrink-0"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0" role="alert" style="border-left: 4px solid #dc3545 !important;">
        <i data-feather="alert-triangle" width="18" height="18" class="flex-shrink-0"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 4px solid #dc3545 !important;">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i data-feather="alert-circle" width="18" height="18" class="flex-shrink-0"></i>
            <strong>Terdapat kesalahan pada input Anda:</strong>
        </div>
        <ul class="mb-0 ps-4">
            @foreach ($errors->all() as $error)
                <li class="small">{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
