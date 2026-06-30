<nav class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <div class="sidebar-brand">
            <a href="{{ url('admin/dashboard') }}">SMS<span>LORD</span></a>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav flex-column">
                    <li class="nav-divider">Overview</li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'dashboard' ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'transactions' ? 'active' : '' }}" href="{{ url('admin/transactions') }}">
                            <i class="fas fa-exchange-alt"></i> Transactions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'verifications' ? 'active' : '' }}" href="{{ url('admin/verifications') }}">
                            <i class="fas fa-mobile-alt"></i> Verifications
                        </a>
                    </li>

                    <li class="nav-divider">Services</li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'services' ? 'active' : '' }}" href="{{ url('admin/services') }}">
                            <i class="fas fa-satellite-dish"></i> SMS Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'vtu' ? 'active' : '' }}" href="{{ url('admin/vtu') }}">
                            <i class="fas fa-bolt"></i> VTU / Bills
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'settings' ? 'active' : '' }}" href="{{ url('admin/settings') }}">
                            <i class="fas fa-cog"></i> Platform Settings
                        </a>
                    </li>

                    <li class="nav-divider">Users &amp; Payments</li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'users' ? 'active' : '' }}" href="{{ url('admin/users') }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ ($adminActive ?? '') === 'manual-payment' ? 'active' : '' }}" href="{{ url('admin/manual-payment') }}">
                            <i class="fas fa-money-check-alt"></i> Manual Payments
                        </a>
                    </li>

                    <li class="nav-divider">Account</li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-logout" href="{{ url('log-out') }}">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</nav>
