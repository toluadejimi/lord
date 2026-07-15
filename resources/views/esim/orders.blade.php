@extends('esim.layout')

@section('esim-body')
@if($orders->isEmpty())
<div class="card esim-empty">
    <div class="card-body text-center py-5 px-4">
        <div class="esim-empty__icon"><i class="ti ti-package-off"></i></div>
        <h3 class="h6 fw-bold mb-2">No Esim orders yet</h3>
        <p class="text-muted mb-3 small">Browse packages and buy your first eSIM.</p>
        <a href="{{ route('esim.index') }}" class="esim-btn">Browse packages</a>
    </div>
</div>
@else
<div class="esim-results-meta">
    <h3>My orders</h3>
    <span>{{ $orders->count() }} recent</span>
</div>
<div class="d-flex flex-column gap-3">
    @foreach($orders as $order)
    <div class="card esim-order">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                <div>
                    <div class="fw-bold">{{ $order->package_name }}</div>
                    <div class="small text-muted">{{ $order->ref_id }} · {{ $order->created_at?->format('M j, Y g:ia') }}</div>
                </div>
                <span class="esim-status esim-status--{{ $order->status }} align-self-start">{{ $order->status }}</span>
            </div>

            <div class="esim-chips mb-2">
                <span class="esim-chip">₦{{ number_format($order->amount_ngn, 2) }}</span>
                @if($order->location)
                    <span class="esim-chip">{{ $order->location }}</span>
                @endif
                @if($order->volume_gb)
                    <span class="esim-chip esim-chip--data">{{ $order->volume_gb }} GB</span>
                @endif
                @if($order->duration_days)
                    <span class="esim-chip esim-chip--days">{{ $order->duration_days }} days</span>
                @endif
            </div>

            @if($order->status === 'failed' && $order->failure_reason)
                <div class="alert alert-danger py-2 small mb-0">{{ $order->failure_reason }}</div>
            @endif

            @if($order->status === 'processing')
                <p class="small text-muted mb-0">Provisioning… your QR code will show here when ready. Refresh shortly.</p>
            @endif

            @if($order->isCompleted())
                <div class="row g-3 mt-1 align-items-start">
                    @if($order->qr_code_url)
                    <div class="col-auto">
                        <img src="{{ $order->qr_code_url }}" alt="eSIM QR" class="esim-qr">
                    </div>
                    @endif
                    <div class="col">
                        @if($order->iccid)
                        <div class="small mb-2"><span class="text-muted">ICCID</span><br><code>{{ $order->iccid }}</code></div>
                        @endif
                        @if($order->activation_code)
                        <div class="small mb-2"><span class="text-muted">Activation</span><br><code class="user-select-all">{{ $order->activation_code }}</code></div>
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
