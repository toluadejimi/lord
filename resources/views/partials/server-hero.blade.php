<div class="cp-hero sv-hero">
    <div class="cp-hero__main">
        <span class="cp-hero__badge sv-server-pill">
            <span class="cp-hero__badge-num sv-num">{{ $serverNum }}</span>
            <span>{{ \App\Support\VerificationLabels::customerMenuLabelForServer($serverNum) }}</span>
        </span>
        <h2 class="h4">{{ $title }}</h2>
        <p class="cp-hero__subtitle">{{ $subtitle }}</p>
    </div>
    @include('partials.customer-wallet-card', [
        'wallet' => $wallet ?? null,
        'secondaryUrl' => url('wallet-transactions'),
        'secondaryLabel' => 'History',
    ])
</div>
