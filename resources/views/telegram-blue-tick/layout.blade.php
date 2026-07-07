@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
@include('telegram-blue-tick.partials.styles')
<div class="pc-container">
    <div class="pc-content p-4 cp-page cp-theme-telegram tbt-page">
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="cp-hero tbt-hero">
            <div class="cp-hero__main">
                <h2 class="h4"><i class="fab fa-telegram me-1"></i> Telegram Blue Tick</h2>
                <p class="cp-hero__subtitle">Gift Telegram Premium to any username — delivered via verified partner API.</p>
            </div>
            @include('partials.customer-wallet-card', [
                'wallet' => $wallet,
                'secondaryUrl' => url('wallet-transactions'),
                'secondaryLabel' => 'History',
            ])
        </div>

        <nav class="cp-subnav tbt-subnav">
            <a href="{{ route('telegram-blue-tick.index') }}" class="{{ request()->routeIs('telegram-blue-tick.index') ? 'active' : '' }}">Buy Premium</a>
            <a href="{{ route('telegram-blue-tick.orders') }}" class="{{ request()->routeIs('telegram-blue-tick.orders') ? 'active' : '' }}">My orders</a>
        </nav>

        @yield('tbt-body')
    </div>
</div>
@stack('page-scripts')
@endsection
