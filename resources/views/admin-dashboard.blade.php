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
    <title>Admin Dashboard — SMSLORD</title>
    <style>
        :root {
            --dashboard-primary: #4f46e5;
            --dashboard-primary-light: #818cf8;
            --dashboard-success: #059669;
            --dashboard-warning: #d97706;
            --dashboard-muted: #64748b;
            --dashboard-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --card-shadow-hover: 0 10px 25px -5px rgba(0,0,0,.08), 0 4px 6px -2px rgba(0,0,0,.04);
        }
        body { background: var(--dashboard-bg); }
        .dashboard-wrapper { padding-bottom: 2rem; }
        .page-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }
        .page-header .pageheader-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }
        .page-breadcrumb .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.875rem;
        }
        .page-breadcrumb .breadcrumb-item a { color: var(--dashboard-muted); }
        .page-breadcrumb .breadcrumb-item.active { color: var(--dashboard-primary); }
        /* Stat cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: box-shadow .2s, transform .02s;
            overflow: hidden;
        }
        .stat-card:hover { box-shadow: var(--card-shadow-hover); }
        .stat-card .card-body {
            padding: 1.25rem 1.5rem;
            position: relative;
        }
        .stat-card .stat-label {
            font-size: 0.8125rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: var(--dashboard-muted);
            margin-bottom: 0.5rem;
        }
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-card .stat-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            opacity: .95;
        }
        .stat-card.stat-users .stat-icon { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .stat-card.stat-in .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card.stat-out .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-card.stat-verified .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        /* Alerts */
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: var(--card-shadow);
        }
        .alert-danger { background: #fef2f2; color: #b91c1c; }
        .alert-success { background: #ecfdf5; color: #047857; }
        /* Content cards */
        .dashboard-content .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }
        .dashboard-content .card-header {
            font-weight: 700;
            font-size: 1rem;
            color: #0f172a;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            border-radius: 12px 12px 0 0;
        }
        .dashboard-content .table {
            font-size: 0.875rem;
        }
        .dashboard-content .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #475569;
            background: #f1f5f9;
            border: none;
            padding: 0.875rem 1rem;
            white-space: nowrap;
        }
        .dashboard-content .table tbody td {
            padding: 0.875rem 1rem;
            vertical-align: middle;
            color: #334155;
        }
        .dashboard-content .table tbody tr:hover {
            background: #f8fafc;
        }
        .dashboard-content .table .badge {
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }
        .dashboard-content .table a { font-weight: 500; color: var(--dashboard-primary); }
        .dashboard-content .table a:hover { text-decoration: underline; }
        /* Settings card */
        .settings-card .card-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }
        .settings-card .form-group {
            margin-bottom: 1rem;
        }
        .settings-card label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.35rem;
        }
        .settings-card .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0.75rem;
            font-size: 0.9375rem;
        }
        .settings-card .form-control:focus {
            border-color: var(--dashboard-primary-light);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .15);
        }
        .settings-card .btn-primary {
            background: var(--dashboard-primary);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        .settings-card .btn-primary:hover {
            background: #4338ca;
            box-shadow: 0 4px 12px rgba(79, 70, 229, .35);
        }
        .settings-card .settings-divider {
            border: 0;
            height: 1px;
            background: #e2e8f0;
            margin: 1rem 0;
        }
        .settings-card .settings-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--dashboard-muted);
            margin-bottom: 0.75rem;
            padding-top: 0.5rem;
        }
        /* Side menu - compact, no excess space */
        .nav-left-sidebar.sidebar-dark {
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);
            box-shadow: 4px 0 24px rgba(0,0,0,.08);
            top: 0 !important;
        }
        .nav-left-sidebar .menu-list {
            padding: 0;
        }
        .nav-left-sidebar .navbar {
            padding: 0.5rem 0;
            flex-direction: column;
            align-items: stretch;
        }
        .nav-left-sidebar .navbar-collapse {
            width: 100%;
        }
        .nav-left-sidebar .navbar-nav.flex-column {
            width: 100%;
            padding: 0.5rem 0;
        }
        .nav-left-sidebar .nav-divider {
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(255,255,255,.4);
            padding: 0.5rem 1rem 0.25rem;
            margin: 0;
            border: none;
            list-style: none;
        }
        .nav-left-sidebar .nav-item {
            margin: 0 0.35rem;
            border-radius: 8px;
            overflow: hidden;
        }
        .nav-left-sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            color: rgba(255,255,255,.85);
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            transition: background .2s, color .2s, padding-left .2s;
        }
        .nav-left-sidebar .nav-link i {
            width: 1.25rem;
            margin-right: 0.5rem;
            font-size: 0.9rem;
            opacity: .9;
            text-align: center;
        }
        .nav-left-sidebar .nav-link:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
        }
        .nav-left-sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--dashboard-primary), #6366f1);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, .4);
        }
        .nav-left-sidebar .nav-link.active i {
            opacity: 1;
        }
        .nav-left-sidebar .sidebar-brand {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            margin-bottom: 0.25rem;
        }
        .nav-left-sidebar .sidebar-brand a {
            font-weight: 800;
            font-size: 1.1rem;
            color: #fff !important;
            letter-spacing: -.02em;
            text-decoration: none;
        }
        .nav-left-sidebar .sidebar-brand a span {
            color: var(--dashboard-primary-light);
        }
        .nav-left-sidebar .navbar-toggler {
            border-color: rgba(255,255,255,.2);
            padding: 0.5rem 0.75rem;
        }
        .nav-left-sidebar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        .nav-left-sidebar .nav-link-logout {
            margin-top: 0.25rem;
            border-top: 1px solid rgba(255,255,255,.1);
            padding-top: 0.5rem;
        }
        .nav-left-sidebar .nav-link-logout:hover {
            color: #fca5a5 !important;
        }
        /* No top header - remove excess space */
        .dashboard-main-wrapper {
            padding-top: 0 !important;
        }
        .dashboard-main-wrapper .dashboard-wrapper {
            padding-top: 0;
        }
        .dashboard-main-wrapper .dashboard-content {
            padding-top: 1.25rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            padding-bottom: 2rem;
        }
        .dashboard-main-wrapper .page-header {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
        }
        /* Navbar */
        .dashboard-header .navbar {
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .dashboard-header .navbar-brand {
            font-weight: 800;
            color: var(--dashboard-primary) !important;
            font-size: 1.25rem;
        }
        .dashboard-header .nav-user-dropdown .nav-user-info {
            background: linear-gradient(135deg, var(--dashboard-primary), #4338ca);
            padding: 0.75rem 1rem;
        }
        /* Footer */
        .footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 0;
            font-size: 0.8125rem;
            color: var(--dashboard-muted);
        }
        .footer a { color: var(--dashboard-primary); }
        /* Pagination */
        .pagination { font-size: 0.875rem; }
        .pagination .page-link {
            border-radius: 8px !important;
            margin: 0 2px;
        }
    </style>
</head>

<body>
<div class="dashboard-main-wrapper">
    <div class="nav-left-sidebar sidebar-dark">
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
                        <li class="nav-divider">Menu</li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ url('admin/dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/users') }}"><i class="fas fa-users"></i> Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/manual-payment') }}"><i class="fas fa-money-check-alt"></i> Manual Payment</a>
                        </li>
                        <li class="nav-divider">Account</li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-logout" href="{{ url('log-out') }}"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session()->has('message'))
                    <div class="alert alert-success">{{ session()->get('message') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Admin Dashboard</h2>
                            <p class="pageheader-text text-muted small mb-0">Overview of platform activity and settings.</p>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}" class="breadcrumb-link">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="{{ url('admin/settings') }}" class="breadcrumb-link">Settings</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Overview</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ecommerce-widget">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                            <div class="card stat-card stat-users">
                                <div class="card-body">
                                    <div class="stat-label">Total Users</div>
                                    <div class="stat-value">{{ number_format($user) }}</div>
                                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                            <div class="card stat-card stat-in">
                                <div class="card-body">
                                    <div class="stat-label">Total In</div>
                                    <div class="stat-value">NGN {{ number_format($total_in) }}</div>
                                    <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                            <div class="card stat-card stat-out">
                                <div class="card-body">
                                    <div class="stat-label">Total Out</div>
                                    <div class="stat-value">NGN {{ number_format($total_out) }}</div>
                                    <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
                            <div class="card stat-card stat-verified">
                                <div class="card-body">
                                    <div class="stat-label">Verified Messages</div>
                                    <div class="stat-value">{{ number_format($total_verified_message) }}</div>
                                    <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                            <div class="card">
                                <h5 class="card-header">Recent Orders</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Order ID</th>
                                                    <th>Country</th>
                                                    <th>Type</th>
                                                    <th>Service</th>
                                                    <th>Phone</th>
                                                    <th>SMS</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>IP</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($verification as $data)
                                                <tr>
                                                    <td><a href="{{ url('admin/view-user?id=') }}{{ $data->user->id ?? '' }}">{{ $data->user->username ?? '—' }}</a></td>
                                                    <td><code class="small">{{ $data->order_id }}</code></td>
                                                    <td>{{ $data->country ?? '—' }}</td>
                                                    <td>
                                                        @if($data->type == 3)
                                                            <span class="badge badge-info">3SIM</span>
                                                        @elseif($data->type == 2)
                                                            <span class="badge badge-primary">SMSPOOL</span>
                                                        @else
                                                            <span class="badge badge-secondary">Legacy</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $data->service ?? '—' }}</td>
                                                    <td><code class="small">{{ $data->phone }}</code></td>
                                                    <td>{{ $data->sms ?? '—' }}</td>
                                                    <td>NGN {{ number_format($data->cost ?? 0, 2) }}</td>
                                                    <td>
                                                        @if($data->status == 2)
                                                            <span class="badge badge-pill badge-success">Successful</span>
                                                        @else
                                                            <span class="badge badge-pill badge-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="small text-muted">{{ $data->ip ?? '—' }}</span></td>
                                                    <td>{{ $data->created_at ? date('d/m/y', strtotime($data->created_at)) : '—' }}</td>
                                                    <td>{{ $data->created_at ? date('H:i', strtotime($data->created_at)) : '—' }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="12" class="text-center text-muted py-4">No orders found.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($verification->hasPages())
                                        <div class="p-3 border-top">{{ $verification->links() }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                            <div class="card settings-card">
                                <h5 class="card-header">Cost &amp; Rate</h5>
                                <div class="card-body">
                                    <div class="settings-section-title">SMS Pool</div>
                                    <form method="post" action="{{ url('admin/update-smspool-rate') }}" class="form-group">
                                        @csrf
                                        <label>Rate</label>
                                        <input type="text" name="rate" class="form-control" value="{{ $smspoolrate }}" placeholder="0.00">
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update Rate</button>
                                    </form>
                                    <form method="post" action="{{ url('admin/update-smspool-cost') }}" class="form-group">
                                        @csrf
                                        <label>Cost</label>
                                        <input type="text" name="cost" class="form-control" value="{{ $smspoolcost }}" placeholder="0.00">
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update Cost</button>
                                    </form>
                                    <hr class="settings-divider">
                                    <div class="settings-section-title">SIM</div>
                                    <form method="post" action="{{ url('admin/update-sim-rate') }}" class="form-group">
                                        @csrf
                                        <label>Rate</label>
                                        <input type="text" name="rate" class="form-control" value="{{ $simrate }}" placeholder="0.00">
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update Rate</button>
                                    </form>
                                    <form method="post" action="{{ url('admin/update-sim-cost') }}" class="form-group">
                                        @csrf
                                        <label>Cost</label>
                                        <input type="text" name="cost" class="form-control" value="{{ $simcost }}" placeholder="0.00">
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update Cost</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Recent Transactions</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Reference</th>
                                                    <th>User</th>
                                                    <th>Type</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($transaction as $data)
                                                <tr>
                                                    <td><code class="small">{{ $data->ref_id }}</code></td>
                                                    <td>{{ $data->user->username ?? '—' }}</td>
                                                    <td>
                                                        @if($data->type == 2)
                                                            <span class="badge badge-success">Credit</span>
                                                        @else
                                                            <span class="badge badge-danger">Debit</span>
                                                        @endif
                                                    </td>
                                                    <td>NGN {{ number_format($data->amount, 2) }}</td>
                                                    <td>
                                                        @if($data->status == 1)
                                                            <span class="badge badge-pill badge-warning">Initiated</span>
                                                        @elseif($data->status == 0)
                                                            <span class="badge badge-pill badge-secondary">Pending</span>
                                                        @elseif($data->status == 3)
                                                            <span class="badge badge-pill badge-danger">Cancelled</span>
                                                        @elseif($data->status == 4)
                                                            <span class="badge badge-pill badge-info">Resolved</span>
                                                        @else
                                                            <span class="badge badge-pill badge-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ date('d/m/y', strtotime($data->created_at)) }}</td>
                                                    <td>{{ date('H:i', strtotime($data->created_at)) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">No transactions found.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($transaction->hasPages())
                                        <div class="p-3 border-top">{{ $transaction->links() }}</div>
                                    @endif
                                </div>
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
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                    &copy; {{ date('Y') }} SMSLORD. All rights reserved.
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 text-md-right">
                    <a href="{{ url('admin/dashboard') }}">Dashboard</a>
                    <span class="mx-2">·</span>
                    <a href="{{ url('/') }}">Site</a>
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
