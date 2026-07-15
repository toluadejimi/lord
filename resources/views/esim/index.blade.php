@extends('esim.layout')

@section('esim-body')
<form method="get" action="{{ route('esim.index') }}" class="esim-filters row g-2 mb-4">
    <div class="col-6 col-md-3">
        <label class="form-label small fw-semibold mb-1">Country</label>
        <input type="text" name="country" class="form-control form-control-sm" placeholder="e.g. US, JP, GB" value="{{ $filters['country'] }}" maxlength="8">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small fw-semibold mb-1">Type</label>
        <select name="type" class="form-select form-select-sm">
            <option value="data" @selected($filters['type'] === 'data')>Data</option>
            <option value="phone" @selected($filters['type'] === 'phone')>Phone plans</option>
            <option value="all" @selected($filters['type'] === 'all')>All</option>
        </select>
    </div>
    <div class="col-12 col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
        @if($filters['country'] !== '')
            <a href="{{ route('esim.index') }}" class="btn btn-link btn-sm">Clear</a>
        @endif
    </div>
</form>

@if($loadError)
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="ti ti-sim-card fs-1 text-muted mb-3 d-block"></i>
        <p class="text-muted mb-0">{{ $loadError }}</p>
    </div>
</div>
@elseif(empty($packages))
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <p class="text-muted mb-0">No packages found. Try another country or check back later.</p>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($packages as $pkg)
    <div class="col-6 col-md-4 col-lg-3">
        <div class="esim-package">
            <div class="esim-package__name">{{ $pkg['name'] }}</div>
            <div class="esim-package__meta">
                @if($pkg['location'])
                    {{ $pkg['location'] }} ·
                @endif
                {{ rtrim(rtrim(number_format($pkg['volume_gb'], 2), '0'), '.') }} GB
                @if($pkg['duration_days'])
                    · {{ $pkg['duration_days'] }} days
                @endif
            </div>
            @if($pkg['speed'])
                <div class="esim-package__meta">{{ $pkg['speed'] }}</div>
            @endif
            <div class="esim-package__price">₦{{ number_format($pkg['price_ngn'], 2) }}</div>
            <form method="post" action="{{ route('esim.purchase') }}" class="mt-2" onsubmit="return confirm('Buy this package for ₦{{ number_format($pkg['price_ngn'], 2) }}?');">
                @csrf
                <input type="hidden" name="package_code" value="{{ $pkg['package_code'] }}">
                <input type="hidden" name="price_ngn" value="{{ $pkg['price_ngn'] }}">
                <button type="submit" class="btn btn-sm btn-primary w-100">Buy</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
