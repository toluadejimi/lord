@extends('layout.main')
@section('content')
<div class="pc-container">
    <div class="pc-content p-4">
        @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        <div class="mb-3">
            <a href="{{ route('vas.index') }}" class="text-muted small"><i class="ti ti-arrow-left"></i> Bills &amp; VTU</a>
        </div>

        <h2 class="mb-1">Airtime</h2>
        <p class="text-muted small mb-3">Top up any Nigerian mobile line from your wallet.</p>

        @include('vas.partials.subnav')

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <form method="post" action="{{ route('vas.airtime.buy') }}" class="card border-0 shadow-sm" id="vas-form">
                    @csrf
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Network</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">Select network</option>
                                @foreach($networks as $network)
                                <option value="{{ $network['id'] }}" @selected(old('service_id') === $network['id'])>{{ $network['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone number</label>
                            <input class="form-control phone-input" name="phone" type="tel" inputmode="numeric"
                                maxlength="11" pattern="[0-9]{11}" placeholder="08012345678"
                                value="{{ old('phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Amount (₦)</label>
                            <input class="form-control form-control-lg amount-input" name="amount" type="number"
                                min="50" max="100000" step="1" value="{{ old('amount') }}" required>
                            <div class="form-text">Min ₦50. Airtel max ₦10,000 per transaction.</div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach([100, 200, 500, 1000, 2000, 5000] as $preset)
                                <button type="button" class="btn btn-sm btn-outline-primary amount-preset" data-amount="{{ $preset }}">₦{{ number_format($preset) }}</button>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100" @disabled(!$vasConfigured)>
                            Pay from wallet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('vas.partials.form-js')
@endsection
