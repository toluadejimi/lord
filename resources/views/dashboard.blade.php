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
            @if(($service['action'] ?? '') === 'numbers-sheet')
            <button type="button" class="dash-service-tile dash-tone-{{ $service['tone'] }}" data-dash-open="numbers-sheet" aria-haspopup="dialog" aria-controls="dash-numbers-sheet">
                <span class="dash-service-icon">
                    <i class="{{ str_contains($service['icon'], 'fa-') ? $service['icon'] : 'ti '.$service['icon'] }}"></i>
                </span>
                <span class="dash-service-label">{{ $service['label'] }}</span>
            </button>
            @else
            <a href="{{ $service['url'] }}" class="dash-service-tile dash-tone-{{ $service['tone'] }}">
                <span class="dash-service-icon">
                    <i class="{{ str_contains($service['icon'], 'fa-') ? $service['icon'] : 'ti '.$service['icon'] }}"></i>
                </span>
                <span class="dash-service-label">{{ $service['label'] }}</span>
            </a>
            @endif
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

@if($verificationServers->isNotEmpty())
<div class="dash-sheet-root" id="dash-numbers-sheet-root" hidden>
    <div class="dash-sheet-backdrop" data-dash-close="numbers-sheet" aria-hidden="true"></div>
    <div class="dash-sheet" id="dash-numbers-sheet" role="dialog" aria-modal="true" aria-labelledby="dash-numbers-sheet-title">
        <div class="dash-sheet-handle" aria-hidden="true"></div>
        <div class="dash-sheet-head">
            <h3 class="dash-sheet-title" id="dash-numbers-sheet-title">Choose a number service</h3>
            <button type="button" class="dash-sheet-close" data-dash-close="numbers-sheet" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <p class="dash-sheet-sub">Select a verification server to buy a virtual number.</p>
        <div class="dash-sheet-list">
            @foreach($verificationServers as $server)
            <a href="{{ url(ltrim($server['user_route'], '/')) }}" class="dash-sheet-option">
                <span class="dash-server-num">{{ $server['server_num'] }}</span>
                <span class="dash-sheet-option-text">
                    <span class="dash-sheet-option-name">{{ $server['menu_label'] }}</span>
                    <span class="dash-sheet-option-hint">{{ \App\Support\VerificationLabels::customerServerHint((int) $server['server_num']) }}</span>
                </span>
                <i class="ti ti-chevron-right dash-sheet-option-arrow"></i>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<script>
(function () {
    var root = document.getElementById('dash-numbers-sheet-root');
    if (!root) return;

    var sheet = document.getElementById('dash-numbers-sheet');
    var openers = document.querySelectorAll('[data-dash-open="numbers-sheet"]');
    var closers = root.querySelectorAll('[data-dash-close="numbers-sheet"]');
    var lastFocus = null;

    function openSheet() {
        lastFocus = document.activeElement;
        root.hidden = false;
        requestAnimationFrame(function () {
            root.classList.add('is-open');
        });
        document.body.classList.add('dash-sheet-open');
    }

    function closeSheet() {
        root.classList.remove('is-open');
        document.body.classList.remove('dash-sheet-open');
        window.setTimeout(function () {
            if (!root.classList.contains('is-open')) {
                root.hidden = true;
            }
        }, 280);
        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    openers.forEach(function (btn) {
        btn.addEventListener('click', openSheet);
    });

    closers.forEach(function (el) {
        el.addEventListener('click', closeSheet);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && root.classList.contains('is-open')) {
            closeSheet();
        }
    });
})();
</script>
@endsection
