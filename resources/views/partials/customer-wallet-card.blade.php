@php
    $balance = (float) ($wallet ?? Auth::user()->wallet ?? 0);
    $fundHref = $fundUrl ?? url('fund-wallet');
    $showFund = $showFund ?? true;
    $secondaryHref = $secondaryUrl ?? null;
    $secondaryText = $secondaryLabel ?? 'Transactions';
@endphp
<div class="cp-wallet-card">
    <div class="cp-wallet-card__icon" aria-hidden="true">
        <i class="ti ti-wallet"></i>
    </div>
    <div class="cp-wallet-card__body">
        <div class="cp-wallet-card__label">{{ $label ?? 'Available balance' }}</div>
        <div class="cp-wallet-card__amount">₦{{ number_format($balance, 2) }}</div>
        @if($showFund || $secondaryHref)
        <div class="cp-wallet-card__actions">
            @if($showFund)
                <a href="{{ $fundHref }}" class="cp-wallet-card__btn cp-wallet-card__btn--primary">
                    <i class="ti ti-plus"></i> Fund wallet
                </a>
            @endif
            @if($secondaryHref)
                <a href="{{ $secondaryHref }}" class="cp-wallet-card__btn cp-wallet-card__btn--ghost">
                    <i class="ti ti-list-details"></i> {{ $secondaryText }}
                </a>
            @endif
        </div>
        @endif
    </div>
</div>
