@extends('layout.main')
@section('content')
@include('telegram-blue-tick.partials.styles')
<div class="pc-container">
    <div class="pc-content p-4 tbt-page">
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="tbt-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h2 class="h4 mb-1"><i class="fab fa-telegram me-1"></i> Telegram Blue Tick</h2>
                <p class="mb-0 small opacity-90">Gift Telegram Premium to any username — delivered via verified partner API.</p>
            </div>
            <div class="tbt-wallet-pill">
                <div class="small opacity-75">Wallet</div>
                <div class="h5 mb-0 fw-bold">₦{{ number_format($wallet, 2) }}</div>
                <a href="{{ url('fund-wallet') }}" class="small text-white text-decoration-underline opacity-90">Fund wallet</a>
            </div>
        </div>

        <nav class="tbt-subnav">
            <a href="{{ route('telegram-blue-tick.index') }}" class="{{ request()->routeIs('telegram-blue-tick.index') ? 'active' : '' }}">Buy Premium</a>
            <a href="{{ route('telegram-blue-tick.orders') }}" class="{{ request()->routeIs('telegram-blue-tick.orders') ? 'active' : '' }}">My orders</a>
        </nav>

        @yield('tbt-body')
    </div>
</div>
@endsection
