@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
@include('esim.partials.styles')
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
                <p class="cp-hero__subtitle">Travel &amp; data eSIMs with instant QR activation.</p>
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
