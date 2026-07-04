@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
@include('vas.partials.styles')
<div class="pc-container">
    <div class="pc-content p-4 cp-page vas-page">
        @if(session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="cp-hero vas-hero">
            <div class="cp-hero__main">
                @if($vasShowBack ?? true)
                <a href="{{ route('vas.index') }}" class="cp-hero__back vas-back">
                    <i class="ti ti-arrow-left"></i> Bills &amp; VTU
                </a>
                @endif
                <h2 class="h4">
                    @if(!empty($vasIcon))<i class="ti {{ $vasIcon }} me-1 opacity-75"></i>@endif
                    {{ $vasTitle }}
                </h2>
                @if(!empty($vasSubtitle))
                <p class="cp-hero__subtitle">{{ $vasSubtitle }}</p>
                @endif
            </div>
            @include('partials.customer-wallet-card', [
                'wallet' => $wallet,
                'secondaryUrl' => url('wallet-transactions'),
                'secondaryLabel' => 'History',
            ])
        </div>

        @if(!$vasConfigured)
        <div class="alert alert-warning border-0 shadow-sm mb-3">
            VTU is not fully configured. Admin must set <strong>WEBKEY</strong> and <strong>SPRINTPAY_WEBHOOK_SECRET</strong>.
        </div>
        @endif

        <nav class="cp-subnav vas-subnav">
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
