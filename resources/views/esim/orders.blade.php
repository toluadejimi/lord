@extends('esim.layout')

@section('esim-body')
@if($orders->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <p class="text-muted mb-3">You have no Esim orders yet.</p>
        <a href="{{ route('esim.index') }}" class="btn btn-primary btn-sm">Browse packages</a>
    </div>
</div>
@else
<div class="d-flex flex-column gap-3">
    @foreach($orders as $order)
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                <div>
                    <div class="fw-bold">{{ $order->package_name }}</div>
                    <div class="small text-muted">{{ $order->ref_id }} · {{ $order->created_at?->format('M j, Y g:ia') }}</div>
                </div>
                <span class="esim-status esim-status--{{ $order->status }} align-self-start">{{ $order->status }}</span>
            </div>
            <div class="small text-muted mb-2">
                ₦{{ number_format($order->amount_ngn, 2) }}
                @if($order->location) · {{ $order->location }}@endif
                @if($order->volume_gb)
                    · {{ $order->volume_gb }} GB
                @endif
                @if($order->duration_days)
                    · {{ $order->duration_days }} days
                @endif
            </div>

            @if($order->status === 'failed' && $order->failure_reason)
                <div class="alert alert-danger py-2 small mb-0">{{ $order->failure_reason }}</div>
            @endif

            @if($order->status === 'processing')
                <p class="small text-muted mb-0">Provisioning… QR code will appear here when ready. Refresh this page in a minute.</p>
            @endif

            @if($order->isCompleted())
                <div class="row g-3 mt-1 align-items-start">
                    @if($order->qr_code_url)
                    <div class="col-auto">
                        <img src="{{ $order->qr_code_url }}" alt="eSIM QR" class="esim-qr border">
                    </div>
                    @endif
                    <div class="col">
                        @if($order->iccid)
                        <div class="small mb-1"><span class="text-muted">ICCID</span><br><code>{{ $order->iccid }}</code></div>
                        @endif
                        @if($order->activation_code)
                        <div class="small mb-1"><span class="text-muted">Activation</span><br><code class="user-select-all">{{ $order->activation_code }}</code></div>
                        @endif
                        @if($order->short_url)
                        <a href="{{ $order->short_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-1">Open install link</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
