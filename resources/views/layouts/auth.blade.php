<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login">
    <title>@yield('title', 'Login')</title>
    <link href="{{ asset('dist/css/style.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative">
        @yield('content')
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
