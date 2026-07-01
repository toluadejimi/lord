@extends('layout.main')
@section('content')
<style>
.fund-page { --fund-accent: #4f46e5; }
.fund-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #a855f7 100%);
    border-radius: 16px; color: #fff; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
    box-shadow: 0 10px 30px rgba(79, 70, 229, .25);
}
.fund-card {
    border: 0; border-radius: 16px; box-shadow: 0 4px 24px rgba(15, 23, 42, .06);
}
.fund-card .form-control, .fund-card .form-select {
    border-radius: 10px; border-color: #e2e8f0; padding: .65rem .85rem;
}
.fund-card .form-control:focus, .fund-card .form-select:focus {
    border-color: var(--fund-accent); box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.fund-submit {
    border: 0; border-radius: 12px; padding: .85rem 1rem; font-weight: 700;
    background: linear-gradient(135deg, #4f46e5, #7c3aed); box-shadow: 0 8px 20px rgba(79,70,229,.3);
}
.fund-submit:hover { filter: brightness(1.05); }
.fund-table th { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
.fund-table td { font-size: .875rem; vertical-align: middle; }
.fund-badge { font-size: .7rem; font-weight: 700; padding: .3rem .55rem; border-radius: 999px; }
.fund-badge-pending { background: #fef3c7; color: #b45309; }
.fund-badge-done { background: #d1fae5; color: #047857; }
</style>

<div class="pc-container">
    <div class="pc-content p-4 fund-page">
        <div class="fund-hero d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h2 class="h4 mb-1"><i class="ti ti-wallet me-1 opacity-75"></i> Fund Wallet</h2>
                <p class="mb-0 small opacity-90">Add money instantly via card or bank transfer</p>
            </div>
            <div class="text-end">
                <div class="small opacity-75">Current balance</div>
                <div class="h4 mb-0 fw-bold">₦{{ number_format((float) Auth::user()->wallet, 2) }}</div>
                <a href="{{ route('wallet.transactions') }}" class="small text-white text-decoration-underline opacity-90">View all transactions</a>
            </div>
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
                <div class="card fund-card h-100">
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

                            <button type="submit" class="btn btn-primary fund-submit w-100 text-white">
                                Continue to payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card fund-card h-100">
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
