@extends('esim.layout')

@section('esim-body')
<form method="get" action="{{ route('esim.index') }}" class="esim-filters" id="esim-filters">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label" for="esim-country">Country</label>
            <select name="country" id="esim-country" class="form-select">
                <option value="">All countries</option>
                @foreach($countries as $code => $name)
                    <option value="{{ $code }}" @selected($filters['country'] === $code)>
                        {{ $name }} ({{ $code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label" for="esim-duration">Duration</label>
            <select name="duration" id="esim-duration" class="form-select">
                <option value="">Any duration</option>
                @foreach($durations as $days => $label)
                    <option value="{{ $days }}" @selected((string) $filters['duration'] === (string) $days)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label" for="esim-type">Plan type</label>
            <select name="type" id="esim-type" class="form-select">
                <option value="data" @selected($filters['type'] === 'data')>Data only</option>
                <option value="phone" @selected($filters['type'] === 'phone')>Phone plans</option>
                <option value="all" @selected($filters['type'] === 'all')>All plans</option>
            </select>
        </div>
        <div class="col-12 col-md-2">
            <div class="esim-filters__actions">
                <button type="submit" class="esim-btn flex-grow-1">Apply</button>
                @if($filters['country'] !== '' || $filters['duration'] !== '' || ($filters['type'] ?? 'data') !== 'data')
                    <a href="{{ route('esim.index') }}" class="esim-btn esim-btn-ghost" title="Clear filters">Clear</a>
                @endif
            </div>
        </div>
    </div>
</form>

@if($loadError)
<div class="card esim-empty">
    <div class="card-body text-center py-5 px-4">
        <div class="esim-empty__icon"><i class="fas fa-sim-card"></i></div>
        <h3 class="h6 fw-bold mb-2">Esim unavailable</h3>
        <p class="text-muted mb-0 small">{{ $loadError }}</p>
    </div>
</div>
@elseif(empty($packages))
<div class="card esim-empty">
    <div class="card-body text-center py-5 px-4">
        <div class="esim-empty__icon"><i class="ti ti-world-search"></i></div>
        <h3 class="h6 fw-bold mb-2">No packages found</h3>
        <p class="text-muted mb-3 small">Try another country, duration, or plan type.</p>
        <a href="{{ route('esim.index') }}" class="esim-btn esim-btn-ghost">Reset filters</a>
    </div>
</div>
@else
<div class="esim-results-meta">
    <h3>
        @if($filters['country'] !== '' && isset($countries[$filters['country']]))
            {{ $countries[$filters['country']] }}
        @else
            Available packages
        @endif
        @if($filters['duration'] !== '')
            · {{ $filters['duration'] }} {{ (int) $filters['duration'] === 1 ? 'day' : 'days' }}
        @endif
    </h3>
    <span>{{ count($packages) }} plan{{ count($packages) === 1 ? '' : 's' }}</span>
</div>

<div class="row g-3">
    @foreach($packages as $pkg)
    <div class="col-6 col-lg-3 col-md-4">
        <div class="esim-package">
            <div class="esim-package__top">
                <div class="esim-package__icon"><i class="fas fa-sim-card"></i></div>
                <div class="min-w-0">
                    <div class="esim-package__name">{{ $pkg['name'] }}</div>
                    <div class="esim-package__location">
                        {{ $pkg['location_name'] ?: ($pkg['location'] ?: 'Multi-country') }}
                    </div>
                </div>
            </div>

            <div class="esim-chips">
                <span class="esim-chip esim-chip--data">
                    {{ rtrim(rtrim(number_format($pkg['volume_gb'], 2), '0'), '.') }} GB
                </span>
                @if($pkg['duration_days'])
                    <span class="esim-chip esim-chip--days">{{ $pkg['duration_days'] }} days</span>
                @endif
                @if($pkg['speed'])
                    <span class="esim-chip esim-chip--speed">{{ $pkg['speed'] }}</span>
                @endif
                @if(!empty($pkg['max_privacy']))
                    <span class="esim-chip esim-chip--privacy">Privacy</span>
                @endif
            </div>

            <div class="esim-package__price">
                ₦{{ number_format($pkg['price_ngn'], 2) }}
                <small>one-time</small>
            </div>

            <form method="post" action="{{ route('esim.purchase') }}" class="mt-1" onsubmit="return confirm('Buy this package for ₦{{ number_format($pkg['price_ngn'], 2) }}?');">
                @csrf
                <input type="hidden" name="package_code" value="{{ $pkg['package_code'] }}">
                <input type="hidden" name="price_ngn" value="{{ $pkg['price_ngn'] }}">
                <button type="submit" class="esim-btn w-100">Buy now</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

@push('page-scripts')
<script>
(function () {
    var form = document.getElementById('esim-filters');
    if (!form) return;
    form.querySelectorAll('select').forEach(function (el) {
        el.addEventListener('change', function () { form.submit(); });
    });
})();
</script>
@endpush
@endsection
