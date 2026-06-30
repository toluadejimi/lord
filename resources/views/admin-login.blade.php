<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Login — SMSLORD</title>
    <link rel="stylesheet" href="{{ asset('concept/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ asset('concept/assets/vendor/fonts/circular-std/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('concept/assets/libs/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('concept/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
    <style>
        :root {
            --admin-primary: #4f46e5;
            --admin-primary-dark: #4338ca;
            --admin-bg: #f8fafc;
            --admin-card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4338ca 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
        }
        .admin-login-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .admin-login-card {
            border: none;
            border-radius: 20px;
            box-shadow: var(--admin-card-shadow);
            overflow: hidden;
            background: #fff;
        }
        .admin-login-card .card-header {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-primary-dark) 100%);
            border: none;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
        }
        .admin-login-card .card-header .logo-text {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }
        .admin-login-card .card-header .logo-text span {
            color: rgba(255,255,255,0.9);
        }
        .admin-login-card .card-header .splash-description {
            display: block;
            font-size: 0.9375rem;
            color: rgba(255,255,255,0.85);
            margin-top: 0.5rem;
        }
        .admin-login-card .card-body {
            padding: 2rem 2rem 2.5rem;
        }
        .admin-login-card .form-group {
            margin-bottom: 1.25rem;
        }
        .admin-login-card .form-control {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 0.875rem 1rem;
            font-size: 1rem;
        }
        .admin-login-card .form-control:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        .admin-login-card .form-control::placeholder {
            color: #94a3b8;
        }
        .admin-login-card .btn-primary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .admin-login-card .btn-primary:hover {
            background: linear-gradient(135deg, var(--admin-primary-dark) 0%, #3730a3 100%);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        }
        .admin-login-card .alert {
            border-radius: 12px;
            border: none;
        }
        .admin-login-card .alert ul {
            margin-bottom: 0;
            padding-left: 1.25rem;
        }
        .admin-login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        .admin-login-footer a {
            color: rgba(255,255,255,0.8);
            font-size: 0.875rem;
            text-decoration: none;
        }
        .admin-login-footer a:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="admin-login-wrapper">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
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

        <div class="card admin-login-card">
            <div class="card-header">
                <div class="logo-text">SMS<span>LORD</span></div>
                <span class="splash-description">Admin sign in</span>
            </div>
            <div class="card-body">
                <form action="{{ url('admin-login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <input class="form-control form-control-lg" name="username" type="text" placeholder="Username" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control form-control-lg" name="password" type="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
                </form>
            </div>
        </div>

        <div class="admin-login-footer">
            <a href="{{ url('/') }}">← Back to site</a>
        </div>
    </div>

    <script src="{{ asset('concept/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('concept/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
</body>
</html>
