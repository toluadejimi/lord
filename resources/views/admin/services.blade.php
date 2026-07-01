@extends('admin.layout', ['adminActive' => 'services'])

@section('title', 'SMS Services')
@section('page-title', 'SMS Services')
@section('page-subtitle', 'Server 1–4 match the user menu. Each card shows the menu name and upstream provider.')

@section('content')
<div class="row">
    @foreach ($services as $groupKey => $service)
    @php
        $enabledKey = collect($service['keys'])->keys()->first(fn ($k) => str_ends_with($k, '_enabled'));
        $isOn = $enabledKey && in_array(strtolower((string)($service['keys'][$enabledKey]['value'] ?? '')), ['1', 'true', 'yes', 'on']);
    @endphp
    <div class="col-lg-6 mb-4">
        <div class="card service-card {{ $isOn ? '' : 'disabled' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas {{ $service['icon'] ?? 'fa-satellite-dish' }} mr-2"></i>{{ $service['label'] }}
                </span>
                <span class="badge {{ $isOn ? 'badge-on' : 'badge-off' }}">{{ $isOn ? 'ON' : 'OFF' }}</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    @if(!empty($service['provider']))
                    <div class="small"><span class="text-muted">Provider:</span> <strong>{{ $service['provider'] }}</strong></div>
                    @endif
                    @if(!empty($service['config_label']) && ($service['config_label'] ?? '') !== ($service['label'] ?? ''))
                    <div class="small text-muted">{{ $service['config_label'] }}</div>
                    @endif
                    @if(!empty($service['user_route']))
                    <div class="small text-muted">User page: <code>{{ $service['user_route'] }}</code></div>
                    @endif
                </div>
                <p class="text-muted small mb-3">{{ $service['description'] ?? '' }}</p>

                <form method="post" action="{{ url('admin/services') }}">
                    @csrf
                    <input type="hidden" name="service_group" value="{{ $groupKey }}">

                    @foreach ($service['keys'] as $configKey => $meta)
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ $meta['label'] }}</label>
                        @if(($meta['type'] ?? 'text') === 'boolean')
                            <div class="form-check">
                                <input type="hidden" name="{{ $configKey }}" value="0">
                                <input class="form-check-input" type="checkbox" name="{{ $configKey }}" value="1" id="{{ $groupKey }}_{{ $configKey }}"
                                    {{ in_array(strtolower((string)($meta['value'] ?? '')), ['1','true','yes','on']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $groupKey }}_{{ $configKey }}">Enabled</label>
                            </div>
                        @elseif(($meta['type'] ?? '') === 'textarea')
                            <textarea class="form-control form-control-sm" name="{{ $configKey }}" rows="2">{{ $meta['value'] }}</textarea>
                        @else
                            <input class="form-control form-control-sm" type="{{ ($meta['type'] ?? '') === 'password' ? 'password' : 'text' }}"
                                name="{{ $configKey }}" value="{{ ($meta['type'] ?? '') === 'password' ? '' : $meta['value'] }}"
                                placeholder="{{ ($meta['type'] ?? '') === 'password' && $meta['value'] ? '•••••••• (saved)' : '' }}">
                        @endif
                    </div>
                    @endforeach

                    @if($service['setting'] ?? null)
                    <hr>
                    <h6 class="small text-uppercase text-muted mb-2">Pricing (Rate × USD + Margin NGN)</h6>
                    <input type="hidden" name="pricing_enabled" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="pricing_enabled" value="1" id="pricing_{{ $groupKey }}"
                            {{ ($service['setting']->is_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pricing_{{ $groupKey }}">Pricing enabled</label>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <input class="form-control form-control-sm" name="rate" placeholder="Rate" value="{{ $service['setting']->rate ?? 0 }}">
                        </div>
                        <div class="col-6">
                            <input class="form-control form-control-sm" name="margin" placeholder="Margin NGN" value="{{ $service['setting']->margin ?? 0 }}">
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary btn-sm mt-3">Save {{ $service['label'] }}</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
