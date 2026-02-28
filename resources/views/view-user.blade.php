@php
    $user = $user ?? null;
@endphp
@if(!$user)
    <!DOCTYPE html>
    <html><head><meta charset="utf-8"><title>User not found</title></head><body><p>User not found.</p><a href="{{ url('admin/users') }}">Back to Users</a></body></html>
@else
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="{{ url('') }}/public/concept/assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/libs/css/style.css">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="{{ url('') }}/public/concept/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <title>{{ $user->username ?? $user->email }} — SMSLORD Admin</title>
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
        .dashboard-content .table .badge { font-weight: 600; padding: 0.35em 0.65em; border-radius: 6px; }
        .dashboard-content .form-control { border-radius: 8px; border: 1px solid #e2e8f0; }
        .dashboard-content .form-control:focus { border-color: var(--dashboard-primary-light); box-shadow: 0 0 0 3px rgba(79, 70, 229, .15); }
        .btn-sm { border-radius: 8px; font-weight: 500; }
        .stat-card { border: none; border-radius: 12px; box-shadow: var(--card-shadow); }
        .stat-card .stat-label { font-size: 0.8125rem; font-weight: 600; color: var(--dashboard-muted); margin-bottom: 0.35rem; }
        .stat-card .stat-value { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
        .user-profile-card .profile-row { padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0; }
        .user-profile-card .profile-row:last-child { border-bottom: 0; }
        .user-profile-card .profile-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--dashboard-muted); }
        .user-profile-card .profile-value { font-size: 1rem; font-weight: 500; color: #0f172a; }
        /* Side menu */
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
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav flex-column">
                        <li class="nav-divider">Menu</li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin/dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ url('admin/users') }}"><i class="fas fa-users"></i> Users</a>
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
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @if (session()->has('message'))
                    <div class="alert alert-success">{{ session()->get('message') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif

                <div class="row mb-4">
                    <div class="col-xl-12">
                        <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
                            <div>
                                <h2 class="pageheader-title">{{ $user->username ?? $user->email }}</h2>
                                <p class="pageheader-text text-muted small mb-0">User profile & activity</p>
                                <div class="page-breadcrumb mt-1">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}" class="breadcrumb-link">Dashboard</a></li>
                                            <li class="breadcrumb-item"><a href="{{ url('admin/users') }}" class="breadcrumb-link">Users</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">View User</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <div class="mt-2 mt-md-0">
                                <a href="{{ url('admin/users') }}" class="btn btn-outline-secondary btn-sm">Back to Users</a>
                                @if($user->status == 9)
                                    <a href="{{ url('unban-users?id=') }}{{ $user->id }}" class="btn btn-info btn-sm">Unban</a>
                                @else
                                    <a href="{{ url('ban-user?id=') }}{{ $user->id }}" class="btn btn-warning btn-sm">Ban</a>
                                @endif
                                <a href="{{ url('admin/remove-user?id=') }}{{ $user->id }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-6 mb-4">
                        <div class="card user-profile-card">
                            <h5 class="card-header">User Information</h5>
                            <div class="card-body">
                                <div class="profile-row">
                                    <div class="profile-label">Username</div>
                                    <div class="profile-value">{{ $user->username ?? '—' }}</div>
                                </div>
                                <div class="profile-row">
                                    <div class="profile-label">Email</div>
                                    <div class="profile-value">{{ $user->email }}</div>
                                </div>
                                <div class="profile-row">
                                    <div class="profile-label">Wallet</div>
                                    <div class="profile-value">NGN {{ number_format($user->wallet ?? 0, 2) }}</div>
                                </div>
                                <div class="profile-row">
                                    <div class="profile-label">Status</div>
                                    <div class="profile-value">
                                        @if($user->status == 9)
                                            <span class="badge badge-warning">Banned</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-6 mb-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card h-100">
                                    <div class="card-body">
                                        <div class="stat-label">Total Funded</div>
                                        <div class="stat-value">NGN {{ number_format($total_funded ?? 0, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card h-100">
                                    <div class="card-body">
                                        <div class="stat-label">Total Bought</div>
                                        <div class="stat-value">NGN {{ number_format($total_bought ?? 0, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card h-100">
                                    <div class="card-body">
                                        <div class="stat-label">Balance (Funded − Bought)</div>
                                        <div class="stat-value">{{ number_format($total_balance ?? 0, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <h5 class="card-header">Fund / Debit User</h5>
                            <div class="card-body">
                                <form action="{{ url('admin/update-user') }}" method="POST" class="row g-3 align-items-end">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-600 text-muted">Amount (NGN)</label>
                                        <input type="number" class="form-control" name="amount" min="0" step="0.01" placeholder="0.00" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-600 text-muted">Action</label>
                                        <select class="form-control" name="trade" required>
                                            <option value="credit">Credit</option>
                                            <option value="debit">Debit</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Apply</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <h5 class="card-header">Transactions</h5>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($transaction ?? [] as $data)
                                            <tr>
                                                <td><code class="small">{{ $data->ref_id }}</code></td>
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
                                                <td>{{ $data->created_at ? date('d/m/Y H:i', strtotime($data->created_at)) : '—' }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">No transactions.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if(isset($transaction) && $transaction->hasPages())
                                    <div class="p-3 border-top">{{ $transaction->links() }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <h5 class="card-header">Verifications / Orders</h5>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Order ID</th>
                                                <th>Service</th>
                                                <th>Phone</th>
                                                <th>SMS</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($verification ?? [] as $data)
                                            <tr>
                                                <td>
                                                    @if($data->type == 3)
                                                        <span class="badge badge-info">3SIM</span>
                                                    @elseif($data->type == 2)
                                                        <span class="badge badge-primary">SMSPOOL</span>
                                                    @else
                                                        <span class="badge badge-secondary">Legacy</span>
                                                    @endif
                                                </td>
                                                <td><code class="small">{{ $data->order_id }}</code></td>
                                                <td>{{ $data->service ?? '—' }}</td>
                                                <td><code class="small">{{ $data->phone }}</code></td>
                                                <td>{{ Str::limit($data->full_sms ?? $data->sms ?? '—', 20) }}</td>
                                                <td>NGN {{ number_format($data->cost ?? 0, 2) }}</td>
                                                <td>{{ $data->created_at ? date('d/m/y H:i', strtotime($data->created_at)) : '—' }}</td>
                                                <td>
                                                    @if($data->status == 2)
                                                        <span class="badge badge-pill badge-success">Completed</span>
                                                    @else
                                                        <span class="badge badge-pill badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($data->status == 1)
                                                        @if($data->type == 3)
                                                            <a href="{{ url('admin-c-sms?id=') }}{{ $data->id }}&delete=1&user_id={{ $data->user_id }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this order?');">Delete</a>
                                                        @else
                                                            <a href="{{ url('admin-cancle-sms?id=') }}{{ $data->id }}&delete=1&type=2&user_id={{ $data->user_id }}" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this order?');">Cancel</a>
                                                        @endif
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">No orders.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if(isset($verification) && $verification->hasPages())
                                    <div class="p-3 border-top">{{ $verification->links() }}</div>
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

<script src="{{ url('') }}/public/concept/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="{{ url('') }}/public/concept/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<script src="{{ url('') }}/public/concept/assets/vendor/slimscroll/jquery.slimscroll.js"></script>
<script src="{{ url('') }}/public/concept/assets/libs/js/main-js.js"></script>
</body>
</html>
@endif
