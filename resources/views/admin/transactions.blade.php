@extends('admin.layout', ['adminActive' => 'transactions'])

@section('title', 'Transactions')
@section('page-title', 'Transactions')
@section('page-subtitle', 'All wallet credits and debits.')

@section('content')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $data)
                    <tr>
                        <td><code class="small">{{ $data->ref_id }}</code></td>
                        <td>
                            @if($data->user)
                                <a href="{{ url('admin/view-user?id=') }}{{ $data->user->id }}">{{ $data->user->username }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($data->type == 2)
                                <span class="badge badge-success">Credit</span>
                            @else
                                <span class="badge badge-danger">Debit</span>
                            @endif
                        </td>
                        <td>NGN {{ number_format($data->amount, 2) }}</td>
                        <td>@include('admin.partials.transaction-status', ['status' => $data->status])</td>
                        <td>{{ $data->created_at ? $data->created_at->format('d/m/y H:i') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No transactions found.</td></tr>
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
