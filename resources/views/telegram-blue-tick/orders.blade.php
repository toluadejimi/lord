@extends('telegram-blue-tick.layout')

@section('tbt-body')
<div class="card tbt-card">
    <div class="card-body p-0">
        @if($orders->isEmpty())
        <div class="text-center py-5 px-3">
            <p class="text-muted mb-3">You have not placed any Telegram Blue Tick orders yet.</p>
            <a href="{{ route('telegram-blue-tick.index') }}" class="btn tbt-submit">Buy Premium</a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Recipient</th>
                        <th>Duration</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="small">{{ $order->created_at->format('M j, Y H:i') }}</td>
                        <td>
                            <div class="fw-semibold">@{{ $order->username }}</div>
                            @if($order->recipient_name)
                            <div class="small text-muted">{{ $order->recipient_name }}</div>
                            @endif
                        </td>
                        <td>{{ $order->months }} mo</td>
                        <td>₦{{ number_format($order->amount_ngn, 2) }}</td>
                        <td>
                            @php
                                $badge = match($order->status) {
                                    'completed' => 'tbt-badge-completed',
                                    'pending' => 'tbt-badge-pending',
                                    'refunded' => 'tbt-badge-refunded',
                                    default => 'tbt-badge-failed',
                                };
                                $label = config('telegram_premium.statuses.'.$order->status, ucfirst($order->status));
                            @endphp
                            <span class="badge {{ $badge }}">{{ $label }}</span>
                            @if($order->failure_reason && $order->status !== 'completed')
                            <div class="small text-danger mt-1">{{ Str::limit($order->failure_reason, 80) }}</div>
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
@endsection
