<div class="card"><div class="card-body">
    <h5>Orders</h5>
    <table class="table table-sm">
        <tr><th>Phone</th><th>Service</th><th>SMS</th><th>Status</th></tr>
        @foreach($verifications as $v)
        <tr data-verification-row="{{ $v->id }}" data-verification-status="{{ $v->status }}">
            <td><code>{{ $v->phone }}</code></td>
            <td>{{ $v->service }}</td>
            <td id="sms-{{ $v->id }}">{{ $v->sms ?? 'waiting...' }}</td>
            <td id="status-{{ $v->id }}">{{ $v->status == 2 ? 'Done' : ($v->status == 99 ? 'Cancelled' : 'Pending') }}</td>
        </tr>
        @endforeach
    </table>
</div></div>
@if(isset($pollUrl))
<script>
(function () {
    const pollUrl = @json(url($pollUrl));
    const queue = @json(
        $verifications->where('status', 1)->map(fn ($v) => ['id' => $v->id, 'phone' => $v->phone])->values()
    );

    if (!queue.length) return;

    let waitMs = 10000;
    let running = false;

    function pollPhone(item) {
        return fetch(pollUrl + '?num=' + encodeURIComponent(item.phone))
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.next_poll_seconds) {
                    waitMs = Math.max(8000, d.next_poll_seconds * 1000);
                }
                if (d.message && d.message !== 'waiting for sms') {
                    const smsEl = document.getElementById('sms-' + item.id);
                    if (smsEl) smsEl.innerText = d.message;
                }
                if (Number(d.status) === 2) {
                    const statusEl = document.getElementById('status-' + item.id);
                    if (statusEl) statusEl.innerText = 'Done';
                    const idx = queue.findIndex(function (q) { return q.id === item.id; });
                    if (idx >= 0) queue.splice(idx, 1);
                }
            })
            .catch(function () {});
    }

    function runCycle() {
        if (running || !queue.length) {
            if (queue.length) setTimeout(runCycle, waitMs);
            return;
        }

        running = true;
        let chain = Promise.resolve();

        queue.slice().forEach(function (item) {
            chain = chain.then(function () { return pollPhone(item); });
        });

        chain.finally(function () {
            running = false;
            if (queue.length) setTimeout(runCycle, waitMs);
        });
    }

    setTimeout(runCycle, 4000);
})();
</script>
@endif
