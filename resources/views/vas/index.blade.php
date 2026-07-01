@extends('layout.main')
@section('content')
<div class="pc-container">
    <div class="pc-content p-4">
        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h2 class="mb-1">Bills &amp; VTU</h2>
                <p class="text-muted mb-0 small">Pay airtime, data, cable, and electricity from your wallet via SprintPay.</p>
            </div>
            <div class="text-end">
                <div class="text-muted small">Wallet balance</div>
                <div class="h4 mb-0 text-primary">₦{{ number_format($wallet, 2) }}</div>
                <a href="{{ url('fund-wallet') }}" class="small">Fund wallet</a>
            </div>
        </div>

        @if(!$vasConfigured)
            <div class="alert alert-warning">
                VTU is not fully configured. Admin must set <strong>WEBKEY</strong> and <strong>SPRINTPAY_WEBHOOK_SECRET</strong>.
            </div>
        @endif

        @include('vas.partials.subnav')

        @if(empty($services))
            <div class="card card-body text-center py-5">
                <h5>No VTU services available</h5>
                <p class="text-muted mb-0">Please check back later or contact support.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach($services as $service)
                <div class="col-md-6 col-xl-3">
                    <a href="{{ $service['url'] }}" class="text-decoration-none text-dark">
                        <div class="card h-100 vtu-service-card">
                            <div class="card-body">
                                <span class="vtu-icon-wrap d-inline-flex mb-3"><i class="ti {{ $service['icon'] ?? 'ti-receipt' }}"></i></span>
                                <h5 class="mb-2">{{ $service['label'] }}</h5>
                                <p class="text-muted small mb-0">{{ $service['description'] ?? '' }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
.vtu-service-card { transition: transform .15s ease, box-shadow .15s ease; border: 1px solid rgba(0,0,0,.08); }
.vtu-service-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
.vtu-icon-wrap {
    width: 48px; height: 48px; border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff; align-items: center; justify-content: center; font-size: 1.25rem;
}
</style>
@endsection
