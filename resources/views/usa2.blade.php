@extends('layout.main')
@section('content')
<div class="pc-container"><div class="pc-content p-4">
    @if(session('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
    <h2>USA Server 2 — Unlimited Portal</h2>
    <p class="text-muted">Rent US numbers for SMS verification.</p>

    <div class="card mb-4"><div class="card-body">
        <form method="post" action="{{ url('order-usa2') }}">@csrf
            <label>Service ID</label>
            <input class="form-control mb-2" name="service" placeholder="e.g. whatsapp" required>
            <label>Provider USD cost (estimate)</label>
            <input class="form-control mb-2" name="api_cost" type="number" step="0.01" value="1">
            <label>Area code (optional)</label>
            <input class="form-control mb-2" name="area_code">
            <button class="btn btn-primary">Rent Number</button>
        </form>
    </div></div>

    <div class="card"><div class="card-body">
        <h5>Recent orders</h5>
        <table class="table table-sm">
            <tr><th>Phone</th><th>Service</th><th>SMS</th><th>Status</th></tr>
            @foreach($verifications as $v)
            <tr>
                <td><code>{{ $v->phone }}</code></td>
                <td>{{ $v->service }}</td>
                <td id="sms-{{ $v->phone }}">{{ $v->sms ?? 'waiting...' }}</td>
                <td>{{ $v->status == 2 ? 'Done' : 'Pending' }}</td>
            </tr>
            @endforeach
        </table>
    </div></div>
</div></div>
<script>
@foreach($verifications->where('status', 1) as $v)
setInterval(function(){
    fetch('{{ url('get-smscode-usa2') }}?num={{ $v->phone }}').then(r=>r.json()).then(d=>{
        if(d.message && d.message !== 'waiting for sms') document.getElementById('sms-{{ $v->phone }}').innerText = d.message;
    });
}, 5000);
@endforeach
</script>
@endsection
