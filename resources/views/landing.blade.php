@extends('layout.guest')

@section('title', 'SMSLORD — SMS Verification Numbers')

@push('styles')
<style>
    :root {
        --brand: #4f46e5;
        --brand-2: #7c3aed;
        --brand-3: #a855f7;
        --ink: #0f172a;
        --muted: #64748b;
    }
    .guest-body {
        font-family: Inter, system-ui, sans-serif;
        color: var(--ink);
        background: #f8fafc;
    }
    .lp-nav {
        backdrop-filter: blur(12px);
        background: rgba(255,255,255,.85);
        border-bottom: 1px solid #e2e8f0;
    }
    .lp-logo { height: 36px; }
    .lp-hero {
        background: linear-gradient(135deg, #0f172a 0%, #312e81 45%, #6d28d9 100%);
        color: #fff;
        padding: 5rem 0 6rem;
        position: relative;
        overflow: hidden;
    }
    .lp-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(168,85,247,.35), transparent 45%),
                    radial-gradient(circle at 80% 0%, rgba(79,70,229,.4), transparent 40%);
    }
    .lp-hero .container { position: relative; z-index: 1; }
    .lp-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 999px;
        padding: .35rem .85rem;
        font-size: .8rem;
        font-weight: 600;
    }
    .lp-title {
        font-size: clamp(2rem, 5vw, 3.25rem);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -.03em;
    }
    .lp-lead { color: rgba(255,255,255,.82); font-size: 1.1rem; max-width: 34rem; }
    .btn-lp-primary {
        background: linear-gradient(135deg, var(--brand), var(--brand-2));
        border: 0;
        color: #fff;
        font-weight: 700;
        padding: .75rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(79,70,229,.35);
    }
    .btn-lp-primary:hover { filter: brightness(1.06); color: #fff; }
    .btn-lp-ghost {
        border: 1px solid rgba(255,255,255,.35);
        color: #fff;
        font-weight: 600;
        padding: .75rem 1.5rem;
        border-radius: 12px;
        background: rgba(255,255,255,.08);
    }
    .btn-lp-ghost:hover { background: rgba(255,255,255,.15); color: #fff; }
    .lp-stat {
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 14px;
        padding: 1rem 1.15rem;
    }
    .lp-stat strong { display: block; font-size: 1.35rem; }
    .lp-stat span { font-size: .8rem; opacity: .8; }
    .lp-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(15,23,42,.06);
        height: 100%;
        transition: transform .2s, box-shadow .2s;
    }
    .lp-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(79,70,229,.12);
    }
    .lp-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #eef2ff, #f3e8ff);
        color: var(--brand);
        font-size: 1.35rem;
    }
    .lp-step-num {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: var(--brand);
        color: #fff;
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: .9rem;
    }
    .lp-cta {
        background: linear-gradient(135deg, var(--brand), var(--brand-3));
        border-radius: 20px;
        color: #fff;
        padding: 3rem 2rem;
    }
    .lp-footer { color: var(--muted); font-size: .875rem; }
    .lp-footer a { color: var(--muted); text-decoration: none; }
    .lp-footer a:hover { color: var(--brand); }
</style>
@endpush

@section('content')
<nav class="lp-nav sticky-top">
    <div class="container py-3 d-flex align-items-center justify-content-between">
        <a href="{{ url('/') }}">
            <img src="{{ static_asset('assets/images/logo.svg') }}" alt="SMSLORD" class="lp-logo">
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('login') }}" class="btn btn-link text-decoration-none fw-semibold">Sign in</a>
            <a href="{{ url('register') }}" class="btn btn-lp-primary btn-sm">Get started</a>
        </div>
    </div>
</nav>

<section class="lp-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="lp-badge mb-3"><i class="ti ti-bolt"></i> Trusted SMS verification platform</div>
                <h1 class="lp-title mb-3">Verify any app.<br>Keep your real number private.</h1>
                <p class="lp-lead mb-4">
                    Rent virtual numbers from 100+ countries. Receive OTP codes in seconds.
                    Fund your wallet instantly and start verifying today.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="{{ url('register') }}" class="btn btn-lp-primary">Create free account</a>
                    <a href="{{ url('login') }}" class="btn btn-lp-ghost">I already have an account</a>
                </div>
                <div class="row g-3">
                    <div class="col-4"><div class="lp-stat"><strong>100+</strong><span>Countries</span></div></div>
                    <div class="col-4"><div class="lp-stat"><strong>4</strong><span>SMS servers</span></div></div>
                    <div class="col-4"><div class="lp-stat"><strong>24/7</strong><span>Instant codes</span></div></div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <img src="{{ static_asset('assets/images/front.svg') }}" alt="" class="img-fluid" style="max-height: 320px; opacity: .95;">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-2">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Everything you need in one place</h2>
            <p class="text-muted mb-0">Built for privacy, speed, and affordability.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card lp-card p-4">
                    <div class="lp-icon mb-3"><i class="ti ti-world"></i></div>
                    <h5 class="fw-bold">Global numbers</h5>
                    <p class="text-muted mb-0">Access virtual numbers across multiple regions and providers from a single dashboard.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card lp-card p-4">
                    <div class="lp-icon mb-3"><i class="ti ti-message-2"></i></div>
                    <h5 class="fw-bold">Fast OTP delivery</h5>
                    <p class="text-muted mb-0">Codes arrive in real time. Track every verification order from your account.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card lp-card p-4">
                    <div class="lp-icon mb-3"><i class="ti ti-wallet"></i></div>
                    <h5 class="fw-bold">Easy wallet funding</h5>
                    <p class="text-muted mb-0">Top up with card or bank transfer. Transparent pricing in Nigerian Naira.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3">How it works</h2>
                <div class="d-flex gap-3 mb-3">
                    <div class="lp-step-num">1</div>
                    <div><strong>Create your account</strong><p class="text-muted mb-0 small">Sign up in under a minute.</p></div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="lp-step-num">2</div>
                    <div><strong>Fund your wallet</strong><p class="text-muted mb-0 small">Add from ₦2,000 via instant payment.</p></div>
                </div>
                <div class="d-flex gap-3">
                    <div class="lp-step-num">3</div>
                    <div><strong>Rent a number & receive SMS</strong><p class="text-muted mb-0 small">Pick country and service, get your code.</p></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="lp-cta text-center text-lg-start">
                    <h3 class="fw-bold mb-2">Ready to verify?</h3>
                    <p class="mb-4 opacity-90">Join SMSLORD and protect your phone number on every signup.</p>
                    <a href="{{ url('register') }}" class="btn btn-light fw-bold px-4">Start now — it's free</a>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="lp-footer border-top py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
        <span>&copy; {{ date('Y') }} SMSLORD</span>
        <div class="d-flex gap-3">
            <a href="{{ url('terms') }}">Terms</a>
            <a href="{{ url('policy') }}">Privacy</a>
            <a href="{{ url('faq') }}">FAQ</a>
        </div>
    </div>
</footer>
@endsection
