@php
    $navCfg = app(\App\Services\AppConfigService::class);
    $vtuMasterOn = $navCfg->getBool('provider_vtu_enabled', true);
    $tbtOn = $navCfg->getBool('provider_telegram_blue_tick_enabled', false);
@endphp
<ul class="pc-navbar pc-navbar-modern">
    {{-- Home --}}
    <li class="pc-item pc-caption pc-caption-modern">
        <label>Home</label>
    </li>
    <li class="pc-item">
        <a href="{{ url('cworld') }}" class="pc-link pc-link-modern {{ request()->is('cworld', 'cworld/*') ? 'active' : '' }}">
            <span class="pc-micon pc-micon-soft"><i class="ti ti-layout-dashboard"></i></span>
            <span class="pc-mtext">Dashboard</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ url('fund-wallet') }}" class="pc-link pc-link-modern {{ request()->is('fund-wallet', 'fund-now', 'verify') ? 'active' : '' }}">
            <span class="pc-micon pc-micon-soft pc-micon-wallet"><i class="ti ti-wallet"></i></span>
            <span class="pc-mtext">Fund Wallet</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ url('wallet-transactions') }}" class="pc-link pc-link-modern {{ request()->is('wallet-transactions') ? 'active' : '' }}">
            <span class="pc-micon pc-micon-soft"><i class="ti ti-list-details"></i></span>
            <span class="pc-mtext">Transactions</span>
        </a>
    </li>

    {{-- Verification --}}
    <li class="pc-item pc-caption pc-caption-modern">
        <label>Verification</label>
    </li>
    <li class="pc-item">
        <a href="{{ url('cworld') }}" class="pc-link pc-link-modern {{ request()->is('cworld', 'cworld/*') ? 'active' : '' }}">
            <span class="pc-micon"><span class="nav-server-badge">1</span></span>
            <span class="pc-mtext">Server 1</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ url('usa2') }}" class="pc-link pc-link-modern {{ request()->is('usa2', 'usa2/*') ? 'active' : '' }}">
            <span class="pc-micon"><span class="nav-server-badge">2</span></span>
            <span class="pc-mtext">Server 2</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ url('world-sv2') }}" class="pc-link pc-link-modern {{ request()->is('world-sv2', 'world-sv2/*') ? 'active' : '' }}">
            <span class="pc-micon"><span class="nav-server-badge">3</span></span>
            <span class="pc-mtext">Server 3</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ url('world-sv3') }}" class="pc-link pc-link-modern {{ request()->is('world-sv3', 'world-sv3/*') ? 'active' : '' }}">
            <span class="pc-micon"><span class="nav-server-badge">4</span></span>
            <span class="pc-mtext">Server 4</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ url('orders') }}" class="pc-link pc-link-modern {{ request()->is('orders', 'verification*') ? 'active' : '' }}">
            <span class="pc-micon pc-micon-soft"><i class="ti ti-messages"></i></span>
            <span class="pc-mtext">My Verifications</span>
        </a>
    </li>

    {{-- Premium --}}
    @if($tbtOn)
    <li class="pc-item pc-caption pc-caption-modern pc-caption-premium">
        <label>Premium</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('telegram-blue-tick.index') }}" class="pc-link pc-link-modern pc-link-premium {{ request()->is('telegram-blue-tick', 'telegram-blue-tick/*') ? 'active' : '' }}">
            <span class="pc-micon pc-micon-telegram"><i class="fab fa-telegram"></i></span>
            <span class="pc-mtext">Telegram Blue Tick</span>
            <span class="nav-chip nav-chip-new">New</span>
        </a>
    </li>
    @endif

    {{-- VTU & API --}}
    <li class="pc-item pc-caption pc-caption-modern">
        <label>Bills &amp; API</label>
    </li>
    @if($vtuMasterOn)
    <li class="pc-item">
        <a href="{{ url('vas') }}" class="pc-link pc-link-modern {{ request()->is('vas') ? 'active' : '' }}">
            <span class="pc-micon pc-micon-soft"><i class="ti ti-receipt"></i></span>
            <span class="pc-mtext">Bills &amp; VTU</span>
        </a>
    </li>
    @foreach(config('platform.admin_vtu_services', []) as $vtuSlug => $vtuSvc)
        @if($navCfg->getBool($vtuSvc['enabled_key'], true))
        <li class="pc-item">
            <a href="{{ url('vas/'.$vtuSlug) }}" class="pc-link pc-link-modern pc-link-nested {{ request()->is('vas/'.$vtuSlug) ? 'active' : '' }}">
                <span class="pc-micon pc-micon-dot"><i class="ti {{ $vtuSvc['icon'] ?? 'ti-point-filled' }}"></i></span>
                <span class="pc-mtext">{{ $vtuSvc['label'] }}</span>
            </a>
        </li>
        @endif
    @endforeach
    @endif
    <li class="pc-item">
        <a href="{{ url('api-docs') }}" class="pc-link pc-link-modern {{ request()->is('api-docs', 'api-docs/*') ? 'active' : '' }}">
            <span class="pc-micon pc-micon-soft"><i class="ti ti-code"></i></span>
            <span class="pc-mtext">API Docs</span>
        </a>
    </li>

    {{-- More --}}
    <li class="pc-item pc-caption pc-caption-modern">
        <label>More</label>
    </li>
    <li class="pc-item">
        <a href="https://loggsplug.online" class="pc-link pc-link-modern" target="_blank" rel="noopener">
            <span class="pc-micon pc-micon-soft"><i class="ti ti-shopping-bag"></i></span>
            <span class="pc-mtext">Buy Social Account</span>
            <span class="nav-chip nav-chip-external"><i class="ti ti-external-link"></i></span>
        </a>
    </li>
    <li class="pc-item">
        <a href="https://t.me/smslordcare" class="pc-link pc-link-modern" target="_blank" rel="noopener">
            <span class="pc-micon pc-micon-soft"><i class="fab fa-telegram"></i></span>
            <span class="pc-mtext">Telegram Support</span>
        </a>
    </li>
    <li class="pc-item nav-logout-item">
        <a href="{{ url('log-out') }}" class="pc-link pc-link-modern pc-link-logout">
            <span class="pc-micon pc-micon-soft"><i class="ti ti-logout"></i></span>
            <span class="pc-mtext">Logout</span>
        </a>
    </li>
</ul>
