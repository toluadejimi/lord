<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ static_asset('concept/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ static_asset('concept/assets/vendor/fonts/circular-std/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ static_asset('concept/assets/libs/css/style.css') }}">
    <link rel="stylesheet" href="{{ static_asset('concept/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
    <link rel="stylesheet" href="{{ static_asset('concept/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css') }}">
    <title>Users — SMSLORD Admin</title>
    <style>
        :root {
            --dashboard-primary: #4f46e5;
            --dashboard-primary-light: #818cf8;
            --dashboard-muted: #64748b;
            --dashboard-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        }
        body { background: var(--dashboard-bg); }
        .dashboard-wrapper { padding-bottom: 2rem; }
        .page-header .pageheader-title { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 0.25rem; }
        .page-breadcrumb .breadcrumb { background: transparent; padding: 0; margin: 0; font-size: 0.875rem; }
        .page-breadcrumb .breadcrumb-item a { color: var(--dashboard-muted); }
        .page-breadcrumb .breadcrumb-item.active { color: var(--dashboard-primary); }
        .alert { border-radius: 10px; border: none; box-shadow: var(--card-shadow); }
        .alert-danger { background: #fef2f2; color: #b91c1c; }
        .alert-success { background: #ecfdf5; color: #047857; }
        .dashboard-content .card { border: none; border-radius: 12px; box-shadow: var(--card-shadow); }
        .dashboard-content .card-header { font-weight: 700; font-size: 1rem; color: #0f172a; background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 12px 12px 0 0; }
        .dashboard-content .table { font-size: 0.875rem; }
        .dashboard-content .table thead th { font-weight: 600; text-transform: uppercase; letter-spacing: .03em; color: #475569; background: #f1f5f9; border: none; padding: 0.875rem 1rem; }
        .dashboard-content .table tbody td { padding: 0.875rem 1rem; vertical-align: middle; color: #334155; }
        .dashboard-content .table tbody tr:hover { background: #f8fafc; }
        .dashboard-content .table a { font-weight: 500; color: var(--dashboard-primary); }
        .dashboard-content .form-control { border-radius: 8px; border: 1px solid #e2e8f0; }
        .dashboard-content .form-control:focus { border-color: var(--dashboard-primary-light); box-shadow: 0 0 0 3px rgba(79, 70, 229, .15); }
        .btn-sm { border-radius: 8px; font-weight: 500; }
        /* Side menu - same as dashboard */
        .nav-left-sidebar.sidebar-dark { background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%); box-shadow: 4px 0 24px rgba(0,0,0,.08); top: 0 !important; }
        .nav-left-sidebar .menu-list { padding: 0; }
        .nav-left-sidebar .navbar { padding: 0.5rem 0; flex-direction: column; align-items: stretch; }
        .nav-left-sidebar .navbar-collapse { width: 100%; }
        .nav-left-sidebar .navbar-nav.flex-column { width: 100%; padding: 0.5rem 0; }
        .nav-left-sidebar .nav-divider { font-size: 0.625rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.4); padding: 0.5rem 1rem 0.25rem; margin: 0; border: none; list-style: none; }
        .nav-left-sidebar .nav-item { margin: 0 0.35rem; border-radius: 8px; overflow: hidden; }
        .nav-left-sidebar .nav-link { display: flex; align-items: center; padding: 0.5rem 1rem; color: rgba(255,255,255,.85); font-size: 0.875rem; font-weight: 500; border: none; border-radius: 8px; transition: background .2s, color .2s; }
        .nav-left-sidebar .nav-link i { width: 1.25rem; margin-right: 0.5rem; font-size: 0.9rem; opacity: .9; text-align: center; }
        .nav-left-sidebar .nav-link:hover { background: rgba(255,255,255,.1); color: #fff; }
        .nav-left-sidebar .nav-link.active { background: linear-gradient(135deg, var(--dashboard-primary), #6366f1); color: #fff; box-shadow: 0 4px 12px rgba(79, 70, 229, .4); }
        .nav-left-sidebar .sidebar-brand { padding: 0.875rem 1rem; border-bottom: 1px solid rgba(255,255,255,.08); margin-bottom: 0.25rem; }
        .nav-left-sidebar .sidebar-brand a { font-weight: 800; font-size: 1.1rem; color: #fff !important; letter-spacing: -.02em; text-decoration: none; }
        .nav-left-sidebar .sidebar-brand a span { color: var(--dashboard-primary-light); }
        .nav-left-sidebar .navbar-toggler { border-color: rgba(255,255,255,.2); padding: 0.5rem 0.75rem; }
        .nav-left-sidebar .navbar-toggler-icon { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e"); }
        .nav-left-sidebar .nav-link-logout { margin-top: 0.25rem; border-top: 1px solid rgba(255,255,255,.1); padding-top: 0.5rem; }
        .nav-left-sidebar .nav-link-logout:hover { color: #fca5a5 !important; }
        .dashboard-main-wrapper { padding-top: 0 !important; }
        .dashboard-main-wrapper .dashboard-wrapper { padding-top: 0; }
        .dashboard-main-wrapper .dashboard-content { padding-top: 1.25rem; padding-left: 1.5rem; padding-right: 1.5rem; padding-bottom: 2rem; }
        .dashboard-main-wrapper .page-header { margin-bottom: 1rem; padding-bottom: 0.5rem; }
        .footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1rem 0; font-size: 0.8125rem; color: var(--dashboard-muted); }
        .footer a { color: var(--dashboard-primary); }
        .stat-card { border: none; border-radius: 12px; box-shadow: var(--card-shadow); }
        .stat-card .stat-label { font-size: 0.8125rem; font-weight: 600; color: var(--dashboard-muted); margin-bottom: 0.35rem; }
        .stat-card .stat-value { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
    </style>
</head>
<body>
<div class="dashboard-main-wrapper">
    @include('admin.partials.sidebar')

    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
                @if (session()->has('message'))
                    <div class="alert alert-success">{{ session()->get('message') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-xl-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Users</h2>
                            <p class="pageheader-text text-muted small mb-0">Manage platform users.</p>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}" class="breadcrumb-link">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-label">Total Users</div>
                                <div class="stat-value">{{ $user }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-label">Search by Email</div>
                                <form action="{{ url('admin/search-user') }}" method="GET" class="mt-2">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-label">Search by Username</div>
                                <form action="{{ url('admin/search-username') }}" method="GET" class="mt-2">
                                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <h5 class="card-header">All Users</h5>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Wallet (NGN)</th>
                                                <th>Funded</th>
                                                <th>Bought</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($users as $data)
                                                @php
                                                    $total_bought = \App\Models\Verification::where('user_id', $data->id)->where('status', 2)->sum('cost');
                                                    $total_funded = \App\Models\Transaction::where('user_id', $data->id)->where('status', 2)->sum('amount');
                                                @endphp
                                                <tr>
                                                    <td><a href="{{ url('admin/view-user?id=') }}{{ $data->id }}">{{ $data->username ?? '—' }}</a></td>
                                                    <td><a href="{{ url('admin/view-user?id=') }}{{ $data->id }}">{{ $data->email }}</a></td>
                                                    <td>{{ number_format($data->wallet ?? 0, 2) }}</td>
                                                    <td>{{ number_format($total_funded, 2) }}</td>
                                                    <td class="{{ $total_funded < $total_bought ? 'text-danger' : '' }}">{{ number_format($total_bought, 2) }}</td>
                                                    <td>
                                                        <a href="{{ url('admin/view-user?id=') }}{{ $data->id }}" class="btn btn-success btn-sm">View</a>
                                                        @if($data->status == 9)
                                                            <a href="{{ url('unban-users?id=') }}{{ $data->id }}" class="btn btn-info btn-sm">Unban</a>
                                                        @else
                                                            <a href="{{ url('ban-user?id=') }}{{ $data->id }}" class="btn btn-warning btn-sm">Ban</a>
                                                        @endif
                                                        <a href="{{ url('admin/remove-user?id=') }}{{ $data->id }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?');">Delete</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($users->hasPages())
                                    <div class="p-3 border-top">{{ $users->links() }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xl-6 col-12">&copy; {{ date('Y') }} SMSLORD. All rights reserved.</div>
                <div class="col-xl-6 col-12 text-md-right">
                    <a href="{{ url('admin/dashboard') }}">Dashboard</a>
                    <span class="mx-2">·</span>
                    <a href="{{ url('admin/users') }}">Users</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ static_asset('concept/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
<script src="{{ static_asset('concept/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
<script src="{{ static_asset('concept/assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
<script src="{{ static_asset('concept/assets/libs/js/main-js.js') }}"></script>
</body>
</html>
