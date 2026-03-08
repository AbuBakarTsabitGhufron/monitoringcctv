<!DOCTYPE html>
@if (\Request::is('rtl'))
    <html dir="rtl" lang="ar">
@else
    <html lang="en">
@endif

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="weather-api-key" content="{{ env('WEATHER_API_KEY') }}">
    


    @if (env('IS_DEMO'))
        <x-demo-metas></x-demo-metas>
    @endif

    <link rel="icon" href="{{ asset('lifemedia_logo.png') }}?v={{ time() }}" type="image/png">

    <title>Admin Monitoring CCTV</title>

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    

    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.3') }}" rel="stylesheet" />
    <style>
        /* Mobile top bar + sidebar */
        .mobile-topbar { display:none; }
        @media (max-width: 991.98px) {
                .mobile-topbar {
                    display:block;
                    position:fixed;
                    top:0; left:0; right:0;
                    height:70px;
                    background:transparent;
                    border-bottom:1px solid transparent;
                    z-index:1300;
                    padding:12px 16px;
                    transition:background .35s ease, box-shadow .35s ease, border-color .35s ease, backdrop-filter .35s ease;
                }
                body.has-mobile-topbar { padding-top:70px; }
            /* Detached state after scroll */
                .mobile-topbar.mobile-detached {
                    background:#ffffffcc; /* slightly translucent */
                    backdrop-filter: blur(6px);
                    -webkit-backdrop-filter: blur(6px);
                    border-bottom:1px solid #e5e5e5;
                    box-shadow:0 2px 4px -1px rgba(0,0,0,.18), 0 8px 16px -6px rgba(0,0,0,.25);
                }
                .mobile-topbar.mobile-detached::after {
                    content:"";
                    position:absolute;
                    left:0; right:0; bottom:-1px;
                    height:10px;
                    pointer-events:none;
                    opacity:.35;
                }
                /* When sidebar open keep solid background and REMOVE shadow & border */
                .mobile-topbar.nav-open {
                    background:#ffffff !important;
                    border-bottom:none !important;
                    box-shadow:none !important;
                }
            #sidenav-main {
                position: fixed;
                top: 0;
                bottom: 0;
                left: -300px; /* Sembunyikan di luar layar */
                width: 280px;
                transition: left 0.3s ease;
                z-index: 1050;
            }
            
            #sidenav-main.active {
                left: 0; /* Tampilkan sidebar */
            }
            
        }
        /* Sidebar layering */
        #sidenav-main { z-index:1050; }
        
        #sidebar-overlay {
            display: none; 
            position: fixed; 
            inset: 0; 
            background: rgba(0,0,0,.35); 
            z-index: 1049; /* Di bawah sidebar */
        }
        #sidenav-main.active + #sidebar-overlay { display:block; }

        /* Custom Hamburger Button */
        .hamburger-btn { position:relative; width:42px; height:42px; border:none; background:transparent; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; transition:background .25s ease; }
        .hamburger-btn:focus-visible { outline:2px solid #4c8dff; outline-offset:2px; }
        .hamburger-btn:hover { background:#f3f5f7; }
        .hamburger-lines { position:relative; width:24px; height:18px; }
        .hamburger-lines span { position:absolute; left:0; width:100%; height:3px; background:#111; border-radius:2px; transition:transform .35s cubic-bezier(.68,-0.55,.27,1.55), top .25s ease, opacity .25s ease, background .25s ease; }
        .hamburger-lines span:nth-child(1){ top:0; }
        .hamburger-lines span:nth-child(2){ top:7.5px; }
        .hamburger-lines span:nth-child(3){ top:15px; }
        .hamburger-btn.is-open .hamburger-lines span:nth-child(1){ top:7.5px; transform:rotate(45deg); }
        .hamburger-btn.is-open .hamburger-lines span:nth-child(2){ opacity:0; transform:translateX(8px); }
        .hamburger-btn.is-open .hamburger-lines span:nth-child(3){ top:7.5px; transform:rotate(-45deg); }
        .hamburger-btn.is-open { background:#eef1f4; }
        .hamburger-btn.is-open .hamburger-lines span { background:#e91e63; }
        @media (prefers-reduced-motion: reduce){
            .hamburger-lines span, .hamburger-btn.is-open .hamburger-lines span { transition:none; }
        }
    </style>
</head>

<body class="{{ Request::is('login') ? 'login-page' : '' }} g-sidenav-show bg-gray-100 {{ \Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '') }}">
    @auth
        @if (!Request::is('cctv-publik'))
            <!-- Desktop Navbar -->
            <div class="d-none d-lg-block">
                @include('layouts.navbars.auth.nav')
            </div>
            <!-- Mobile Navbar -->
            <nav class="mobile-topbar d-lg-none">
                <div class="d-flex align-items-center justify-content-between h-100">
                    <button class="hamburger-btn" id="mobileSidebarToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="sidenav-main" type="button">
                        <span class="hamburger-lines" aria-hidden="true">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </button>
                    <span class="fw-semibold small">Admin Monitoring CCTV</span>
                    <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('lokasi.index') : route('home') }}" class="d-inline-flex align-items-center p-2">
                        <img src="{{ asset('images/lifemedia_logo.png') }}" alt="Logo" style="height:32px; width:auto;">
                    </a>
                </div>
            </nav>
            @include('layouts.navbars.auth.sidebar')
            <div id="sidebar-overlay" class="d-lg-none"></div>
        @endif
    @endauth

    
    @auth
        @yield('auth')
    @endauth

    @guest
        @yield('guest')
    @endguest

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
            class="position-fixed bg-success rounded text-white text-sm py-2 px-4"
            style="top: 1rem; right: 1rem; z-index: 9999;">
            <p class="m-0">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>

    @stack('rtl')
    @stack('dashboard')

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scriptsku')

    @if (session('message'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('message') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('mobileSidebarToggle');
        const sidebar = document.getElementById('sidenav-main');
        const overlay = document.getElementById('sidebar-overlay');
    const mobileTopbar = document.querySelector('.mobile-topbar');
        // Add topbar padding indicator
        if (window.innerWidth < 992) document.body.classList.add('has-mobile-topbar');

        function toggleSidebar(forceClose = false) {
            if (!sidebar) return;
            const willOpen = forceClose ? false : !sidebar.classList.contains('active');
            if (willOpen) {
                sidebar.classList.add('active');
                toggleBtn?.classList.add('is-open');
                mobileTopbar?.classList.add('nav-open');
                toggleBtn?.setAttribute('aria-expanded', 'true');
                if (overlay) overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('active');
                toggleBtn?.classList.remove('is-open');
                toggleBtn?.setAttribute('aria-expanded', 'false');
                toggleBtn?.classList.remove('is-open');
                mobileTopbar?.classList.remove('nav-open');
                if (overlay) overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Safety: ensure left off-canvas only on mobile on load
        function ensureInitialState() {
            if (window.innerWidth < 992) {
                document.body.classList.add('has-mobile-topbar');
                sidebar?.classList.remove('active');
                overlay && (overlay.style.display = 'none');
                toggleBtn?.setAttribute('aria-expanded', 'false');
            } else {
                // Desktop: clean inline styles
                document.body.style.overflow = '';
                document.body.classList.remove('has-mobile-topbar');
            }
        }

        ensureInitialState();
        window.addEventListener('resize', ensureInitialState);

        // Handle detach effect on scroll (only mobile)
        function handleScrollDetach(){
            if (!mobileTopbar) return;
            if (window.innerWidth >= 992) { mobileTopbar.classList.remove('mobile-detached'); return; }
            const scrolled = window.scrollY || document.documentElement.scrollTop;
            if (scrolled > 12) mobileTopbar.classList.add('mobile-detached');
            else mobileTopbar.classList.remove('mobile-detached');
        }
        handleScrollDetach();
        window.addEventListener('scroll', handleScrollDetach, { passive:true });

        toggleBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
        overlay?.addEventListener('click', () => toggleSidebar(true));

        // Close when pressing ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') toggleSidebar(true);
        });
    });
</script>
</html>