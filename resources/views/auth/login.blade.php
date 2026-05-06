@extends('layouts.auth')

@section('title', 'Login')

@push('styles')
    <style>
        .auth-wrapper {
            min-height: 100vh;
            padding: 24px;
            background: linear-gradient(135deg, #f2f7ff 0%, #eef5fb 100%);
            align-items: center !important;
        }

        .auth-box {
            width: 100%;
            max-width: 900px;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(31, 45, 61, 0.12);
            background: #ffffff;
            margin: 12px 0;
        }

        .modal-bg-img {
            min-height: 520px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .modal-bg-img::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.12), rgba(255, 255, 255, 0.65));
        }

        .login-brand {
            width: 92px;
            height: auto;
            border-radius: 18px;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .auth-form-panel {
            display: flex;
            align-items: center;
            padding: 2rem;
        }

        .auth-form-panel .text-center h2 {
            font-size: 1.85rem;
            letter-spacing: -.02em;
        }

        .auth-form-panel .text-center p {
            color: #6b7280;
        }

        .form-control {
            border-radius: 0.9rem;
            border-color: rgba(15, 23, 42, 0.12);
            min-height: 54px;
        }

        .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-color: rgba(15, 23, 42, 0.12);
        }

        .btn-dark {
            border-radius: 0.9rem;
            min-height: 52px;
        }

        .auth-note {
            color: #6b7280;
            margin-top: 16px;
            font-size: 0.95rem;
        }

        @media (max-width: 767.98px) {
            .auth-wrapper {
                padding: 12px;
            }

            .modal-bg-img {
                min-height: 260px;
            }

            .auth-form-panel {
                padding: 1.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="auth-box row w-100">
        <div class="col-lg-6 col-md-5 modal-bg-img" style="background-image: url('{{ asset('assets/images/login.jpg') }}');">
        </div>
        <div class="col-lg-6 col-md-7 bg-white auth-form-panel">
            <div class="w-100">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/images/icon.jpg') }}" alt="logo" class="login-brand">
                </div>
                <div class="text-center mb-4">
                    <h2 class="mb-2">Selamat Datang</h2>
                    <p class="mb-0">Masuk menggunakan akun admin BAPETEN untuk mengelola proyeksi jabatan.</p>
                </div>

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

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-3">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label text-dark" for="email">Email</label>
                        <input class="form-control" id="email" name="email" type="email"
                            placeholder="contoh: admin@bapeten.local" value="{{ old('email') }}">
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label text-dark" for="password">Password</label>
                        <div class="input-group">
                            <input class="form-control" id="password" name="password" type="password"
                                placeholder="masukkan password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                aria-label="Tampilkan password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-eye">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 mb-2">Masuk</button>
                    <p class="auth-note text-center">Gunakan akun admin BAPETEN untuk mengakses dashboard proyeksi jabatan.
                    </p>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');

                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ?
                        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' :
                        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.37 21.37 0 0 1 5.06-6.06"></path><path d="M1 1l22 22"></path><path d="M10.59 10.59a3 3 0 0 0 4.24 4.24"></path><path d="M14.12 14.12a3 3 0 0 1-4.24-4.24"></path></svg>';
                });
            });
        </script>
    @endpush
@endsection
