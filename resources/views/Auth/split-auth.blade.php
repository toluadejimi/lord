@extends('layout.guest')

@section('title', ($tab ?? 'login') === 'register' ? 'Create account — SMSLORD' : 'Sign in — SMSLORD')

@push('styles')
<style>
    :root {
        --brand: #4f46e5;
        --brand-2: #7c3aed;
        --ink: #0f172a;
        --muted: #64748b;
    }
    .guest-body { font-family: Inter, system-ui, sans-serif; min-height: 100vh; }
    .auth-split { min-height: 100vh; display: flex; flex-wrap: wrap; }
    .auth-brand {
        flex: 1 1 42%;
        min-width: 280px;
        background: linear-gradient(160deg, #0f172a 0%, #3730a3 50%, #7c3aed 100%);
        color: #fff;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .auth-brand::after {
        content: '';
        position: absolute;
        width: 320px; height: 320px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        top: -80px; right: -80px;
    }
    .auth-brand .inner { position: relative; z-index: 1; max-width: 420px; }
    .auth-brand h1 { font-size: 2rem; font-weight: 800; line-height: 1.15; }
    .auth-brand p { color: rgba(255,255,255,.8); }
    .auth-feature {
        display: flex; align-items: center; gap: .75rem;
        margin-bottom: .85rem;
        font-size: .95rem;
    }
    .auth-feature i { font-size: 1.1rem; opacity: .9; }
    .auth-panels {
        flex: 1 1 58%;
        min-width: 300px;
        display: flex;
        background: #f8fafc;
    }
    .auth-panel {
        flex: 1;
        padding: 2.5rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .auth-panel-login { border-right: 1px solid #e2e8f0; background: #fff; }
    .auth-panel-register { background: #f8fafc; }
    .auth-panel h2 { font-size: 1.35rem; font-weight: 800; color: var(--ink); }
    .auth-panel .sub { color: var(--muted); font-size: .9rem; margin-bottom: 1.5rem; }
    .auth-form .form-control {
        border-radius: 10px;
        border-color: #e2e8f0;
        padding: .7rem .9rem;
    }
    .auth-form .form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    }
    .btn-auth {
        border: 0;
        border-radius: 10px;
        padding: .75rem;
        font-weight: 700;
        width: 100%;
        background: linear-gradient(135deg, var(--brand), var(--brand-2));
        color: #fff;
        box-shadow: 0 8px 20px rgba(79,70,229,.25);
    }
    .btn-auth:hover { filter: brightness(1.05); color: #fff; }
    .btn-auth-outline {
        border: 1px solid #e2e8f0;
        background: #fff;
        color: var(--ink);
        box-shadow: none;
    }
    .btn-auth-outline:hover { background: #f1f5f9; color: var(--ink); }
    .auth-panel.is-active { background: #fff; box-shadow: inset 0 0 0 2px var(--brand); }
    .auth-mobile-tabs { display: none; }
    @media (max-width: 991.98px) {
        .auth-panels { flex-direction: column; width: 100%; }
        .auth-panel-login { border-right: 0; border-bottom: 1px solid #e2e8f0; }
        .auth-panel.d-none-mobile { display: none !important; }
        .auth-panel.is-active { box-shadow: none; }
        .auth-mobile-tabs {
            display: flex;
            gap: .5rem;
            padding: 1rem 1rem 0;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }
        .auth-mobile-tabs button {
            flex: 1;
            border: 0;
            background: #f1f5f9;
            border-radius: 10px;
            padding: .65rem;
            font-weight: 700;
            color: var(--muted);
        }
        .auth-mobile-tabs button.active {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: #fff;
        }
        .auth-brand { padding: 2rem 1.5rem; min-height: auto; }
        .auth-brand h1 { font-size: 1.5rem; }
    }
    .auth-back {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 10;
        color: #fff;
        text-decoration: none;
        font-size: .875rem;
        opacity: .85;
    }
    .auth-back:hover { color: #fff; opacity: 1; }
</style>
@endpush

@section('content')
<div class="auth-split position-relative">
    <a href="{{ url('/') }}" class="auth-back d-lg-none"><i class="ti ti-arrow-left"></i> Home</a>

    <aside class="auth-brand">
        <div class="inner">
            <a href="{{ url('/') }}" class="d-inline-block mb-4">
                <img src="{{ static_asset('assets/images/logo.svg') }}" alt="SMSLORD" style="height: 40px; filter: brightness(0) invert(1);">
            </a>
            <h1 class="mb-3">Your privacy-first SMS verification hub</h1>
            <p class="mb-4">Sign in or create an account to rent numbers, receive OTP codes, and manage your wallet.</p>
            <div class="auth-feature"><i class="ti ti-shield-check"></i> Non-VoIP numbers worldwide</div>
            <div class="auth-feature"><i class="ti ti-bolt"></i> Instant code delivery</div>
            <div class="auth-feature"><i class="ti ti-credit-card"></i> Pay with card or transfer</div>
        </div>
    </aside>

    <div class="auth-panels flex-grow-1">
        <div class="auth-mobile-tabs w-100">
            <button type="button" class="{{ ($tab ?? 'login') === 'login' ? 'active' : '' }}" data-auth-tab="login">Sign in</button>
            <button type="button" class="{{ ($tab ?? 'login') === 'register' ? 'active' : '' }}" data-auth-tab="register">Register</button>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-0 mb-0 w-100">
                <ul class="mb-0 small">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-success border-0 rounded-0 mb-0 w-100 small">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 rounded-0 mb-0 w-100 small">{{ session('error') }}</div>
        @endif

        <div class="auth-panel auth-panel-login {{ ($tab ?? 'login') === 'login' ? 'is-active' : '' }} {{ ($tab ?? 'login') !== 'login' ? 'd-none-mobile' : '' }}" data-panel="login">
            <div style="max-width: 360px; margin: 0 auto; width: 100%;">
                <h2>Welcome back</h2>
                <p class="sub">Sign in to your SMSLORD account</p>
                <form action="{{ url('login_now') }}" method="post" class="auth-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="••••••••" required>
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
                <p class="text-center text-muted small mt-4 mb-0 d-lg-none">
                    No account? <a href="{{ url('register') }}" class="fw-semibold">Register</a>
                </p>
            </div>
        </div>

        <div class="auth-panel auth-panel-register {{ ($tab ?? 'login') === 'register' ? 'is-active' : '' }} {{ ($tab ?? 'login') !== 'register' ? 'd-none-mobile' : '' }}" data-panel="register">
            <div style="max-width: 360px; margin: 0 auto; width: 100%;">
                <h2>Create account</h2>
                <p class="sub">Start verifying in minutes</p>
                <form action="{{ url('register_now') }}" method="post" class="auth-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="johndoe" value="{{ old('username') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Email</label>
                        <input name="email" type="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Min. 4 characters" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-muted">Confirm password</label>
                        <input name="password_confirmation" type="password" class="form-control" placeholder="Repeat password" required>
                    </div>
                    <button type="submit" class="btn btn-auth">Create account</button>
                </form>
                <p class="text-center text-muted small mt-3 mb-0">
                    By registering you agree to our <a href="{{ url('terms') }}">Terms</a> and <a href="{{ url('policy') }}">Privacy Policy</a>.
                </p>
                <p class="text-center text-muted small mt-3 mb-0 d-lg-none">
                    Have an account? <a href="{{ url('login') }}" class="fw-semibold">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-auth-tab]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var tab = btn.getAttribute('data-auth-tab');
        document.querySelectorAll('[data-auth-tab]').forEach(function (b) { b.classList.toggle('active', b === btn); });
        document.querySelectorAll('[data-panel]').forEach(function (panel) {
            var show = panel.getAttribute('data-panel') === tab;
            panel.classList.toggle('d-none-mobile', !show);
            panel.classList.toggle('is-active', show);
        });
    });
});
</script>
@endpush
