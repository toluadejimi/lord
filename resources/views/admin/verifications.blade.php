@extends('admin.layout', ['adminActive' => 'verifications'])

@section('title', 'Verifications')
@section('page-title', 'Verifications')
@section('page-subtitle', 'SMS verification orders across all providers.')

@section('content')
<div class="card">
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
                        <th>SMS</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>IP</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($verifications as $data)
                    <tr>
                        <td><a href="{{ url('admin/view-user?id=') }}{{ $data->user->id ?? '' }}">{{ $data->user->username ?? '—' }}</a></td>
                        <td><code class="small">{{ $data->order_id }}</code></td>
                        <td>{{ $data->country ?? '—' }}</td>
                        <td>@include('admin.partials.verification-type', ['type' => $data->type])</td>
                        <td>{{ $data->service ?? '—' }}</td>
                        <td><code class="small">{{ $data->phone }}</code></td>
                        <td class="small">{{ $data->sms ? \Illuminate\Support\Str::limit($data->sms, 40) : '—' }}</td>
                        <td>NGN {{ number_format($data->cost ?? 0, 2) }}</td>
                        <td>
                            @if($data->status == 2)
                                <span class="badge badge-pill badge-success">Successful</span>
                            @else
                                <span class="badge badge-pill badge-warning">Pending</span>
                            @endif
                        </td>
                        <td><span class="small text-muted">{{ $data->ip ?? '—' }}</span></td>
                        <td>{{ $data->created_at ? $data->created_at->format('d/m/y H:i') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="text-center text-muted py-4">No verifications found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($verifications->hasPages())
            <div class="p-3 border-top">{{ $verifications->links() }}</div>
        @endif
    </div>
</div>
@endsection
