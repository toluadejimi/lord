<nav class="nav nav-pills flex-wrap gap-2 mb-4 vtu-subnav">
    <a class="nav-link {{ request()->routeIs('vas.airtime*') ? 'active' : '' }}" href="{{ route('vas.airtime') }}">Airtime</a>
    <a class="nav-link {{ request()->routeIs('vas.data*') ? 'active' : '' }}" href="{{ route('vas.data') }}">Data</a>
    <a class="nav-link {{ request()->routeIs('vas.cable*') ? 'active' : '' }}" href="{{ route('vas.cable') }}">Cable TV</a>
    <a class="nav-link {{ request()->routeIs('vas.electricity*') ? 'active' : '' }}" href="{{ route('vas.electricity') }}">Electricity</a>
</nav>

@if(!$vasConfigured)
<div class="alert alert-warning">
    VTU payments are not fully configured. Admin must set <strong>WEBKEY</strong> and <strong>SPRINTPAY_WEBHOOK_SECRET</strong> in Admin → Settings.
</div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="text-muted small">Wallet balance</div>
            <div class="h4 mb-0 text-primary">₦{{ number_format($wallet, 2) }}</div>
            <div class="text-muted small">Debited instantly on purchase</div>
        </div>
        <a href="{{ url('fund-wallet') }}" class="btn btn-sm btn-outline-primary">Fund wallet</a>
    </div>
</div>

<style>
.vtu-subnav .nav-link { border-radius: 999px; color: #4b5563; }
.vtu-subnav .nav-link.active { background: #6366f1; color: #fff; }
.bundle-card { cursor: pointer; border: 1px solid #e5e7eb; border-radius: 10px; padding: .75rem; transition: .15s; }
.bundle-card:hover, .bundle-card.selected { border-color: #6366f1; background: #f5f3ff; }
</style>
