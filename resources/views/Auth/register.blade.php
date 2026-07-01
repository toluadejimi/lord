@extends('layout.guest')

@section('title', 'Create account — SMSLORD')

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
            <h1 class="mb-3">Join SMSLORD</h1>
            <p class="mb-4">Create your account and start verifying apps without sharing your real phone number.</p>
            <div class="auth-feature"><i class="ti ti-user-plus"></i> Free account setup</div>
            <div class="auth-feature"><i class="ti ti-wallet"></i> Fund wallet from ₦2,000</div>
            <div class="auth-feature"><i class="ti ti-message-2"></i> Receive codes in seconds</div>
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

            <h2>Create account</h2>
            <p class="sub">Fill in your details to get started</p>

            <form action="{{ url('register_now') }}" method="post" class="auth-form">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="johndoe" value="{{ old('username') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input name="email" type="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" placeholder="Min. 4 characters" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm password</label>
                    <input name="password_confirmation" type="password" class="form-control" placeholder="Repeat password" required>
                </div>
                <button type="submit" class="btn btn-auth">Create account</button>
            </form>

            <p class="text-muted small text-center mt-3 mb-0">
                By registering you agree to our <a href="{{ url('terms') }}" class="text-decoration-none">Terms</a> and <a href="{{ url('policy') }}" class="text-decoration-none">Privacy Policy</a>.
            </p>

            <p class="auth-switch">
                Already have an account? <a href="{{ url('login') }}">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
