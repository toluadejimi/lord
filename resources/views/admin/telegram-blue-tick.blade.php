@extends('admin.layout', ['adminActive' => 'telegram-blue-tick'])

@section('title', 'Telegram Blue Tick')
@section('page-title', 'Telegram Blue Tick')
@section('page-subtitle', 'iStar / Fragment API — gift Telegram Premium to any username.')

@section('content')
@if(!$configured)
<div class="alert alert-warning">Set your <strong>ISTAR_API_KEY</strong> below and enable the module for customers to see Telegram Blue Tick.</div>
@elseif(!$pricingConfigured)
<div class="alert alert-warning">API key is set, but <strong>customer pricing is not configured</strong>. Set the rate (₦ per $1) or fixed NGN prices below — otherwise customers will see no packages.</div>
@endif

<form method="post" action="{{ url('admin/telegram-blue-tick/fetch-packages') }}" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-outline-secondary btn-sm">Test API &amp; fetch packages</button>
</form>

<form method="post" action="{{ url('admin/telegram-blue-tick') }}">
    @csrf

    <div class="card mb-4">
        <div class="card-header">Module &amp; iStar API</div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input type="hidden" name="provider_telegram_blue_tick_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="provider_telegram_blue_tick_enabled" value="1" id="provider_telegram_blue_tick_enabled"
                    {{ $moduleEnabled ? 'checked' : '' }}>
                <label class="form-check-label" for="provider_telegram_blue_tick_enabled"><strong>Telegram Blue Tick enabled</strong> (customer menu)</label>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold">iStar API Key</label>
                    <input class="form-control form-control-sm" type="password" name="ISTAR_API_KEY"
                        placeholder="{{ $apiKeyMasked ? '•••••••• (saved)' : 'From iStar Developer Dashboard' }}">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label small fw-semibold">API Base URL</label>
                    <input class="form-control form-control-sm" type="text" name="ISTAR_API_BASE" value="{{ $apiBase }}">
                    <div class="form-text">Production: https://v1.fragmentapi.com/api/v1/partner</div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label small fw-semibold">Webhook secret</label>
                    <input class="form-control form-control-sm" type="password" name="ISTAR_WEBHOOK_SECRET"
                        placeholder="{{ $webhookSecretMasked ? '•••••••• (saved)' : 'Optional HMAC secret' }}">
                </div>
            </div>

            <p class="small text-muted mb-0">
                Webhook URL for iStar dashboard:
                <code>{{ url('/api/webhooks/istar') }}</code>
            </p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Customer pricing (NGN)</div>
        <div class="card-body">
            <p class="small text-muted">Leave fixed prices empty to use <strong>(USD × rate) + margin</strong> from packages API.</p>
            <div class="row mb-3">
                <div class="col-md-2 mb-2">
                    <label class="form-label small">Rate (₦ per $1)</label>
                    <input class="form-control form-control-sm" type="number" step="0.01" name="rate" value="{{ $rate }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label small">Margin (₦)</label>
                    <input class="form-control form-control-sm" type="number" step="0.01" name="margin" value="{{ $margin }}">
                </div>
            </div>
            <div class="row">
                @foreach([3, 6, 12] as $months)
                <div class="col-md-4 mb-2">
                    <label class="form-label small">{{ $months }} months — fixed ₦ (optional)</label>
                    <input class="form-control form-control-sm" type="number" step="0.01" name="telegram_premium_price_{{ $months }}"
                        value="{{ $fixedPrices[$months] }}" placeholder="Auto from rate">
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save settings</button>
</form>

@if(session('remotePackages'))
<div class="card mt-4">
    <div class="card-header">Provider packages (USD)</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Months</th><th>USD</th><th>TON</th><th>Customer ₦</th></tr></thead>
            <tbody>
                @foreach(session('displayPackages', []) as $pkg)
                <tr>
                    <td>{{ $pkg['months'] }}</td>
                    <td>${{ number_format($pkg['usd'], 2) }}</td>
                    <td>—</td>
                    <td>₦{{ number_format($pkg['price_ngn'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card mt-4">
    <div class="card-header">Recent orders</div>
    <div class="card-body p-0">
        @if($recentOrders->isEmpty())
        <p class="text-muted small p-3 mb-0">No orders yet.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>@username</th>
                        <th>Months</th>
                        <th>₦</th>
                        <th>Status</th>
                        <th>iStar ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td class="small">{{ $order->user->email ?? $order->user_id }}</td>
                        <td>@{{ $order->username }}</td>
                        <td>{{ $order->months }}</td>
                        <td>{{ number_format($order->amount_ngn, 2) }}</td>
                        <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                        <td class="small text-muted">{{ Str::limit($order->istar_order_id, 12) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
