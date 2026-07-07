@if(($navVerificationServers ?? collect())->isNotEmpty())
<div class="dash-sheet-root" id="dash-numbers-sheet-root" hidden>
    <div class="dash-sheet-backdrop" data-dash-close="numbers-sheet" aria-hidden="true"></div>
    <div class="dash-sheet" id="dash-numbers-sheet" role="dialog" aria-modal="true" aria-labelledby="dash-numbers-sheet-title">
        <div class="dash-sheet-handle" aria-hidden="true"></div>
        <div class="dash-sheet-head">
            <h3 class="dash-sheet-title" id="dash-numbers-sheet-title">Choose a number service</h3>
            <button type="button" class="dash-sheet-close" data-dash-close="numbers-sheet" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <p class="dash-sheet-sub">Select a verification server to buy a virtual number.</p>
        <div class="dash-sheet-list">
            @foreach($navVerificationServers as $server)
            <a href="{{ url(ltrim($server['user_route'], '/')) }}" class="dash-sheet-option">
                <span class="dash-server-num">{{ $server['server_num'] }}</span>
                <span class="dash-sheet-option-text">
                    <span class="dash-sheet-option-name">{{ $server['menu_label'] }}</span>
                    <span class="dash-sheet-option-hint">{{ \App\Support\VerificationLabels::customerServerHint((int) $server['server_num']) }}</span>
                </span>
                <i class="ti ti-chevron-right dash-sheet-option-arrow"></i>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif
