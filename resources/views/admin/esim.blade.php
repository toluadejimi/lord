@extends('admin.layout', ['adminActive' => 'esim'])

@section('title', 'Esim')
@section('page-title', 'Esim')
@section('page-subtitle', 'Sell data eSIMs to customers. Upstream provider stays internal.')

@section('content')
@if(!$configured)
<div class="alert alert-warning">Set your <strong>API key</strong> below and enable the module for customers to see <strong>Esim</strong>.</div>
@elseif(!$pricingConfigured)
<div class="alert alert-warning">API key is set, but <strong>customer pricing is not configured</strong>. Set the rate (₦ per $1) below — otherwise customers will see no packages.</div>
@endif

<form method="post" action="{{ url('admin/esim/fetch-packages') }}" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-outline-secondary btn-sm">Test API &amp; fetch packages</button>
</form>

<form method="post" action="{{ url('admin/esim') }}">
    @csrf

    <div class="card mb-4">
        <div class="card-header">Module &amp; API</div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input type="hidden" name="provider_pikasim_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="provider_pikasim_enabled" value="1" id="provider_pikasim_enabled"
                    {{ $moduleEnabled ? 'checked' : '' }}>
                <label class="form-check-label" for="provider_pikasim_enabled"><strong>Esim enabled</strong> (customer menu &amp; dashboard)</label>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold">Reseller API Key</label>
                    <input class="form-control form-control-sm" type="password" name="PIKASIM_API_KEY"
                        placeholder="{{ $apiKeyMasked ? '•••••••• (saved)' : 'pk_live_… from reseller dashboard' }}">
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label small fw-semibold">API Base URL</label>
                    <input class="form-control form-control-sm" type="text" name="PIKASIM_API_BASE" value="{{ $apiBase }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label small fw-semibold">Webhook secret</label>
                    <input class="form-control form-control-sm" type="password" name="PIKASIM_WEBHOOK_SECRET"
                        placeholder="{{ $webhookSecretMasked ? '•••••••• (saved)' : 'HMAC secret' }}">
                </div>
            </div>

            <p class="small text-muted mb-0">
                Webhook URL to register with the provider:
                <code>{{ url('/api/webhooks/esim') }}</code>
            </p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Customer pricing (NGN)</div>
        <div class="card-body">
            <p class="small text-muted mb-3">Provider price is in USD cents. Customer price = <strong>(USD × rate) + margin</strong>.</p>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label small">Rate (₦ per $1)</label>
                    <input class="form-control form-control-sm" type="number" step="0.01" name="rate" value="{{ $rate }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label small">Margin (₦)</label>
                    <input class="form-control form-control-sm" type="number" step="0.01" name="margin" value="{{ $margin }}">
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save settings</button>
</form>

@if(session('displayPackages'))
<div class="card mt-4">
    <div class="card-header">Sample customer packages (₦)</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Name</th><th>Location</th><th>Data</th><th>Days</th><th>Customer ₦</th></tr></thead>
            <tbody>
                @foreach(session('displayPackages', []) as $pkg)
                <tr>
                    <td>{{ $pkg['name'] }}</td>
                    <td>{{ $pkg['location'] ?: '—' }}</td>
                    <td>{{ $pkg['volume_gb'] }} GB</td>
                    <td>{{ $pkg['duration_days'] }}</td>
                    <td>₦{{ number_format($pkg['price_ngn'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card mt-4">
    <div class="card-header">Recent Esim orders</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>User</th>
                    <th>Package</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td><code>{{ $order->ref_id }}</code></td>
                    <td>{{ $order->user->username ?? $order->user_id }}</td>
                    <td>{{ $order->package_name }}</td>
                    <td>₦{{ number_format($order->amount_ngn, 2) }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at?->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-muted text-center py-3">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
