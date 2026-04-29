@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="auth-box row w-100">
        <div class="col-lg-7 col-md-5 modal-bg-img" style="background-image: url('{{ asset('assets/images/big/3.jpg') }}');">
        </div>
        <div class="col-lg-5 col-md-7 bg-white">
            <div class="p-3">
                <div class="text-center">
                    <img src="{{ asset('assets/images/big/icon.png') }}" alt="logo">
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
