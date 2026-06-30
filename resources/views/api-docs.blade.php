@extends('layout.main')
@section('content')
<div class="pc-container"><div class="pc-content p-4">
    <h2>Reseller API</h2>
    @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
    <div class="card card-body mb-3">
        <p><strong>API Key:</strong> <code>{{ $user->api_key }}</code></p>
        <form method="post" action="{{ url('api-docs/regenerate') }}" class="d-inline">@csrf
            <button class="btn btn-sm btn-warning">Regenerate Key</button>
        </form>
    </div>
    <form method="post" action="{{ url('api-docs/webhook') }}" class="card card-body mb-3">@csrf
        <label>Webhook URL (SMS delivery)</label>
        <input class="form-control mb-2" name="webhook_url" value="{{ $user->webhook_url }}" placeholder="https://yoursite.com/webhook">
        <button class="btn btn-primary btn-sm">Save Webhook</button>
    </form>
    <div class="card card-body">
        <h5>Endpoints (prefix: {{ url('/api/v1') }})</h5>
        <ul>
            <li><code>balance</code> — wallet balance</li>
            <li><code>get-world-countries</code> / <code>get-world-services</code></li>
            <li><code>check-world-number-availability</code> — country, service, api_key</li>
            <li><code>rent-world-number</code> — country, service, api_key</li>
            <li><code>get-world-sms</code> / <code>cancel-world-sms</code> — order_id, api_key</li>
            <li><code>get-usa-sms</code> / <code>cancel-usa-sms</code> — USA2 orders</li>
            <li><code>usa-services</code> / <code>rent-usa-number</code> — 410 Gone (retired)</li>
        </ul>
        <p class="text-muted mb-0">Pass <code>api_key</code> in query or POST body on every request.</p>
    </div>
</div></div>
@endsection
