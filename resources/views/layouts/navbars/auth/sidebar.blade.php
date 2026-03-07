<style>
.transition-mobile-sidebar {
    transition: transform 0.3s ease-in-out !important;
}
@media (max-width: 991.98px) {
    #sidenav-main {
        position: fixed !important;
        top: 0 !important;
        bottom: 0 !important;
        left: 0 !important; /* keep anchor */
        width: 250px !important;
        max-width: 80%;
        transform: translateX(-100%) !important; /* hidden */
        box-shadow: 0 0 10px rgba(0,0,0,.3);
        background: #fff;
    }
    #sidenav-main.active {
        transform: translateX(0) !important; /* slide in */
    }
    /* Hide logo in sidebar on mobile */
    #sidenav-main .navbar-brand {
        display: none !important;
    }
}
</style>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs ps bg-white fixed-start transition-mobile-sidebar" 
    id="sidenav-main"
    style="height: 100vh; overflow-y: auto;">
    <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/lifemedia_logo.png') }}" class="navbar-brand-img h-100" alt="...">
            </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="" id="sidenav-collapse-main" style="overflow: none;">
        <ul class="navbar-nav" style="overflow: none;">

            <li class="nav-item pb-2">
                <a class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center {{ Route::currentRouteName() == 'dashboard' ? 'bg-gradient-primary text-white' : '' }}">
                        <!-- Menambahkan ikon home Font Awesome -->
                        <i class="fas fa-home {{ Route::currentRouteName() == 'dashboard' ? 'text-white' : 'text-dark' }}"
                            style="font-size: 0.7rem;"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Users</h6>
            </li>
            <li class="nav-item pb-2">
                <a class="nav-link {{ Route::currentRouteName() == 'user-management' ? 'active' : '' }}"
                    href="{{ route('user-management') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center {{ Route::currentRouteName() == 'user-management' ? 'bg-gradient-primary text-white' : '' }}">
                        <!-- Menambahkan ikon users Font Awesome -->
                        <i class="fas fa-user {{ Route::currentRouteName() == 'user-management' ? 'text-white' : 'text-dark' }}"
                            style="font-size: 0.7rem;"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Management</span>
                </a>
            </li>

            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Data</h6>
            </li>

            <!-- CCTV Lokasi -->
            <li class="nav-item pb-2">
                <a class="nav-link {{ Route::currentRouteName() == 'menu-lokasi' ? 'active' : '' }}"
                    href="{{ route('menu-lokasi') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center {{ Route::currentRouteName() == 'menu-lokasi' ? 'bg-gradient-primary text-white' : '' }}">
                        <!-- CCTV Lokasi Icon (Lokasi) -->
                        <i class="fas fa-book-open {{ Route::currentRouteName() == 'menu-lokasi' ? 'text-white' : 'text-dark' }}"
                            style="font-size: 0.7rem;"></i>
                    </div>
                    <span class="nav-link-text ms-1">CCTV Lokasi</span>
                </a>
            </li>

            <li class="nav-item pb-2 mt-3">
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
                <button type="button"
                    class="nav-link border-0 bg-transparent d-flex align-items-center logout-btn">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-button-power text-danger"></i>
                    </div>
                    <span class="nav-link-text ms-1 text-danger">Logout</span>
                </button>
            </li>

            <script>
                document.querySelectorAll('.logout-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Anda akan Logout dari sini.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Saya Yakin!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('logout-form').submit();
                        }
                    });
                });
            });
            </script>
            

        </ul>
    </div>
</aside>