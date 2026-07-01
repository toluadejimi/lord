@extends('admin.layout', ['adminActive' => 'settings'])

@section('title', 'Platform Settings')
@section('page-title', 'Platform Settings')
@section('page-subtitle', 'Wallet funding, security, Telegram alerts, and sitewide banner.')

@section('content')
<form method="post" action="{{ url('admin/settings/keys') }}" class="mb-4">
    @csrf
    <div class="row">
        @foreach($groups as $groupKey => $group)
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header fw-bold">{{ $group['label'] }}</div>
                <div class="card-body">
                    @foreach($group['keys'] as $configKey => $meta)
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ $meta['label'] }}</label>
                        @if(($meta['type'] ?? 'text') === 'boolean')
                            <div class="form-check">
                                <input type="hidden" name="{{ $configKey }}" value="0">
                                <input class="form-check-input" type="checkbox" name="{{ $configKey }}" value="1" id="{{ $configKey }}"
                                    {{ in_array(strtolower((string)($meta['value'] ?? '')), ['1','true','yes','on']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $configKey }}">Enabled</label>
                            </div>
                        @elseif(($meta['type'] ?? '') === 'textarea')
                            <textarea class="form-control form-control-sm" name="{{ $configKey }}" rows="3">{{ $meta['value'] }}</textarea>
                        @else
                            <input class="form-control form-control-sm" type="{{ ($meta['type'] ?? '') === 'password' ? 'password' : 'text' }}"
                                name="{{ $configKey }}" value="{{ ($meta['type'] ?? '') === 'password' ? '' : $meta['value'] }}"
                                placeholder="{{ ($meta['type'] ?? '') === 'password' && $meta['value'] ? '•••••••• (saved)' : '' }}">
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary">Save Platform Settings</button>
</form>

<form method="post" action="{{ url('admin/settings/notification') }}" class="card">
    @csrf
    <div class="card-header fw-bold">Sitewide Notification Banner</div>
    <div class="card-body">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="notif_active"
                {{ ($notification->is_active ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="notif_active">Show banner to users</label>
        </div>
        <input class="form-control mb-2" name="title" placeholder="Title" value="{{ $notification->title ?? '' }}">
        <textarea class="form-control mb-2" name="message" rows="3" placeholder="Message">{{ $notification->message ?? '' }}</textarea>
        <button class="btn btn-primary btn-sm">Save Notification</button>
    </div>
</form>

@include('admin.partials.maintenance')
@endsection
