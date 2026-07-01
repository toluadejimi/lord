@extends('admin.layout', ['adminActive' => 'transactions'])

@section('title', 'Transactions')
@section('page-title', 'Transactions')
@section('page-subtitle', 'Wallet credits and debits — filter by verifications, VTU, or API funding.')

@section('content')
<style>
.admin-tx-filters {
    display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem;
}
.admin-tx-filters a {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem .85rem; border-radius: 999px; text-decoration: none;
    font-size: .8rem; font-weight: 600; color: #475569;
    background: #f1f5f9; border: 1px solid #e2e8f0; transition: .15s;
}
.admin-tx-filters a:hover { color: #1e293b; background: #e2e8f0; }
.admin-tx-filters a.active {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border-color: transparent; color: #fff;
}
.admin-tx-filters .count {
    font-size: .7rem; font-weight: 700; padding: .1rem .45rem;
    border-radius: 999px; background: rgba(0,0,0,.08);
}
.admin-tx-filters a.active .count { background: rgba(255,255,255,.22); }
</style>

<nav class="admin-tx-filters" aria-label="Filter transactions">
    @foreach($filters as $key => $label)
        <a href="{{ url('admin/transactions') }}{{ $key === 'all' ? '' : '?filter='.$key }}"
           class="{{ $filter === $key ? 'active' : '' }}">
            {{ $label }}
            <span class="count">{{ number_format($counts[$key] ?? 0) }}</span>
        </a>
    @endforeach
</nav>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Direction</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $data)
                        @php
                            $category = \App\Support\TransactionLabels::categoryFor($data);
                            [$categoryLabel, $categoryTone] = \App\Support\TransactionLabels::categoryBadge($category);
                            $isCredit = \App\Support\TransactionLabels::isCredit((int) $data->type);
                        @endphp
                        <tr>
                            <td><code class="small">{{ $data->ref_id }}</code></td>
                            <td>
                                @if($data->user)
                                    <a href="{{ url('admin/view-user?id=') }}{{ $data->user->id }}">{{ $data->user->username }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $categoryTone }}">{{ $categoryLabel }}</span></td>
                            <td class="small">{{ \App\Support\TransactionLabels::typeLabel((int) $data->type) }}</td>
                            <td>
                                @if($isCredit)
                                    <span class="badge badge-success">Credit</span>
                                @else
                                    <span class="badge badge-danger">Debit</span>
                                @endif
                            </td>
                            <td class="font-weight-bold">₦{{ number_format((float) $data->amount, 2) }}</td>
                            <td>@include('admin.partials.transaction-status', ['status' => $data->status])</td>
                            <td class="small text-muted">{{ $data->created_at ? $data->created_at->format('d/m/y H:i') : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No transactions found{{ $filter !== 'all' ? ' for this filter' : '' }}.
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
@endsection
