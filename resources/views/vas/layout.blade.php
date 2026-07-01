@extends('layout.main')
@section('content')
@include('vas.partials.styles')
<div class="pc-container">
    <div class="pc-content p-4 vas-page">
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="vas-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                @if($vasShowBack ?? true)
                <a href="{{ route('vas.index') }}" class="vas-back d-inline-block mb-2">
                    <i class="ti ti-arrow-left"></i> Bills &amp; VTU
                </a>
                @endif
                <h2 class="h4 mb-1">
                    @if(!empty($vasIcon))<i class="ti {{ $vasIcon }} me-1 opacity-75"></i>@endif
                    {{ $vasTitle }}
                </h2>
                @if(!empty($vasSubtitle))
                <p class="mb-0 small opacity-90">{{ $vasSubtitle }}</p>
                @endif
            </div>
            <div class="vas-wallet-pill">
                <div class="small opacity-75">Wallet</div>
                <div class="h5 mb-0 fw-bold">₦{{ number_format($wallet, 2) }}</div>
                <a href="{{ url('fund-wallet') }}" class="small text-white text-decoration-underline opacity-90">Fund wallet</a>
            </div>
        </div>

        @if(!$vasConfigured)
        <div class="alert alert-warning border-0 shadow-sm mb-3">
            VTU is not fully configured. Admin must set <strong>WEBKEY</strong> and <strong>SPRINTPAY_WEBHOOK_SECRET</strong>.
        </div>
        @endif

        <nav class="vas-subnav">
            <a href="{{ route('vas.airtime') }}" class="{{ request()->routeIs('vas.airtime*') ? 'active' : '' }}">Airtime</a>
            <a href="{{ route('vas.data') }}" class="{{ request()->routeIs('vas.data*') ? 'active' : '' }}">Data</a>
            <a href="{{ route('vas.cable') }}" class="{{ request()->routeIs('vas.cable*') ? 'active' : '' }}">Cable TV</a>
            <a href="{{ route('vas.electricity') }}" class="{{ request()->routeIs('vas.electricity*') ? 'active' : '' }}">Electricity</a>
        </nav>

        @yield('vas-body')
    </div>
</div>
@push('page-scripts')
@include('vas.partials.form-js')
@endpush
@endsection
