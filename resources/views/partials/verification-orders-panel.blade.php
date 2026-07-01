@php
    $showServer = $showServerColumn ?? false;
    $panelId = $panelId ?? 'verification-orders-panel';
@endphp

<div class="vo-panel card border-0 shadow-sm" id="{{ $panelId }}">
    <div class="card-body p-0">
        @if(!empty($panelTitle))
            <div class="px-4 pt-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">{{ $panelTitle }}</h5>
                @if(!empty($panelLink))
                    <a href="{{ $panelLink }}" class="small text-decoration-none">View all</a>
                @endif
            </div>
        @endif

        @if($verifications->isEmpty())
            <div class="vo-empty text-center text-muted py-5 px-3">
                <i class="ti ti-inbox d-block mb-2" style="font-size:2rem;opacity:.45;"></i>
                <div class="small">No verification orders yet.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover vo-table mb-0 align-middle">
                    <thead>
                        <tr>
                            @if($showServer)<th>Server</th>@endif
                            <th>Service</th>
                            <th>Number</th>
                            <th>OTP</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verifications as $v)
                            @php
                                $status = (int) $v->status;
                                $isPending = $status === 1;
                                $isDone = $status === 2;
                                $isCancelled = $status === 99;
                            @endphp
                            <tr data-vo-row="{{ $v->id }}" data-vo-status="{{ $status }}">
                                @if($showServer)
                                    <td>
                                        <span class="badge bg-{{ \App\Support\VerificationLabels::providerBadgeClass((int) $v->type) }} vo-server-badge">
                                            {{ \App\Support\VerificationLabels::providerName((int) $v->type) }}
                                        </span>
                                    </td>
                                @endif
                                <td class="text-truncate" style="max-width:110px;" title="{{ $v->service }}">{{ $v->service }}</td>
                                <td>
                                    <button type="button" class="btn btn-link btn-sm p-0 font-monospace vo-copy" data-copy="{{ $v->phone }}" title="Copy number">
                                        {{ $v->phone }}
                                    </button>
                                </td>
                                <td class="vo-sms-cell" data-vo-sms="{{ $v->id }}">
                                    @if($v->sms)
                                        <span class="font-monospace fw-semibold text-success">{{ $v->sms }}</span>
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1 vo-copy" data-copy="{{ $v->sms }}" title="Copy code"><i class="ti ti-copy"></i></button>
                                    @elseif($isPending)
                                        <span class="vo-waiting text-muted small"><span class="spinner-border spinner-border-sm me-1"></span>Waiting…</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-nowrap">₦{{ number_format((float) $v->cost, 2) }}</td>
                                <td data-vo-status-cell="{{ $v->id }}">
                                    @if($isPending)
                                        <span class="vo-badge vo-badge-pending">Pending</span>
                                    @elseif($isDone)
                                        <span class="vo-badge vo-badge-done">Completed</span>
                                    @elseif($isCancelled)
                                        <span class="vo-badge vo-badge-cancelled">Cancelled</span>
                                    @else
                                        <span class="vo-badge vo-badge-muted">{{ $status }}</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if($isPending)
                                        <button type="button" class="btn btn-outline-danger btn-sm vo-cancel-btn" data-vo-cancel="{{ $v->id }}">
                                            Cancel & refund
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
.vo-table thead th {
    font-size: .68rem; text-transform: uppercase; letter-spacing: .04em;
    color: #64748b; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    padding: .65rem 1rem; white-space: nowrap;
}
.vo-table tbody td { padding: .75rem 1rem; font-size: .85rem; border-color: #f1f5f9; }
.vo-server-badge { font-size: .65rem; font-weight: 600; max-width: 130px; white-space: normal; line-height: 1.2; }
.vo-badge { font-size: .68rem; font-weight: 700; padding: .28rem .55rem; border-radius: 999px; display: inline-block; }
.vo-badge-pending { background: #fef3c7; color: #b45309; }
.vo-badge-done { background: #d1fae5; color: #047857; }
.vo-badge-cancelled { background: #f1f5f9; color: #64748b; }
.vo-badge-muted { background: #e2e8f0; color: #475569; }
.vo-cancel-btn { font-size: .72rem; padding: .25rem .55rem; }
</style>

@if($verifications->where('status', 1)->isNotEmpty())
<script>
(function () {
    const panel = document.getElementById(@json($panelId));
    if (!panel) return;

    const csrf = @json(csrf_token());
    const pollBase = @json(url('verification'));
    let waitMs = 10000;
    let running = false;

    const queue = @json(
        $verifications->where('status', 1)->map(fn ($v) => ['id' => $v->id])->values()
    );

    panel.querySelectorAll('.vo-copy').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const text = btn.getAttribute('data-copy') || btn.textContent.trim();
            navigator.clipboard.writeText(text).catch(function () {});
        });
    });

    panel.querySelectorAll('.vo-cancel-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.getAttribute('data-vo-cancel');
            if (!id || !confirm('Cancel this number and refund your wallet?')) return;

            btn.disabled = true;
            btn.textContent = 'Cancelling…';

            fetch(pollBase + '/' + id + '/cancel', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
                .then(function (res) {
                    if (!res.ok) {
                        alert(res.data.message || 'Could not cancel order.');
                        btn.disabled = false;
                        btn.textContent = 'Cancel & refund';
                        return;
                    }

                    const row = panel.querySelector('[data-vo-row="' + id + '"]');
                    if (row) {
                        row.setAttribute('data-vo-status', '99');
                        const statusCell = row.querySelector('[data-vo-status-cell="' + id + '"]');
                        if (statusCell) statusCell.innerHTML = '<span class="vo-badge vo-badge-cancelled">Cancelled</span>';
                        const smsCell = row.querySelector('[data-vo-sms="' + id + '"]');
                        if (smsCell) smsCell.innerHTML = '<span class="text-muted">—</span>';
                        btn.remove();
                    }

                    const idx = queue.findIndex(function (q) { return String(q.id) === String(id); });
                    if (idx >= 0) queue.splice(idx, 1);

                    if (res.data.message) alert(res.data.message);
                })
                .catch(function () {
                    alert('Network error. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Cancel & refund';
                });
        });
    });

    function pollOne(item) {
        return fetch(pollBase + '/' + item.id + '/poll', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.next_poll_seconds) {
                    waitMs = Math.max(8000, d.next_poll_seconds * 1000);
                }

                if (d.sms && d.message !== 'waiting for sms') {
                    const smsCell = panel.querySelector('[data-vo-sms="' + item.id + '"]');
                    if (smsCell) {
                        smsCell.innerHTML = '<span class="font-monospace fw-semibold text-success">' + d.sms + '</span>' +
                            '<button type="button" class="btn btn-link btn-sm p-0 ms-1 vo-copy" data-copy="' + d.sms + '" title="Copy code"><i class="ti ti-copy"></i></button>';
                        smsCell.querySelector('.vo-copy')?.addEventListener('click', function () {
                            navigator.clipboard.writeText(d.sms).catch(function () {});
                        });
                    }
                }

                if (Number(d.status) === 2) {
                    const statusCell = panel.querySelector('[data-vo-status-cell="' + item.id + '"]');
                    if (statusCell) statusCell.innerHTML = '<span class="vo-badge vo-badge-done">Completed</span>';
                    const row = panel.querySelector('[data-vo-row="' + item.id + '"]');
                    const cancelBtn = row?.querySelector('.vo-cancel-btn');
                    if (cancelBtn) cancelBtn.remove();
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
            chain = chain.then(function () { return pollOne(item); });
        });
        chain.finally(function () {
            running = false;
            if (queue.length) setTimeout(runCycle, waitMs);
        });
    }

    setTimeout(runCycle, 3000);
})();
</script>
@endif
