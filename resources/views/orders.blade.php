@extends('layout.main')
@section('content')
<style>
.orders-page { --orders-accent: #4f46e5; }
.orders-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #a855f7 100%);
    border-radius: 16px; color: #fff; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px rgba(79, 70, 229, .25);
}
.orders-nav { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
.orders-nav a {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem .75rem; border-radius: 999px; font-size: .78rem; font-weight: 600;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22);
    color: #fff; text-decoration: none;
}
.orders-nav a:hover { background: rgba(255,255,255,.22); color: #fff; }
.orders-card { border: 0; border-radius: 16px; box-shadow: 0 4px 24px rgba(15, 23, 42, .06); }
.orders-hint { font-size: .8rem; color: #64748b; }
</style>

<div class="pc-container">
    <div class="pc-content p-4 orders-page">
        <div class="orders-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1 class="h4 mb-1">My verifications</h1>
                <p class="mb-0 small opacity-90">All SMS orders across Server 1–4. OTP codes update automatically — no refresh needed.</p>
                <div class="orders-nav">
                    <a href="{{ url('cworld') }}"><i class="ti ti-world"></i> Server 1</a>
                    <a href="{{ url('usa2') }}"><i class="ti ti-flag"></i> Server 2</a>
                    <a href="{{ url('world-sv2') }}"><i class="ti ti-globe"></i> Server 3</a>
                    <a href="{{ url('world-sv3') }}"><i class="ti ti-planet"></i> Server 4</a>
                </div>
            </div>
            <div class="text-end">
                <div class="small opacity-75">Wallet</div>
                <div class="h4 mb-0 fw-bold">₦{{ number_format((float) Auth::user()->wallet, 2) }}</div>
                <a href="{{ url('fund-wallet') }}" class="small text-white text-decoration-underline opacity-90">Fund wallet</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="card orders-card">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="mb-0 fw-bold">Verification orders</h5>
                        <p class="orders-hint mb-0 mt-1">Pending orders poll providers every few seconds. Cancel to release the number and refund your wallet.</p>
                    </div>
                    <span class="badge bg-light text-dark border">{{ $orders->count() }} total</span>
                </div>

                @include('partials.verification-orders-panel', [
                    'verifications' => $orders,
                    'showServerColumn' => true,
                    'panelId' => 'orders-main-panel',
                ])
            </div>
        </div>
    </div>
</div>
@endsection
