@extends('layout.main')
@section('content')
@include('partials.customer-page-styles')
<style>
.tx-stat-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: .75rem; margin-bottom: 1.25rem; }
@media (min-width: 576px) { .tx-stat-grid { grid-template-columns: repeat(3, 1fr); } }
.tx-stat {
    background: #fff; border-radius: 14px; padding: 1rem 1.1rem;
    box-shadow: 0 4px 20px rgba(15, 23, 42, .06); border: 1px solid #eef2f7;
}
.tx-stat .label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
.tx-stat .value { font-size: 1.25rem; font-weight: 800; margin-top: .25rem; }
.tx-stat.credit .value { color: #059669; }
.tx-stat.debit .value { color: #dc2626; }
.tx-stat.wallet .value { color: var(--cp-accent); }
.tx-table th { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; white-space: nowrap; }
.tx-table td { vertical-align: middle; font-size: .875rem; }
.tx-ref { font-family: ui-monospace, monospace; font-size: .75rem; color: #64748b; }
.tx-amount-credit { color: #059669; font-weight: 700; }
.tx-amount-debit { color: #dc2626; font-weight: 700; }
.tx-badge { font-size: .7rem; font-weight: 700; padding: .3rem .55rem; border-radius: 999px; }
.tx-badge-success { background: #d1fae5; color: #047857; }
.tx-badge-warning { background: #fef3c7; color: #b45309; }
.tx-badge-danger { background: #fee2e2; color: #b91c1c; }
.tx-badge-secondary { background: #f1f5f9; color: #475569; }
.tx-badge-info { background: #dbeafe; color: #1d4ed8; }
.tx-dir-credit { background: #ecfdf5; color: #047857; }
.tx-dir-debit { background: #fef2f2; color: #b91c1c; }
</style>

<div class="pc-container">
    <div class="pc-content p-4 cp-page tx-page">
        <div class="cp-hero">
            <div class="cp-hero__main">
                <h2 class="h4"><i class="ti ti-list-details me-1 opacity-75"></i> Wallet Transactions</h2>
                <p class="cp-hero__subtitle">Track every credit and debit on your account</p>
            </div>
            @include('partials.customer-wallet-card', [
                'wallet' => $stats['wallet'],
                'label' => 'Current balance',
            ])
        </div>

        <div class="tx-stat-grid">
            <div class="tx-stat wallet">
                <div class="label">Wallet balance</div>
                <div class="value">₦{{ number_format($stats['wallet'], 2) }}</div>
            </div>
            <div class="tx-stat credit">
                <div class="label">Total credited</div>
                <div class="value">₦{{ number_format($stats['total_credit'], 2) }}</div>
            </div>
            <div class="tx-stat debit">
                <div class="label">Total debited</div>
                <div class="value">₦{{ number_format($stats['total_debit'], 2) }}</div>
            </div>
        </div>

        <nav class="cp-subnav" aria-label="Filter transactions">
            <a href="{{ url('wallet-transactions') }}" class="{{ $filter === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ url('wallet-transactions?filter=credit') }}" class="{{ $filter === 'credit' ? 'active' : '' }}">Credits</a>
            <a href="{{ url('wallet-transactions?filter=debit') }}" class="{{ $filter === 'debit' ? 'active' : '' }}">Debits</a>
        </nav>

        <div class="card cp-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 tx-table">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Reference</th>
                                <th>Type</th>
                                <th>Direction</th>
                                <th>Amount</th>
                                <th>Balance after</th>
                                <th class="pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                                @php
                                    $isCredit = \App\Support\TransactionLabels::isCredit((int) $txn->type);
                                    [$statusLabel, $statusTone] = \App\Support\TransactionLabels::statusBadge((int) $txn->status);
                                @endphp
                                <tr>
                                    <td class="ps-3 text-nowrap">
                                        {{ $txn->created_at ? $txn->created_at->format('d M Y, H:i') : '—' }}
                                    </td>
                                    <td><span class="tx-ref">{{ $txn->ref_id }}</span></td>
                                    <td>{{ \App\Support\TransactionLabels::typeLabel((int) $txn->type) }}</td>
                                    <td>
                                        <span class="tx-badge {{ $isCredit ? 'tx-dir-credit' : 'tx-dir-debit' }}">
                                            {{ $isCredit ? 'Credit' : 'Debit' }}
                                        </span>
                                    </td>
                                    <td class="{{ $isCredit ? 'tx-amount-credit' : 'tx-amount-debit' }}">
                                        {{ $isCredit ? '+' : '−' }}₦{{ number_format($txn->amount, 2) }}
                                    </td>
                                    <td>
                                        @if($txn->balance !== null)
                                            ₦{{ number_format($txn->balance, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="pe-3">
                                        <span class="tx-badge tx-badge-{{ $statusTone }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="ti ti-receipt-off d-block fs-2 mb-2 opacity-50"></i>
                                        No transactions yet.
                                        <a href="{{ url('fund-wallet') }}" class="d-block mt-2">Fund your wallet</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                    <div class="p-3 border-top">{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
