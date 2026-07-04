<div class="sv-hero d-flex flex-column flex-sm-row flex-wrap justify-content-between align-items-start gap-3">
    <div class="flex-grow-1">
        <span class="sv-server-pill mb-2">
            <span class="sv-num">{{ $serverNum }}</span>
            <span>Server {{ $serverNum }}</span>
        </span>
        <h2 class="h4 mb-1">{{ $title }}</h2>
        <p class="mb-0 small opacity-90">{{ $subtitle }}</p>
    </div>
    <div class="sv-wallet-block w-100 w-sm-auto">
        <div class="small opacity-75">Wallet balance</div>
        <div class="h4 mb-0 fw-bold">₦{{ number_format((float) ($wallet ?? Auth::user()->wallet), 2) }}</div>
        <a href="{{ url('fund-wallet') }}" class="small text-white text-decoration-underline opacity-90">Fund wallet</a>
    </div>
</div>
