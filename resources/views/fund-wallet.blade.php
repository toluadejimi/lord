@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
<style>
.fund-card .form-control, .fund-card .form-select {
    border-radius: 10px; border-color: #e2e8f0; padding: .65rem .85rem;
}
.fund-card .form-control:focus, .fund-card .form-select:focus {
    border-color: var(--cp-accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--cp-accent) 18%, transparent);
}
.fund-submit {
    border: 0; border-radius: 12px; padding: .85rem 1rem; font-weight: 700;
    background: var(--cp-hero-bg); box-shadow: 0 8px 20px var(--cp-hero-shadow); color: #fff;
}
.fund-submit:hover { filter: brightness(1.05); color: #fff; }
.fund-table th { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
.fund-table td { font-size: .875rem; vertical-align: middle; }
.fund-badge { font-size: .7rem; font-weight: 700; padding: .3rem .55rem; border-radius: 999px; }
.fund-badge-pending { background: #fef3c7; color: #b45309; }
.fund-badge-done { background: #d1fae5; color: #047857; }
</style>

<div class="pc-container">
    <div class="pc-content p-4 cp-page fund-page">
        <div class="cp-hero">
            <div class="cp-hero__main">
                <h2 class="h4"><i class="ti ti-wallet me-1 opacity-75"></i> Fund Wallet</h2>
                <p class="cp-hero__subtitle">Add money instantly via card or bank transfer. Minimum ₦2,000 per top-up.</p>
            </div>
            @include('partials.customer-wallet-card', [
                'showFund' => false,
                'label' => 'Current balance',
                'secondaryUrl' => route('wallet.transactions'),
                'secondaryLabel' => 'All transactions',
            ])
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card cp-card fund-card h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Add funds</h5>
                        <form action="{{ url('fund-now') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Amount (NGN)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">₦</span>
                                    <input type="number" name="amount" class="form-control border-start-0 ps-0"
                                        min="2000" max="100000" step="1" placeholder="e.g. 5000" required>
                                </div>
                                <div class="form-text">Minimum ₦2,000 · Maximum ₦100,000 per transaction</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-muted">Payment method</label>
                                <select name="type" class="form-select">
                                    <option value="1">Instant payment (card / transfer)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn fund-submit w-100">
                                <i class="ti ti-credit-card me-1"></i> Continue to payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card cp-card fund-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Recent funding attempts</h5>
                            <a href="{{ route('wallet.transactions') }}" class="small">See all</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0 fund-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaction as $data)
                                        <tr>
                                            <td><code class="small">{{ $data->ref_id }}</code></td>
                                            <td class="fw-semibold">₦{{ number_format($data->amount, 2) }}</td>
                                            <td>
                                                @if ((int) $data->status === 1)
                                                    <span class="fund-badge fund-badge-pending">Pending</span>
                                                    <a href="{{ url('resolve-page?trx_ref='.$data->ref_id) }}" class="btn btn-link btn-sm p-0 ms-1">Resolve</a>
                                                @elseif ((int) $data->status === 2)
                                                    <span class="fund-badge fund-badge-done">Completed</span>
                                                @else
                                                    <span class="fund-badge fund-badge-pending">Processing</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap text-muted">
                                                {{ $data->created_at ? $data->created_at->format('d M Y, H:i') : '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No funding attempts yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($transaction->hasPages())
                            <div class="mt-3">{{ $transaction->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
