@extends('layout.main')
@section('content')
<div class="pc-container"><div class="pc-content p-4">
    @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <h2>World Server 2 — HeroSMS</h2>
    <form method="post" action="{{ url('order-world-hero') }}" class="card card-body mb-4">@csrf
        <input class="form-control mb-2" name="service" placeholder="Service code" required>
        <input class="form-control mb-2" name="country" placeholder="Country code (optional)">
        <input class="form-control mb-2" name="api_cost" type="number" step="0.01" value="1" placeholder="USD cost">
        <input class="form-control mb-2" name="max_price" placeholder="Max price tier (optional)">
        <button class="btn btn-primary">Order Number</button>
    </form>
    @include('partials.verification-list', ['verifications' => $verifications, 'pollUrl' => 'get-smscode-hero'])
</div></div>
@endsection
