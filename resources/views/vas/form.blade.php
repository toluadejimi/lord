@extends('layout.main')
@section('content')
<div class="pc-container"><div class="pc-content p-4">
    @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <h2>{{ $title }} — VTU</h2>
    <form method="post" action="{{ url('vas/purchase') }}" class="card card-body">@csrf
        <input type="hidden" name="category_id" value="{{ $categoryId }}">
        <label>Amount (NGN)</label>
        <input class="form-control mb-2" name="amount" type="number" min="50" required>
        <label>Phone / Meter / Smartcard</label>
        <input class="form-control mb-2" name="phone">
        <input class="form-control mb-2" name="billersCode" placeholder="Billers code (cable/electricity)">
        @if(!empty($variations))
        <label>Bundle</label>
        <select name="variation_code" class="form-control mb-2">
            @foreach($variations as $v)
            <option value="{{ $v['variation_code'] ?? $v['code'] ?? '' }}">{{ $v['name'] ?? 'Bundle' }}</option>
            @endforeach
        </select>
        @endif
        <button class="btn btn-primary">Purchase with Wallet</button>
    </form>
</div></div>
@endsection
