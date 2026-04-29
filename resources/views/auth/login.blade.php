@extends('layouts.auth')

@section('title', 'Login')

@push('styles')
    <style>
        .auth-wrapper {
            min-height: 100vh;
            padding: 24px;
            background: #f6f8fb;
        }

        .auth-box {
            width: 100%;
            max-width: 980px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(31, 45, 61, 0.12);
            background: #fff;
        }

        .modal-bg-img {
            min-height: 520px;
            background-size: cover;
            background-position: center;
        }

        .login-brand {
            width: 96px;
            height: auto;
        }

        .auth-form-panel {
            display: flex;
            align-items: center;
        }

        .auth-form-panel .p-3 {
            width: 100%;
            padding: 2rem !important;
        }

        @media (max-width: 767.98px) {
            .auth-wrapper {
                padding: 12px;
            }

            .modal-bg-img {
                min-height: 240px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="auth-box row w-100">
        <div class="col-lg-6 col-md-5 modal-bg-img" style="background-image: url('{{ asset('assets/images/login.jpg') }}');">
        </div>
        <div class="col-lg-6 col-md-7 bg-white auth-form-panel">
            <div class="p-3">
                <div class="text-center">
                    <img src="{{ asset('assets/images/icon.jpg') }}" alt="logo" class="login-brand">
                </div>
                <h2 class="mt-3 text-center">Sign In</h2>
                <p class="text-center">Masukkan email dan password akun admin untuk masuk.</p>

                @if (session('status'))
                    <div class="alert alert-success mb-3" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-3" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-4">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label text-dark" for="email">Email</label>
                        <input class="form-control" id="email" name="email" type="email"
                            placeholder="contoh: admin@bapeten.local" value="{{ old('email') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label text-dark" for="password">Password</label>
                        <input class="form-control" id="password" name="password" type="password"
                            placeholder="masukkan password">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn w-100 btn-dark">Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
