@extends('layout.main')
@section('content')
@include('partials.dashboard-styles')

<div class="pc-container dash-app">
    <div class="pc-content dash-page">
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm dash-alert">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm dash-alert">{{ session('error') }}</div>
        @endif

        <header class="dash-top">
            <div>
                <p class="dash-greeting">Welcome Back 👋</p>
                <h1 class="dash-user">{{ $displayName }}</h1>
            </div>
            <div class="dash-brand-pill">sms<span>LORD</span></div>
        </header>

        <a href="{{ url('fund-wallet') }}" class="dash-balance-card text-decoration-none">
            <div class="dash-balance-label">Main Balance</div>
            <div class="dash-balance-amount">₦{{ number_format($wallet, 2) }}</div>
            <div class="dash-balance-cta">
                <span><i class="ti ti-plus"></i> Fund wallet</span>
                <i class="ti ti-chevron-right"></i>
            </div>
        </a>

        <div class="dash-section-head">
            <h2 class="dash-section-title">Popular Services</h2>
            <a href="{{ url('vas') }}" class="dash-see-all">See all</a>
        </div>

        <div class="dash-services-grid">
            @foreach($popularServices as $service)
            <a href="{{ $service['url'] }}" class="dash-service-tile dash-tone-{{ $service['tone'] }}">
                <span class="dash-service-icon">
                    <i class="{{ str_contains($service['icon'], 'fa-') ? $service['icon'] : 'ti '.$service['icon'] }}"></i>
                </span>
                <span class="dash-service-label">{{ $service['label'] }}</span>
            </a>
            @endforeach
        </div>

        @if($verificationServers->isNotEmpty())
        <div class="dash-section-head mt-4">
            <h2 class="dash-section-title">Virtual Numbers</h2>
        </div>
        <div class="dash-server-list">
            @foreach($verificationServers as $server)
            <a href="{{ url(ltrim($server['user_route'], '/')) }}" class="dash-server-row">
                <span class="dash-server-num">{{ $server['server_num'] }}</span>
                <span class="dash-server-name">{{ $server['menu_label'] }}</span>
                <i class="ti ti-chevron-right ms-auto opacity-50"></i>
            </a>
            @endforeach
        </div>
        @endif

        @if($recentOrders->isNotEmpty())
        <div class="dash-section-head mt-4">
            <h2 class="dash-section-title">Recent activity</h2>
            <a href="{{ url('orders') }}" class="dash-see-all">View all</a>
        </div>
        <div class="dash-activity-card">
            @foreach($recentOrders as $order)
            <div class="dash-activity-row">
                <div>
                    <div class="dash-activity-service">{{ $order->service ?? 'Verification' }}</div>
                    <div class="dash-activity-meta">
                        {{ \App\Support\VerificationLabels::customerServerLabel((int) $order->type) }}
                        · {{ $order->created_at?->diffForHumans() }}
                    </div>
                </div>
                <div class="text-end">
                    <div class="dash-activity-amount">₦{{ number_format((float) $order->cost, 2) }}</div>
                    @php $st = (int) $order->status; @endphp
                    <span class="dash-activity-status dash-st-{{ $st === 2 ? 'ok' : ($st === 99 ? 'muted' : 'pending') }}">
                        {{ $st === 2 ? 'Done' : ($st === 99 ? 'Cancelled' : 'Pending') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
