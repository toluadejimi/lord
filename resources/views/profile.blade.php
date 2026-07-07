@extends('layout.main')
@section('content')
@include('partials.profile-styles')

<div class="pc-container prof-app">
    <div class="pc-content prof-page">
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm prof-alert">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm prof-alert">{{ session('error') }}</div>
        @endif

        <header class="prof-hero">
            <div class="prof-avatar" aria-hidden="true">{{ $initials }}</div>
            <h1 class="prof-name">{{ $displayName }}</h1>
            <p class="prof-email">{{ $user->email }}</p>
            @if(!empty($user->phone))
            <p class="prof-phone">{{ $user->phone }}</p>
            @endif
        </header>

        <a href="{{ url('fund-wallet') }}" class="prof-wallet-card text-decoration-none">
            <div>
                <div class="prof-wallet-label">Wallet balance</div>
                <div class="prof-wallet-amount">₦{{ number_format($wallet, 2) }}</div>
            </div>
            <span class="prof-wallet-cta">Fund <i class="ti ti-chevron-right"></i></span>
        </a>

        <div class="prof-section-label">Account</div>
        <div class="prof-menu-card">
            <a href="{{ url('fund-wallet') }}" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-wallet"><i class="ti ti-wallet"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">Fund wallet</span>
                    <span class="prof-menu-hint">Add money to your balance</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
            <a href="{{ route('wallet.transactions') }}" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-logs"><i class="ti ti-receipt"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">Transactions</span>
                    <span class="prof-menu-hint">View wallet history</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
            <a href="{{ url('orders') }}" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-numbers"><i class="ti ti-messages"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">My verifications</span>
                    <span class="prof-menu-hint">SMS orders and OTP logs</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
            <a href="{{ url('change-password') }}" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-lock"><i class="ti ti-lock"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">Change password</span>
                    <span class="prof-menu-hint">Update your login password</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
            <a href="{{ url('api-docs') }}" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-api"><i class="ti ti-code"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">API documentation</span>
                    <span class="prof-menu-hint">Keys, webhooks &amp; integration</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
        </div>

        <div class="prof-section-label">Support &amp; legal</div>
        <div class="prof-menu-card">
            <a href="https://t.me/smslordcare" target="_blank" rel="noopener noreferrer" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-telegram"><i class="fab fa-telegram"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">Telegram support</span>
                    <span class="prof-menu-hint">Chat with our team</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
            <a href="{{ url('policy') }}" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-policy"><i class="ti ti-shield"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">Privacy policy</span>
                    <span class="prof-menu-hint">How we handle your data</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
            <a href="{{ url('terms') }}" class="prof-menu-row">
                <span class="prof-menu-icon prof-tone-terms"><i class="ti ti-file-text"></i></span>
                <span class="prof-menu-text">
                    <span class="prof-menu-title">Terms of service</span>
                    <span class="prof-menu-hint">Usage rules &amp; policies</span>
                </span>
                <i class="ti ti-chevron-right prof-menu-arrow"></i>
            </a>
        </div>

        <a href="{{ url('log-out') }}" class="prof-logout-btn">
            <i class="ti ti-logout"></i> Log out
        </a>
    </div>
</div>
@endsection
