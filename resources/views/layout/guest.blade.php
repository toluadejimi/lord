<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SMSLORD — fast, affordable SMS verification numbers worldwide.">
    <title>@yield('title', 'SMSLORD')</title>
    <link rel="icon" href="{{ static_asset('assets/images/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ static_asset('assets/fonts/inter/inter.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/fonts/tabler-icons.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="guest-body">
@yield('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
