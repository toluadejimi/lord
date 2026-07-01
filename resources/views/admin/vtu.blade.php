@extends('admin.layout', ['adminActive' => 'vtu'])

@section('title', 'VTU / Bills')
@section('page-title', 'VTU & Bill Payments')
@section('page-subtitle', 'Category IDs come from SprintPay — save your API key, fetch the list, then paste each ID into Airtime / Data / Cable / Electricity.')

@section('content')
<form method="post" action="{{ url('admin/vtu/fetch-categories') }}" class="mb-3">
    @csrf
    <button type="submit" class="btn btn-outline-secondary btn-sm">Fetch categories from provider</button>
</form>

<form method="post" action="{{ url('admin/vtu') }}">
    @csrf

    <div class="card mb-4">
        <div class="card-header">Module &amp; Provider</div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input type="hidden" name="provider_vtu_enabled" value="0">
                <input class="form-check-input" type="checkbox" name="provider_vtu_enabled" value="1" id="provider_vtu_enabled"
                    {{ $vtuEnabled ? 'checked' : '' }}>
                <label class="form-check-label" for="provider_vtu_enabled"><strong>VTU module enabled</strong> (master switch)</label>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold">SprintPay API Key (WEBKEY)</label>
                    <input class="form-control form-control-sm" type="password" name="WEBKEY"
                        placeholder="{{ $sprintpayKeys['WEBKEY'] ? '•••••••• (saved)' : 'Enter API key' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold">API Base URL</label>
                    <input class="form-control form-control-sm" type="text" name="SPRINTPAY_API_BASE"
                        value="{{ $sprintpayKeys['SPRINTPAY_API_BASE'] }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-semibold">PalmPay Key</label>
                    <input class="form-control form-control-sm" type="password" name="PALMPAYKEY"
                        placeholder="{{ $sprintpayKeys['PALMPAYKEY'] ? '•••••••• (saved)' : 'Optional' }}">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($vtuServices as $slug => $svc)
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $svc['label'] }}</span>
                    <span class="badge {{ $svc['enabled'] ? 'badge-on' : 'badge-off' }}">{{ $svc['enabled'] ? 'ON' : 'OFF' }}</span>
                </div>
                <div class="card-body">
                    <input type="hidden" name="{{ $svc['enabled_key'] }}" value="0">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="{{ $svc['enabled_key'] }}" value="1" id="{{ $svc['enabled_key'] }}"
                            {{ $svc['enabled'] ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $svc['enabled_key'] }}">Service enabled</label>
                    </div>
                    <label class="form-label small fw-semibold">Category ID</label>
                    <input class="form-control form-control-sm" type="text" name="{{ $svc['category_key'] }}"
                        value="{{ $svc['category_id'] }}" placeholder="SprintPay category ID">
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary">Save VTU Settings</button>
</form>

@if(session('parsedCategories') && count(session('parsedCategories')))
<div class="card mt-4 border-primary">
    <div class="card-header bg-light">
        <strong>SprintPay categories</strong> — copy the ID into the matching service card above
    </div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Service name</th><th>Category ID (copy this)</th></tr></thead>
            <tbody>
                @foreach(session('parsedCategories') as $cat)
                <tr>
                    <td>{{ $cat['name'] }}</td>
                    <td><code>{{ $cat['id'] }}</code></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if(session('remoteCategories'))
<div class="card mt-3">
    <div class="card-header">Raw API response (reference)</div>
    <div class="card-body p-0">
        <pre class="mb-0 p-3 small" style="max-height:300px;overflow:auto;">{{ json_encode(session('remoteCategories'), JSON_PRETTY_PRINT) }}</pre>
    </div>
</div>
@endif
@endsection
