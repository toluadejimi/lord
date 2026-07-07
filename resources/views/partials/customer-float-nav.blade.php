@auth
<nav class="smslord-float-nav" aria-label="Quick navigation">
    <a href="{{ route('dashboard') }}" class="smslord-float-nav__item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
        <span class="smslord-float-nav__icon"><i class="ti ti-home"></i></span>
        <span class="smslord-float-nav__label">Home</span>
    </a>

    @if(($navVerificationServers ?? collect())->isNotEmpty())
    <button type="button" class="smslord-float-nav__item smslord-float-nav__item--center {{ request()->is('cworld*', 'usa2*', 'world-sv2*', 'world-sv3*', 'orders*') ? 'is-active' : '' }}" data-dash-open="numbers-sheet" aria-haspopup="dialog" aria-controls="dash-numbers-sheet">
        <span class="smslord-float-nav__icon smslord-float-nav__icon--main"><i class="ti ti-device-mobile"></i></span>
        <span class="smslord-float-nav__label">Numbers</span>
    </button>
    @else
    <a href="{{ url('orders') }}" class="smslord-float-nav__item smslord-float-nav__item--center {{ request()->is('orders*') ? 'is-active' : '' }}">
        <span class="smslord-float-nav__icon smslord-float-nav__icon--main"><i class="ti ti-device-mobile"></i></span>
        <span class="smslord-float-nav__label">Numbers</span>
    </a>
    @endif

    <a href="{{ route('wallet.transactions') }}" class="smslord-float-nav__item {{ request()->is('wallet-transactions') ? 'is-active' : '' }}">
        <span class="smslord-float-nav__icon"><i class="ti ti-receipt"></i></span>
        <span class="smslord-float-nav__label">Transactions</span>
    </a>

    <a href="{{ url('profile') }}" class="smslord-float-nav__item {{ request()->is('profile', 'change-password') ? 'is-active' : '' }}">
        <span class="smslord-float-nav__icon"><i class="ti ti-user"></i></span>
        <span class="smslord-float-nav__label">Profile</span>
    </a>
</nav>
@endauth
