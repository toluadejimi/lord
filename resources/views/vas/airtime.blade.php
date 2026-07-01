@extends('vas.layout', [
    'vasTitle' => 'Airtime',
    'vasSubtitle' => 'Instant top-up for MTN, Glo, Airtel & 9mobile',
    'vasIcon' => 'ti-phone',
    'wallet' => $wallet,
    'vasConfigured' => $vasConfigured,
])

@section('vas-body')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <form method="post" action="{{ route('vas.airtime.buy') }}" class="card vas-card" id="vas-form">
            @csrf
            <div class="card-body">
                @include('vas.partials.network-picker', ['networks' => $networks])

                <div class="mb-4">
                    <div class="vas-field-label">Phone number</div>
                    <input class="form-control vas-input phone-input" name="phone" type="tel" inputmode="numeric"
                        maxlength="11" placeholder="08012345678" value="{{ old('phone') }}" required>
                    <div class="form-text">11-digit Nigerian mobile number</div>
                </div>

                <div class="mb-4">
                    <div class="vas-field-label">Amount</div>
                    <div class="input-group mb-2">
                        <span class="input-group-text border-end-0 bg-white">₦</span>
                        <input class="form-control vas-input amount-input border-start-0 ps-0" name="amount" type="number"
                            min="50" max="100000" step="1" value="{{ old('amount') }}" placeholder="500" required>
                    </div>
                    <div class="form-text mb-2">Min ₦50 · Airtel max ₦10,000 per transaction</div>
                    <div class="amount-chips">
                        @foreach([100, 200, 500, 1000, 2000, 5000] as $preset)
                        <button type="button" class="amount-chip amount-preset" data-amount="{{ $preset }}">₦{{ number_format($preset) }}</button>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary vas-submit btn-lg w-100 text-white" @disabled(!$vasConfigured)>
                    Pay from wallet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
