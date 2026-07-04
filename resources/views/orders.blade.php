@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
<style>
.orders-page .cp-subnav { margin-bottom: 0; }
.orders-hint { font-size: .8rem; color: #64748b; }
</style>

<div class="pc-container">
    <div class="pc-content p-4 cp-page orders-page">
        <div class="cp-hero">
            <div class="cp-hero__main">
                <h1 class="h4">My verifications</h1>
                <p class="cp-hero__subtitle">All SMS orders across Server 1–4. OTP codes update automatically — no refresh needed.</p>
                <nav class="cp-hero-nav" aria-label="Verification servers">
                    <a href="{{ url('cworld') }}"><i class="ti ti-world"></i> Server 1</a>
                    <a href="{{ url('usa2') }}"><i class="ti ti-flag"></i> Server 2</a>
                    <a href="{{ url('world-sv2') }}"><i class="ti ti-globe"></i> Server 3</a>
                    <a href="{{ url('world-sv3') }}"><i class="ti ti-planet"></i> Server 4</a>
                </nav>
            </div>
            @include('partials.customer-wallet-card', [
                'secondaryUrl' => url('wallet-transactions'),
                'secondaryLabel' => 'History',
            ])
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

        <div class="card cp-card">
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
                    'ordersPanelClass' => 'sv-orders-panel',
                ])
            </div>
        </div>
    </div>
</div>
@endsection
