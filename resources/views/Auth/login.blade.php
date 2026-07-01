@extends('layout.guest')

@section('title', 'Sign in — SMSLORD')

@push('styles')
    @include('Auth.partials.auth-styles')
@endpush

@section('content')
<div class="auth-page position-relative">
    <a href="{{ url('/') }}" class="auth-top-link d-lg-none"><i class="ti ti-arrow-left"></i> Home</a>

    <aside class="auth-brand">
        <div class="inner">
            <a href="{{ url('/') }}" class="d-inline-block mb-4">
                <img src="{{ static_asset('assets/images/logo.svg') }}" alt="SMSLORD" style="height: 40px; filter: brightness(0) invert(1);">
            </a>
            <h1 class="mb-3">Welcome back</h1>
            <p class="mb-4">Sign in to rent numbers, receive OTP codes, and manage your wallet.</p>
            <div class="auth-feature"><i class="ti ti-shield-check"></i> Secure, privacy-first verification</div>
            <div class="auth-feature"><i class="ti ti-bolt"></i> Instant SMS delivery</div>
            <div class="auth-feature"><i class="ti ti-world"></i> Numbers in 100+ countries</div>
        </div>
    </aside>

    <div class="auth-form-side">
        <div class="auth-form-wrap">
            <a href="{{ url('/') }}" class="auth-home-link d-none d-lg-inline-flex"><i class="ti ti-arrow-left"></i> Back to home</a>

            @if ($errors->any())
                <div class="alert alert-danger border-0 small py-2 mb-3">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @if (session('message'))
                <div class="alert alert-success border-0 small py-2 mb-3">{{ session('message') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger border-0 small py-2 mb-3">{{ session('error') }}</div>
            @endif

            <h2>Sign in</h2>
            <p class="sub">Enter your account details to continue</p>

            <form action="{{ url('login_now') }}" method="post" class="auth-form">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input name="email" type="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" placeholder="Your password" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" checked>
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                    <a href="{{ url('forgot-password') }}" class="small fw-semibold text-decoration-none">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-auth">Sign in</button>
            </form>

            <p class="auth-switch">
                Don't have an account? <a href="{{ url('register') }}">Create one</a>
            </p>
        </div>
    </div>
</div>
@endsection
