@extends('admin.layout', ['adminActive' => 'verifications'])

@section('title', 'Verifications')
@section('page-title', 'Verifications')
@section('page-subtitle', 'SMS verification orders across all providers. Cancel pending orders and refund customers.')

@section('content')
@if(session('message'))
<div class="alert alert-success">{{ session('message') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Order ID</th>
                        <th>Country</th>
                        <th>Provider</th>
                        <th>Service</th>
                        <th>Phone</th>
                        <th>SMS</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($verifications as $data)
                    @php
                        $status = (int) $data->status;
                        $isPending = $status === 1;
                        $isCompleted = $status === 2;
                        $isCancelled = $status === 99;
                    @endphp
                    <tr>
                        <td><a href="{{ url('admin/view-user?id=') }}{{ $data->user->id ?? '' }}">{{ $data->user->username ?? '—' }}</a></td>
                        <td><code class="small">{{ $data->order_id }}</code></td>
                        <td>{{ $data->country ?? '—' }}</td>
                        <td>@include('admin.partials.verification-type', ['type' => $data->type])</td>
                        <td><code class="small">{{ $data->service ?? '—' }}</code></td>
                        <td><code class="small">{{ $data->phone }}</code></td>
                        <td class="small">{{ $data->sms ? \Illuminate\Support\Str::limit($data->sms, 40) : '—' }}</td>
                        <td>₦{{ number_format($data->cost ?? 0, 2) }}</td>
                        <td>
                            @if($isCompleted)
                                <span class="badge badge-pill badge-success">Successful</span>
                            @elseif($isCancelled)
                                <span class="badge badge-pill badge-secondary">Cancelled</span>
                            @elseif($isPending)
                                <span class="badge badge-pill badge-warning">Pending</span>
                            @else
                                <span class="badge badge-pill badge-light">{{ $status }}</span>
                            @endif
                        </td>
                        <td class="small text-nowrap">{{ $data->created_at ? $data->created_at->format('d/m/y H:i') : '—' }}</td>
                        <td class="text-end text-nowrap">
                            @if($isPending)
                            <form method="post" action="{{ url('admin/verifications/'.$data->id.'/cancel') }}" class="d-inline"
                                onsubmit="return confirm('Cancel this verification and refund ₦{{ number_format($data->cost ?? 0, 2) }} to {{ $data->user->username ?? 'customer' }}?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Cancel &amp; refund
                                </button>
                            </form>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
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
