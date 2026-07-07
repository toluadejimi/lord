<!doctype html>
<html lang="en"><!-- [Head] start -->
<head><title>SMS LORD</title><!-- [Meta] -->
    <base href="{{ rtrim(url('/'), '/') }}/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
          content="SMSLORD NUMBER VERIFICATION">
    <meta name="keywords"
          content="SMS LORD VERIFICATION">
    <meta name="author" content="Phoenixcoded"><!-- [Favicon] icon -->
    <link rel="icon" href="{{ static_asset('assets/images/favicon.svg') }}" type="image/x-icon"><!-- [Font] Family -->
    <link rel="stylesheet" href="{{ static_asset('assets/fonts/inter/inter.css') }}" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ static_asset('assets/fonts/tabler-icons.min.css') }}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ static_asset('assets/fonts/feather.css') }}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ static_asset('assets/fonts/fontawesome.css') }}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ static_asset('assets/fonts/material.css') }}"><!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ static_asset('assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/smslord-theme.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
          integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
          crossorigin="anonymous"/>


    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .search-results {
            max-height: 300px;
            overflow-y: auto;
            position: absolute;
            width: 100%;
            background: #fff;
            border: 1px solid #ddd;
        }
        .search-results li {
            padding: 10px;
            cursor: pointer;
        }
        .search-results li:hover {
            background: #eee;
        }


        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }


        .search-container {
            position: relative;
            display: inline-block;
        }

        /* Side menu: no underline on links */
        .pc-sidebar .pc-link,
        .pc-sidebar .pc-link:hover,
        .pc-sidebar .pc-link:focus {
            text-decoration: none !important;
        }

    </style>




</head><!-- [Head] end --><!-- [Body] Start -->
<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr"
      data-pc-theme_contrast="" data-pc-theme="light" @auth class="has-customer-float-nav" @endauth><!-- [ Pre-loader ] start -->
<div class="page-loader">
    <div class="bar"></div>
</div><!-- [ Pre-loader ] End --><!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ url('/') }}" class="b-brand text-primary">
                <img src="{{ static_asset('assets/images/logo.svg') }}">
            </a>
        </div>
        <div class="navbar-content">


            @auth
                <div class="card pc-user-card">
                    <div class="card-body">

                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0"><img src="{{ static_asset('assets/images/user/avatar-1.jpg') }}"
                                                            alt="user-image"
                                                            class="user-avtar wid-45 rounded-circle"></div>
                            <div class="flex-grow-1 ms-3 me-2"><h6 class="mb-0">{{Auth::user()->username}}</h6>
                                <small>Customer</small>
                            </div>
                            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse"
                               href="#pc_sidebar_userlink">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-sort-outline"></use>
                                </svg>
                            </a></div>
                        <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                            <div class="pt-3">
                                <a href="#">
                                    <i class="ti ti-user">
                                    </i>
                                    <span>Profile</span>
                                </a>


                                <a href="{{ url('log-out') }}">
                                    <i class="ti ti-power">

                                    </i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @include('partials.customer-sidebar-nav')

            @else

                <div class="card">
                    <div class="card-body">


                        <a href="{{ url('register') }}" class="btn btn-dark w-100 my-3  btn-block">
                            Register
                        </a>

                        <a href="{{ url('login') }}" class="btn btn-primary w-100  btn-block">
                            Login
                        </a>


                    </div>

                </div>

            @endauth


        </div>
    </div>
</nav><!-- [ Sidebar Menu ] end --><!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper"><!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled"><!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <button type="button" class="pc-head-link ms-0 border-0 bg-transparent" id="sidebar-hide" aria-label="Toggle sidebar">
                        <i class="ti ti-menu-2"></i>
                    </button>
                </li>

                <li class="pc-h-item pc-sidebar-popup mob-header-brand-wrap">
                    <button type="button" class="pc-head-link ms-0 border-0 bg-transparent" id="mobile-collapse" aria-label="Open menu">
                        <i class="ti ti-menu-2"></i>
                    </button>
                    <a href="{{ route('dashboard') }}" class="mob-header-brand" aria-label="SMSLORD home">
                        <img src="{{ static_asset('assets/images/logo.svg') }}" alt="SMSLORD">
                    </a>
                </li>


            </ul>
        </div><!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled align-items-center">
                @auth
                <li class="pc-h-item d-md-none">
                    <a href="{{ url('fund-wallet') }}" class="smslord-header-wallet smslord-header-wallet--compact" title="Fund wallet">
                        <span class="wallet-icon-wrap"><i class="ti ti-wallet"></i></span>
                        <span class="wallet-amount">₦{{ number_format((float) Auth::user()->wallet, 0) }}</span>
                    </a>
                </li>
                <li class="pc-h-item d-none d-md-inline-block">
                    <a href="{{ url('fund-wallet') }}" class="smslord-header-wallet" title="Fund wallet">
                        <span class="wallet-icon-wrap"><i class="ti ti-wallet"></i></span>
                        <span>
                            <span class="wallet-fund-hint d-block">Wallet</span>
                            <span class="wallet-amount">₦{{ number_format((float) Auth::user()->wallet, 2) }}</span>
                        </span>
                    </a>
                </li>
                @endauth
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="javascript:void(0)"
                       role="button" aria-haspopup="false" aria-expanded="false">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-sun-1"></use>
                        </svg>
                    </a>


                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <a href="javascript:void(0)"
                           class="dropdown-item"
                           role="button"
                           onclick="layout_change('dark'); return false;">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-moon"></use>
                            </svg>
                            <span>Dark</span> </a>
                        <a href="javascript:void(0)" class="dropdown-item" role="button" onclick="layout_change('light'); return false;">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sun-1"></use>
                            </svg>
                            <span>Light</span> </a><a href="javascript:void(0)" class="dropdown-item" role="button" onclick="layout_change_default(); return false;">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-setting-2"></use>
                            </svg>
                            <span>Default</span>
                        </a>
                    </div>
                </li>


                <li class="dropdown pc-h-item header-user-profile">

                    <a
                        class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false"
                        data-bs-auto-close="outside" aria-expanded="false">

                        <img src="{{ static_asset('assets/images/user/avatar-2.jpg') }}"
                             alt="user-image" class="user-avtar">
                    </a>

                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        @auth
                            <div class="dropdown-header d-flex align-items-center justify-content-between"><h5
                                    class="m-0">
                                    Profile</h5></div>
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative"
                                     style="max-height: calc(100vh - 225px)">
                                    <div class="d-flex mb-1">
                                        <div class="flex-shrink-0"><img
                                                src="{{ static_asset('assets/images/user/avatar-2.jpg') }}"
                                                alt="user-image" class="user-avtar wid-35"></div>
                                        <div class="flex-grow-1 ms-3"><h6 class="mb-1">{{Auth::user()->username}} 🖖</h6>
                                            <span>{{Auth::user()->email}}</a></span>
                                        </div>
                                    </div>
                                    <hr class="border-secondary border-opacity-50">

                                    <p class="text-span">Manage</p>
                                    <a href="#"
                                       class="dropdown-item"><span><svg
                                                class="pc-icon text-muted me-2"><use
                                                    xlink:href="#custom-setting-outline"></use></svg> <span>Settings</span> </span></a>
                                    <a href="#" class="dropdown-item">
                                    <span><svg
                                            class="pc-icon text-muted me-2"><use xlink:href="#custom-share-bold"></use></svg> <span>Share</span> </span></a>
                                    <a href="{{ url('logout') }}" class="dropdown-item"><span>
                                        <svg
                                            class="pc-icon text-muted me-2"><use
                                                xlink:href="#custom-lock-outline"></use></svg> <span>Change Password</span></span></a>
                                    <hr class="border-secondary border-opacity-50">

                                    <hr class="border-secondary border-opacity-50">
                                    <div class="d-grid mb-3">
                                        <button class="btn btn-primary">
                                            <svg class="pc-icon me-2">
                                                <use xlink:href="#custom-logout-1-outline"></use>
                                            </svg>
                                            Logout
                                        </button>
                                    </div>

                                </div>
                            </div>

                        @else
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative"
                                     style="max-height: calc(100vh - 225px)">


                                    <hr class="border-secondary border-opacity-50">
                                    <div class="d-grid mb-3">
                                        <a href="{{ url('login') }}" class="btn btn-primary my-2">
                                            Login
                                        </a>

                                        <a href="{{ url('register') }}" class="btn btn-dark">
                                            Register
                                        </a>


                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                </li>


            </ul>
        </div>
    </div>
</header>
<div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement"
     aria-labelledby="announcementLabel">
    <div class="offcanvas-header"><h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

</div><!-- [ Header ] end --><!-- [ Main Content ] start -->

@php($siteConfig = app(\App\Services\AppConfigService::class))
@if($siteConfig->getBool('site_notification_active'))
<div class="alert alert-info text-center mb-0 rounded-0">
    <strong>{{ $siteConfig->get('site_notification_title') }}</strong>
    {{ $siteConfig->get('site_notification_message') }}
</div>
@endif

@yield('content')

@stack('page-scripts')

@auth
@include('partials.numbers-bottom-sheet')
@include('partials.customer-float-nav')
@else
<footer class="pc-footer smslord-footer smslord-footer--guest">
    <div class="footer-wrapper">
        <div class="footer-bottom" style="border:0;padding:1rem 1.25rem;">
            <span>&copy; {{ date('Y') }} SMSLORD. All rights reserved.</span>
            <span>
                <a href="{{ url('policy') }}">Privacy</a>
                &middot;
                <a href="{{ url('login') }}">Login</a>
            </span>
        </div>
    </div>
</footer>
@endauth

@auth
<script>
(function () {
    var root = document.getElementById('dash-numbers-sheet-root');
    if (!root) return;

    var openers = document.querySelectorAll('[data-dash-open="numbers-sheet"]');
    var closers = root.querySelectorAll('[data-dash-close="numbers-sheet"]');
    var floatNav = document.querySelector('.smslord-float-nav');
    var lastFocus = null;

    function openSheet() {
        lastFocus = document.activeElement;
        if (floatNav) {
            floatNav.classList.add('is-hidden');
        }
        root.hidden = false;
        requestAnimationFrame(function () {
            root.classList.add('is-open');
        });
        document.body.classList.add('dash-sheet-open');
    }

    function closeSheet() {
        root.classList.remove('is-open');
        document.body.classList.remove('dash-sheet-open');
        window.setTimeout(function () {
            if (!root.classList.contains('is-open')) {
                root.hidden = true;
            }
            if (floatNav) {
                floatNav.classList.remove('is-hidden');
            }
        }, 280);
        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    openers.forEach(function (btn) {
        btn.addEventListener('click', openSheet);
    });

    closers.forEach(function (el) {
        el.addEventListener('click', closeSheet);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && root.classList.contains('is-open')) {
            closeSheet();
        }
    });
})();
</script>
@endauth


<!-- Required Js -->
<script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script src="{{ static_asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ static_asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ static_asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ static_asset('assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ static_asset('assets/js/pcoded.js') }}"></script>
<script src="{{ static_asset('assets/js/plugins/feather.min.js') }}"></script>
<script>
(function () {
    function cleanThemeHash() {
        if (window.location.hash === '#!' || window.location.hash === '#') {
            history.replaceState(null, '', window.location.pathname + window.location.search);
        }
    }

    cleanThemeHash();

    var savedTheme = localStorage.getItem('smslord_theme');

    if (typeof layout_change === 'function') {
        if (savedTheme === 'dark') {
            layout_change('dark');
        } else if (savedTheme === 'default' && typeof layout_change_default === 'function') {
            layout_change_default();
        } else {
            layout_change('light');
            if (!savedTheme) {
                localStorage.setItem('smslord_theme', 'light');
            }
        }

        var originalLayoutChange = layout_change;
        window.layout_change = function (theme) {
            if (theme === 'dark' || theme === 'light') {
                localStorage.setItem('smslord_theme', theme);
            }
            var result = originalLayoutChange(theme);
            cleanThemeHash();
            return result;
        };
    }

    if (typeof layout_change_default === 'function') {
        var originalLayoutChangeDefault = layout_change_default;
        window.layout_change_default = function () {
            localStorage.setItem('smslord_theme', 'default');
            var result = originalLayoutChangeDefault();
            cleanThemeHash();
            return result;
        };
    }

    document.addEventListener('click', function (e) {
        var toggle = e.target.closest('#mobile-collapse, #sidebar-hide, [data-smslord-toggle]');
        if (toggle) {
            e.preventDefault();
            return;
        }
        var link = e.target.closest('a[href="javascript:void(0)"], a[href="#"]');
        if (!link) return;
        if (link.getAttribute('onclick') || link.closest('.pc-h-dropdown, .pct-offcanvas, .theme-color, .theme-main-layout, .pc-sidebar')) {
            e.preventDefault();
            cleanThemeHash();
        }
    });

    function smslordCloseMobileMenu() {
        var sidebar = document.querySelector('.pc-sidebar');
        if (!sidebar) return;
        sidebar.classList.remove('mob-sidebar-active');
        document.body.classList.remove('smslord-menu-open');
        var overlay = sidebar.querySelector('.pc-menu-overlay');
        if (overlay) overlay.remove();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.pc-sidebar .pc-link-modern, .pc-sidebar .pc-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 1024) {
                    smslordCloseMobileMenu();
                }
            });
        });
    });
})();
</script>
<script>layout_theme_contrast_change('false');</script>
<script>change_box_container('false');</script>
<script>layout_caption_change('true');</script>
<script>layout_rtl_change('false');</script>
<script>preset_change('preset-1');</script>
<script>
    localStorage.setItem('layout', 'vertical');
    if (typeof main_layout_change === 'function') {
        main_layout_change('vertical');
    }
</script>
<div class="pct-c-btn"><a href="javascript:void(0)" data-bs-toggle="offcanvas"
                          data-bs-target="#offcanvas_pc_layout"><i class="ph-duotone ph-gear-six"></i></a></div>
<div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
    <div class="offcanvas-header"><h5 class="offcanvas-title">Settings</h5>
        <button type="button" class="btn btn-icon btn-link-danger ms-auto" data-bs-dismiss="offcanvas"
                aria-label="Close"><i class="ti ti-x"></i></button>
    </div>
    <div class="pct-body customizer-body">
        <div class="offcanvas-body py-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="pc-dark"><h6 class="mb-1">Theme Mode</h6>
                        <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
                        <div class="row theme-color theme-layout">
                            <div class="col-4">
                                <div class="d-grid">
                                    <button class="preset-btn btn active" data-value="true"
                                            onclick="layout_change('light');" data-bs-toggle="tooltip" title="Light">
                                        <svg class="pc-icon text-warning">
                                            <use xlink:href="#custom-sun-1"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-grid">
                                    <button class="preset-btn btn" data-value="false" onclick="layout_change('dark');"
                                            data-bs-toggle="tooltip" title="Dark">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-moon"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-grid">
                                    <button class="preset-btn btn" data-value="default"
                                            onclick="layout_change_default();" data-bs-toggle="tooltip"
                                            title="Automatically sets the theme based on user's operating system's color scheme.">
                                        <span class="pc-lay-icon d-flex align-items-center justify-content-center"><i
                                                class="ph-duotone ph-cpu"></i></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Theme Contrast</h6>
                    <p class="text-muted text-sm">Choose theme contrast</p>
                    <div class="row theme-contrast">
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn" data-value="true"
                                        onclick="layout_theme_contrast_change('true');" data-bs-toggle="tooltip"
                                        title="True">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-mask"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn active" data-value="false"
                                        onclick="layout_theme_contrast_change('false');" data-bs-toggle="tooltip"
                                        title="False">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-mask-1-outline"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Custom Theme</h6>
                    <p class="text-muted text-sm">Choose your primary theme color</p>
                    <div class="theme-color preset-color"><a href="javascript:void(0)" data-bs-toggle="tooltip"
                                                             title="Blue" class="active" data-value="preset-1"><i
                                class="ti ti-checks"></i></a> <a href="javascript:void(0)"
                                                                 data-bs-toggle="tooltip" title="Indigo"
                                                                 data-value="preset-2"><i class="ti ti-checks"></i></a>
                        <a href="javascript:void(0)" data-bs-toggle="tooltip" title="Purple"
                           data-value="preset-3"><i class="ti ti-checks"></i></a> <a href="javascript:void(0)"
                                                                                     data-bs-toggle="tooltip"
                                                                                     title="Pink" data-value="preset-4"><i
                                class="ti ti-checks"></i></a> <a href="javascript:void(0)"
                                                                 data-bs-toggle="tooltip" title="Red"
                                                                 data-value="preset-5"><i class="ti ti-checks"></i></a>
                        <a href="javascript:void(0)" data-bs-toggle="tooltip" title="Orange"
                           data-value="preset-6"><i class="ti ti-checks"></i></a> <a href="javascript:void(0)"
                                                                                     data-bs-toggle="tooltip"
                                                                                     title="Yellow"
                                                                                     data-value="preset-7"><i
                                class="ti ti-checks"></i></a> <a href="javascript:void(0)"
                                                                 data-bs-toggle="tooltip" title="Green"
                                                                 data-value="preset-8"><i class="ti ti-checks"></i></a>
                        <a href="javascript:void(0)" data-bs-toggle="tooltip" title="Teal"
                           data-value="preset-9"><i class="ti ti-checks"></i></a> <a href="javascript:void(0)"
                                                                                     data-bs-toggle="tooltip"
                                                                                     title="Cyan"
                                                                                     data-value="preset-10"><i
                                class="ti ti-checks"></i></a></div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Theme layout</h6>
                    <p class="text-muted text-sm">Choose your layout</p>
                    <div class="theme-main-layout d-flex align-center gap-1 w-100"><a href="javascript:void(0)"
                                                                                      data-bs-toggle="tooltip"
                                                                                      title="Vertical" class="active"
                                                                                      data-value="vertical"><img
                                src="{{ static_asset('assets/images/customizer/caption-on.svg') }}" alt="img"
                                class="img-fluid"> </a><a
                            href="javascript:void(0)" data-bs-toggle="tooltip" title="Horizontal"
                            data-value="horizontal"><img
                                src="{{ static_asset('assets/images/customizer/horizontal.svg') }}" alt="img"
                                class="img-fluid"> </a><a href="javascript:void(0)"
                                                          data-bs-toggle="tooltip"
                                                          title="Color Header"
                                                          data-value="color-header"><img
                                src="{{ static_asset('assets/images/customizer/color-header.svg') }}" alt="img"
                                class="img-fluid"> </a><a
                            href="javascript:void(0)" data-bs-toggle="tooltip" title="Compact"
                            data-value="compact"><img src="{{ static_asset('assets/images/customizer/compact.svg') }}"
                                                      alt="img"
                                                      class="img-fluid"> </a><a href="javascript:void(0)"
                                                                                data-bs-toggle="tooltip" title="Tab"
                                                                                data-value="tab"><img
                                src="{{ static_asset('assets/images/customizer/tab.svg') }}" alt="img"
                                class="img-fluid"></a></div>
                </li>
                <li class="list-group-item"><h6 class="mb-1">Sidebar Caption</h6>
                    <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
                    <div class="row theme-color theme-nav-caption">
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn-img btn active" data-value="true"
                                        onclick="layout_caption_change('true');" data-bs-toggle="tooltip"
                                        title="Caption Show"><img
                                        src="{{ static_asset('assets/images/customizer/caption-on.svg') }}"
                                        alt="img" class="img-fluid"></button>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid">
                                <button class="preset-btn btn-img btn" data-value="false"
                                        onclick="layout_caption_change('false');" data-bs-toggle="tooltip"
                                        title="Caption Hide"><img
                                        src="{{ static_asset('assets/images/customizer/caption-off.svg') }}"
                                        alt="img" class="img-fluid"></button>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="pc-rtl"><h6 class="mb-1">Theme Layout</h6>
                        <p class="text-muted text-sm">LTR/RTL</p>
                        <div class="row theme-color theme-direction">
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn active" data-value="false"
                                            onclick="layout_rtl_change('false');" data-bs-toggle="tooltip" title="LTR">
                                        <img src="{{ static_asset('assets/images/customizer/ltr.svg') }}" alt="img"
                                             class="img-fluid">
                                    </button>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn" data-value="true"
                                            onclick="layout_rtl_change('true');" data-bs-toggle="tooltip" title="RTL">
                                        <img src="{{ static_asset('assets/images/customizer/rtl.svg') }}" alt="img"
                                             class="img-fluid">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item pc-box-width">
                    <div class="pc-container-width"><h6 class="mb-1">Layout Width</h6>
                        <p class="text-muted text-sm">Choose Full or Container Layout</p>
                        <div class="row theme-color theme-container">
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn active" data-value="false"
                                            onclick="change_box_container('false')" data-bs-toggle="tooltip"
                                            title="Full Width"><img
                                            src="{{ static_asset('assets/images/customizer/full.svg') }}" alt="img"
                                            class="img-fluid"></button>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-grid">
                                    <button class="preset-btn btn-img btn" data-value="true"
                                            onclick="change_box_container('true')" data-bs-toggle="tooltip"
                                            title="Fixed Width"><img
                                            src="{{ static_asset('assets/images/customizer/fixed.svg') }}"
                                            alt="img" class="img-fluid"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="d-grid">
                        <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>
    if (typeof preset_change === 'function') {
        preset_change('preset-1');
    }
    if (typeof main_layout_change === 'function') {
        localStorage.setItem('layout', 'vertical');
        main_layout_change('vertical');
    }
</script>




<script>
    // Toggle for country dropdown
    function toggleDropdown() {
        document.getElementById('dropdown').style.display = 'block';
    }

    // Toggle for service dropdown
    function toggleDropdownservice() {
        document.getElementById('dropdownservice').style.display = 'block';
    }

    // Filter for country items
    function filterItems() {
        const searchInput = document.getElementById('search').value.toLowerCase();
        const items = document.querySelectorAll('#dropdown .item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Filter for service items
    function filterItemsservice() {
        const searchInput = document.getElementById('searchservice').value.toLowerCase();
        const items = document.querySelectorAll('#dropdownservice .item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchInput)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Select country
    function selectCountry(element) {
        document.getElementById('search').value = element.textContent;
        document.getElementById('dropdown').style.display = 'none';
        document.getElementById('selectedID').value = element.getAttribute('data-id');
    }

    // Select service
    function selectService(element) {
        document.getElementById('searchservice').value = element.textContent;
        document.getElementById('dropdownservice').style.display = 'none';
        document.getElementById('serviceID').value = element.getAttribute('data-id');
    }

    // Close dropdowns if clicked outside
    document.addEventListener('click', function (event) {
        const searchContainerCountry = document.querySelector('#search');
        const searchContainerService = document.querySelector('#searchservice');

        if (!searchContainerCountry.contains(event.target)) {
            document.getElementById('dropdown').style.display = 'none';
        }

        if (!searchContainerService.contains(event.target)) {
            document.getElementById('dropdownservice').style.display = 'none';
        }
    });
</script>
</body>
<!-- [Body] end -->
</html>
