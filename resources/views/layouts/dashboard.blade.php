<!DOCTYPE html>
<html dir="ltr" lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dashboard Proyeksi Jabatan - Sistem Manajemen Proyeksi Kenaikan Pangkat Pegawai">
    <meta name="author" content="BAPETEN">
    <meta name="theme-color" content="#4f46e5">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/icon.jpg') }}">
    <title>@yield('title', 'Dashboard') - Proyeksi Jabatan BAPETEN</title>

    <link href="{{ asset('assets/extra-libs/c3/c3.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/chartist/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet">
    <link href="{{ asset('dist/css/style.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/dashboard-shared.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>
    {{-- Skip to main content link for accessibility --}}
    <a href="#main-content" class="skip-to-main">
        Skip to main content
    </a>

    {{-- Preloader with ARIA label --}}
    <div class="preloader" role="status" aria-label="Loading application">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
        <span class="sr-only">Loading...</span>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        @include('dashboard.partials.topbar')
        @include('dashboard.partials.sidebar')

        <div class="page-wrapper">
            <main id="main-content" role="main" aria-label="Main content">
                @yield('content')
            </main>
            @include('dashboard.partials.footer')
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('dist/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('dist/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('dist/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/c3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/c3/c3.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chartist/dist/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('dist/js/pages/dashboards/dashboard1.min.js') }}"></script>

    <script>
        (function() {
            function safeInitDashboardUi() {
                var preloader = document.querySelector('.preloader');
                if (preloader) {
                    preloader.style.display = 'none';
                }

                var mainWrapper = document.getElementById('main-wrapper');
                var navTogglers = document.querySelectorAll('.nav-toggler');
                navTogglers.forEach(function(trigger) {
                    trigger.addEventListener('click', function(event) {
                        event.preventDefault();

                        if (!mainWrapper) {
                            return;
                        }

                        var wasSidebarShown = mainWrapper.classList.contains('show-sidebar');

                        // Let original template handlers run first; only fallback if no change happened.
                        setTimeout(function() {
                            var isSidebarShown = mainWrapper.classList.contains('show-sidebar');
                            if (isSidebarShown === wasSidebarShown) {
                                mainWrapper.classList.toggle('show-sidebar');
                            }
                        }, 0);
                    });
                });

                if (window.bootstrap) {
                    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
                        window.bootstrap.Dropdown.getOrCreateInstance(el);
                    });

                    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(el) {
                        var target = el.getAttribute('data-bs-target');
                        if (!target) {
                            return;
                        }

                        var collapseTarget = document.querySelector(target);
                        if (collapseTarget) {
                            window.bootstrap.Collapse.getOrCreateInstance(collapseTarget, {
                                toggle: false
                            });
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', safeInitDashboardUi);
            } else {
                safeInitDashboardUi();
            }
        })();
    </script>

    @stack('scripts')
    <x-modal-confirm />
</body>

</html>
