@extends('admin.layout', ['adminActive' => 'dashboard'])

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Overview of platform activity.')

@section('content')
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
        <div class="card stat-card stat-users">
            <div class="card-body">
                <div class="stat-label">Total Users</div>
                <div class="stat-value">{{ number_format($user) }}</div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
        <div class="card stat-card stat-in">
            <div class="card-body">
                <div class="stat-label">Total In</div>
                <div class="stat-value">NGN {{ number_format($total_in) }}</div>
                <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
        <div class="card stat-card stat-out">
            <div class="card-body">
                <div class="stat-label">Total Out</div>
                <div class="stat-value">NGN {{ number_format($total_out) }}</div>
                <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
        <div class="card stat-card stat-verified">
            <div class="card-body">
                <div class="stat-label">Verified Messages</div>
                <div class="stat-value">{{ number_format($total_verified_message) }}</div>
                <div class="stat-icon"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Verifications</span>
                <a href="{{ url('admin/verifications') }}" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Order ID</th>
                                <th>Country</th>
                                <th>Type</th>
                                <th>Service</th>
                                <th>Phone</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($verification as $data)
                            <tr>
                                <td><a href="{{ url('admin/view-user?id=') }}{{ $data->user->id ?? '' }}">{{ $data->user->username ?? '—' }}</a></td>
                                <td><code class="small">{{ $data->order_id }}</code></td>
                                <td>{{ $data->country ?? '—' }}</td>
                                <td>@include('admin.partials.verification-type', ['type' => $data->type])</td>
                                <td>{{ $data->service ?? '—' }}</td>
                                <td><code class="small">{{ $data->phone }}</code></td>
                                <td>NGN {{ number_format($data->cost ?? 0, 2) }}</td>
                                <td>
                                    @if($data->status == 2)
                                        <span class="badge badge-pill badge-success">Successful</span>
                                    @else
                                        <span class="badge badge-pill badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $data->created_at ? $data->created_at->format('d/m/y H:i') : '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Transactions</span>
                <a href="{{ url('admin/transactions') }}" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
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
                            @forelse ($transaction as $data)
                            <tr>
                                <td><code class="small">{{ $data->ref_id }}</code></td>
                                <td>{{ $data->user->username ?? '—' }}</td>
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
            </div>
        </div>
    </div>
</div>
@endsection
