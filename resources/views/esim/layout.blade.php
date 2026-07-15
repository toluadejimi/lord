@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
<style>
.esim-page .cp-hero { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
.esim-package {
    border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; background: #fff;
    height: 100%; display: flex; flex-direction: column; gap: 0.35rem;
    transition: border-color .15s, box-shadow .15s;
}
.esim-package:hover { border-color: #a78bfa; box-shadow: 0 8px 24px rgba(79,70,229,.12); }
.esim-package__name { font-weight: 700; color: #0f172a; font-size: .95rem; }
.esim-package__meta { font-size: .8rem; color: #64748b; }
.esim-package__price { font-weight: 800; font-size: 1.15rem; color: #4f46e5; margin-top: auto; }
.esim-filters .form-control, .esim-filters .form-select { border-radius: 10px; }
.esim-status { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; padding: .2rem .5rem; border-radius: 999px; }
.esim-status--completed { background: #dcfce7; color: #166534; }
.esim-status--processing { background: #e0e7ff; color: #3730a3; }
.esim-status--failed { background: #fee2e2; color: #991b1b; }
.esim-qr { max-width: 160px; border-radius: 8px; }
</style>
<div class="pc-container">
    <div class="pc-content p-4 cp-page esim-page">
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="cp-hero">
            <div class="cp-hero__main">
                <h2 class="h4"><i class="ti ti-sim-card me-1"></i> Esim</h2>
                <p class="cp-hero__subtitle">Buy travel &amp; data eSIMs — activate instantly with a QR code.</p>
            </div>
            @include('partials.customer-wallet-card', [
                'wallet' => $wallet,
                'secondaryUrl' => url('wallet-transactions'),
                'secondaryLabel' => 'History',
            ])
        </div>

        <nav class="cp-subnav">
            <a href="{{ route('esim.index') }}" class="{{ request()->routeIs('esim.index') ? 'active' : '' }}">Buy Esim</a>
            <a href="{{ route('esim.orders') }}" class="{{ request()->routeIs('esim.orders') ? 'active' : '' }}">My orders</a>
        </nav>

        @yield('esim-body')
    </div>
</div>
@stack('page-scripts')
@endsection
