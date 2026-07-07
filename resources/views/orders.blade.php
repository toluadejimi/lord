@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
@php
    $navCfg = app(\App\Services\AppConfigService::class);
    $verificationServers = collect(config('platform.admin_service_groups', []))
        ->filter(fn ($svc) => !empty($svc['user_route']) && !empty($svc['enabled_key']))
        ->map(function ($svc) use ($navCfg) {
            return array_merge($svc, [
                'enabled' => $navCfg->getBool($svc['enabled_key'], (bool) ($svc['enabled_default'] ?? false)),
            ]);
        })
        ->filter(fn ($svc) => $svc['enabled'])
        ->values();
@endphp
<style>
.orders-page .cp-subnav { margin-bottom: 0; }
.orders-hint { font-size: .8rem; color: #64748b; }
</style>

<div class="pc-container">
    <div class="pc-content p-4 cp-page orders-page">
        <div class="cp-hero">
            <div class="cp-hero__main">
                <h1 class="h4">My verifications</h1>
                <p class="cp-hero__subtitle">All SMS orders across enabled servers. OTP codes update automatically — no refresh needed.</p>
                @if($verificationServers->isNotEmpty())
                <nav class="cp-hero-nav" aria-label="Verification servers">
                    @foreach($verificationServers as $server)
                    <a href="{{ url(ltrim($server['user_route'], '/')) }}"><i class="ti ti-world"></i> {{ $server['menu_label'] ?? ('Server '.($server['server_num'] ?? '')) }}</a>
                    @endforeach
                </nav>
                @endif
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
