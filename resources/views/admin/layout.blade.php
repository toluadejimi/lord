<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('concept/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ asset('concept/assets/vendor/fonts/circular-std/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('concept/assets/libs/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('concept/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
    <title>@yield('title', 'Admin') — SMSLORD</title>
    <style>
        :root {
            --dashboard-primary: #4f46e5;
            --dashboard-muted: #64748b;
            --dashboard-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        body { background: var(--dashboard-bg); }
        .dashboard-main-wrapper { padding-top: 0 !important; }
        .dashboard-wrapper { padding-bottom: 2rem; }
        .dashboard-main-wrapper .dashboard-content { padding: 1.25rem 1.5rem 2rem; }
        .page-header { margin-bottom: 1.25rem; }
        .pageheader-title { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: .25rem; }
        .pageheader-text { color: var(--dashboard-muted); font-size: .875rem; }
        .alert { border-radius: 10px; border: none; box-shadow: var(--card-shadow); }
        .alert-danger { background: #fef2f2; color: #b91c1c; }
        .alert-success { background: #ecfdf5; color: #047857; }
        .card { border: none; border-radius: 12px; box-shadow: var(--card-shadow); }
        .card-header { font-weight: 700; background: #fff; border-bottom: 1px solid #e2e8f0; }
        .stat-card .card-body { padding: 1.25rem 1.5rem; position: relative; }
        .stat-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; color: var(--dashboard-muted); }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #0f172a; }
        .stat-icon { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; }
        .stat-users .stat-icon { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .stat-in .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-out .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-verified .stat-icon { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .nav-left-sidebar.sidebar-dark { background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%); box-shadow: 4px 0 24px rgba(0,0,0,.08); top: 0 !important; min-height: 100vh; }
        .sidebar-brand { padding: 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar-brand a { font-size: 1.25rem; font-weight: 800; color: #fff; text-decoration: none; }
        .sidebar-brand span { color: #a5b4fc; }
        .nav-left-sidebar .nav-divider { font-size: .625rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.4); padding: .75rem 1rem .25rem; list-style: none; }
        .nav-left-sidebar .nav-link { display: flex; align-items: center; padding: .55rem 1rem; color: rgba(255,255,255,.85); font-size: .875rem; font-weight: 500; border-radius: 8px; margin: 0 .35rem; }
        .nav-left-sidebar .nav-link i { width: 1.25rem; margin-right: .5rem; text-align: center; }
        .nav-left-sidebar .nav-link:hover { background: rgba(255,255,255,.1); color: #fff; }
        .nav-left-sidebar .nav-link.active { background: linear-gradient(135deg, var(--dashboard-primary), #6366f1); color: #fff; }
        .nav-link-logout { margin-top: .5rem; border-top: 1px solid rgba(255,255,255,.1); padding-top: .75rem !important; }
        .service-card { border-left: 4px solid var(--dashboard-primary); }
        .service-card.disabled { border-left-color: #94a3b8; opacity: .85; }
        .badge-on { background: #dcfce7; color: #166534; }
        .badge-off { background: #f1f5f9; color: #64748b; }
        .footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 1rem 0; font-size: .8125rem; color: var(--dashboard-muted); }
        .table thead th { font-size: .75rem; text-transform: uppercase; color: #475569; background: #f1f5f9; border: none; }
    </style>
    @stack('styles')
</head>
<body>
<div class="dashboard-main-wrapper">
    @include('admin.partials.sidebar')

    <div class="dashboard-wrapper">
        <div class="container-fluid dashboard-content">
            @include('admin.partials.alerts')

            <div class="page-header">
                <h2 class="pageheader-title">@yield('page-title')</h2>
                @hasSection('page-subtitle')
                    <p class="pageheader-text mb-0">@yield('page-subtitle')</p>
                @endif
            </div>

            @yield('content')
        </div>
    </div>

    <div class="footer">
        <div class="container-fluid text-center">
            &copy; {{ date('Y') }} SMSLORD Admin · <a href="{{ url('/') }}">View Site</a>
        </div>
    </div>
</div>

<script src="{{ asset('concept/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('concept/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
@stack('scripts')
</body>
</html>
