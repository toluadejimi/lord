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
[data-pc-theme="dark"] .tx-stat { background: #1e293b; border-color: #334155; }
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

        <nav class="cp-subnav cp-subnav--scroll" aria-label="Filter transactions">
            @foreach(\App\Support\TransactionLabels::customerFilters() as $key => $label)
            <a href="{{ url('wallet-transactions?filter='.$key) }}" class="{{ $filter === $key ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="cp-activity-card">
            @forelse($transactions as $txn)
                @php
                    $isCredit = \App\Support\TransactionLabels::isCredit((int) $txn->type);
                    [$statusLabel, $statusTone] = \App\Support\TransactionLabels::statusBadge((int) $txn->status);
                    $category = \App\Support\TransactionLabels::categoryFor($txn);
                @endphp
                <div class="cp-activity-row">
                    <div>
                        <div class="cp-activity-title">{{ \App\Support\TransactionLabels::typeLabel((int) $txn->type) }}</div>
                        <div class="cp-activity-meta">
                            {{ \App\Support\TransactionLabels::categoryLabel($category) }}
                            · {{ $txn->ref_id }}
                            · {{ $txn->created_at ? $txn->created_at->diffForHumans() : '—' }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="{{ $isCredit ? 'cp-amount-credit' : 'cp-amount-debit' }}">
                            {{ $isCredit ? '+' : '−' }}₦{{ number_format($txn->amount, 2) }}
                        </div>
                        @if($txn->balance !== null)
                        <div class="cp-activity-meta">Bal ₦{{ number_format($txn->balance, 2) }}</div>
                        @endif
                        <span class="cp-activity-status cp-st-{{ $statusTone }}">{{ $statusLabel }}</span>
                    </div>
                </div>
            @empty
                <div class="cp-activity-empty">
                    <i class="ti ti-receipt-off d-block fs-2 mb-2 opacity-50"></i>
                    No transactions yet.
                    <a href="{{ url('fund-wallet') }}" class="d-block mt-2">Fund your wallet</a>
                </div>
            @endforelse
        </div>

        @if($transactions->hasPages())
            <div class="mt-3">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
