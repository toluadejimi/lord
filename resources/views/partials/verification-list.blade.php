<div class="card"><div class="card-body">
    <h5>Orders</h5>
    <table class="table table-sm">
        <tr><th>Phone</th><th>Service</th><th>SMS</th><th>Status</th></tr>
        @foreach($verifications as $v)
        <tr>
            <td><code>{{ $v->phone }}</code></td>
            <td>{{ $v->service }}</td>
            <td id="sms-{{ $v->id }}">{{ $v->sms ?? 'waiting...' }}</td>
            <td>{{ $v->status == 2 ? 'Done' : ($v->status == 99 ? 'Cancelled' : 'Pending') }}</td>
        </tr>
        @endforeach
    </table>
</div></div>
@if(isset($pollUrl))
<script>
@foreach($verifications->where('status', 1) as $v)
setInterval(function(){
    fetch('{{ url($pollUrl) }}?num={{ $v->phone }}').then(r=>r.json()).then(d=>{
        if(d.message && d.message !== 'waiting for sms') document.getElementById('sms-{{ $v->id }}').innerText = d.message;
    });
}, 5000);
@endforeach
</script>
@endif
