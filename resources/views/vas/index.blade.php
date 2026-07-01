@extends('vas.layout', [
    'vasTitle' => 'Bills & VTU',
    'vasSubtitle' => 'Airtime, data, cable TV & electricity from your wallet',
    'vasIcon' => 'ti-receipt',
    'vasShowBack' => false,
    'wallet' => $wallet,
    'vasConfigured' => $vasConfigured,
])

@section('vas-body')
@if(empty($services))
<div class="card vas-card">
    <div class="card-body text-center py-5">
        <i class="ti ti-info-circle fs-1 text-muted mb-3 d-block"></i>
        <h5>No VTU services available</h5>
        <p class="text-muted mb-0">Please check back later or contact support.</p>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($services as $service)
    <div class="col-md-6 col-xl-3">
        <a href="{{ $service['url'] }}" class="text-decoration-none text-dark">
            <div class="card h-100 vtu-service-card">
                <div class="card-body p-4">
                    <span class="vtu-icon-wrap mb-3"><i class="ti {{ $service['icon'] ?? 'ti-receipt' }}"></i></span>
                    <h5 class="mb-2">{{ $service['label'] }}</h5>
                    <p class="text-muted small mb-0">{{ $service['description'] ?? '' }}</p>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endif
@endsection
