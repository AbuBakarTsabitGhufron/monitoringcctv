<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('css/lokasi.css') }}" rel="stylesheet">

@php
    // Hapus semua query Eloquent di sini. Data disuplai dari controller:
    // $groupedCctvs, $jumlahCCTV, $jumlahLokasi, $jumlahWilayah, $jumlahCCTVaktif, $namaWilayahLengkap
@endphp
<style>
    .sidebar-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

#sidebar-scroll-area {
    flex: 1; /* biar bagian menu isi ruang sisa */
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

/* footer selalu nempel bawah */
.sidebar-footer {
    margin-top: auto;
    background: linear-gradient(180deg, var(--primary-color) 0%, #34495e 100%);
    padding: 10px 15px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <button class="btn btn-outline-primary me-3" type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand">
                <div>
                    <i class="fas fa-video text-primary me-2"></i>
                    CCTV LOKASI
                </div>
                <div style="font-size: 0.7rem; color: #6c757d; font-weight: normal;">
                    Memantau Kondisi Keamanan Lokasi DIY
                </div>
            </div>
            <div class="ms-auto navbar-status">
                {{--
                <div class="online-row">
                    <span class="badge bg-success">
                        <i class="fas fa-circle me-1"></i>Online
                    </span>
                </div>
                --}}
                <div class="time-row text-muted pt-1">
                    <i class="fas fa-clock me-1"></i>
                    <span id="currentTime"></span>
                </div>
            </div>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center px-3 py-3">
            <h4 class="m-0 text-white">
                <i class="fas fa-video me-2"></i>CCTV Control
            </h4>
            <button class="btn btn-link text-white p-0" id="sidebarClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="sidebar-content">
            <div class="px-3 pb-3 pt-3">
                <div class="row g-2">
                    <div class="col-8">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control form-control-sm" id="sidebarSearchInput" placeholder="Cari..." style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; font-size: 0.8rem; padding: 8px 15px 8px 35px;">
                        </div>
                    </div>
                    <div class="col-4">
                        <button id="sidebarHideAllBtn"
                                class="btn btn-sm hide-all-btn w-100"
                                style="font-size: 0.7rem; padding: 8px 5px;"
                                disabled>
                            <i id="sidebarHideAllIcon" class="fas fa-eye-slash me-1"></i>
                            <span id="sidebarHideAllText">Sembunyikan</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="sidebar-nav-wrapper d-flex flex-column h-100 px-2">
                <div class="flex-grow-1 overflow-auto" id="sidebar-scroll-area">
                    <ul class="sidebar-nav">
                        <li class="sidebar-item">
                            <div class="sidebar-link d-flex justify-content-between align-items-center"
                                onclick="toggleDropdown('cctvDropdown')">
                                <span><i class="fas fa-video me-2"></i>CCTV Monitoring</span>
                                <i class="fas fa-chevron-right" id="cctvChevron"></i>
                            </div>
                            <div class="dropdown-content" id="cctvDropdown">
                                @foreach ($groupedCctvs as $wilayah => $lokasiGroup)
                                    <div class="dropdown-item" onclick="toggleSubDropdown('{{ Str::slug($wilayah) }}Dropdown')">
                                        <i class="fas fa-map-marker-alt me-2"></i>{{ $namaWilayahLengkap[$wilayah] ?? $wilayah }}
                                        <i class="fas fa-chevron-right float-end" id="{{ Str::slug($wilayah) }}Chevron"></i>
                                    </div>

                                    <div class="dropdown-content sub-dropdown ps-3" id="{{ Str::slug($wilayah) }}Dropdown">
                                        @foreach ($lokasiGroup as $namaLokasi => $cctvs)
                                            @php $lokasiSlug = Str::slug($namaLokasi); @endphp
                                            <div class="dropdown-item d-flex justify-content-between align-items-center"
                                                onclick="toggleSchool('{{ $lokasiSlug }}')">
                                                <span><i class="fas fa-school me-2"></i>{{ $namaLokasi }}</span>
                                                <div>
                                                    <i id="eye-{{ $lokasiSlug }}" class="fas fa-eye-slash me-2"
                                                        onclick="event.stopPropagation(); toggleAllSchoolCCTV('{{ $namaLokasi }}')"
                                                        title="Tampilkan/sembunyikan CCTV {{ $namaLokasi }}"
                                                        style="cursor: pointer; opacity: 0.7;"></i>
                                                    <i class="fas fa-chevron-right" id="{{ $lokasiSlug }}Chevron"></i>
                                                </div>
                                            </div>
                                            <div class="dropdown-content sub-dropdown ps-4" id="{{ $lokasiSlug }}">
                                                @foreach ($cctvs as $cctv)
                                                    <div class="dropdown-item d-flex justify-content-between align-items-center" style="padding: 8px 12px;">
                                                        <div class="d-flex align-items-center flex-grow-1"
                                                            onclick="selectCCTV('{{ $cctv->lokasi->nama_lokasi }}', '{{ $cctv->nama_cctv }}')"
                                                            style="cursor: pointer;">
                                                            <i class="fas fa-camera me-2"></i>
                                                            <span>{{ $cctv->nama_cctv }}</span>
                                                        </div>
                                                        <div class="form-check form-switch me-0" style="min-width: 40px;">
                                                            <input class="form-check-input cctv-point-checkbox"
                                                                type="checkbox"
                                                                id="checkbox-{{ Str::slug($cctv->lokasi->nama_lokasi . '-' . $cctv->nama_cctv) }}"
                                                                data-card-id="{{ Str::slug($cctv->lokasi->nama_lokasi . '-' . $cctv->nama_cctv) }}"
                                                                onchange="toggleCCTVFromSidebar(this)"
                                                                style="margin: 0; cursor: pointer;"
                                                                onclick="event.stopPropagation();"
                                                                {{ $cctv->active ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="mt-2 pt-2 pb-2 border-top border-white border-opacity-10">
                    <ul class="sidebar-nav">
                        @auth
                            @if (Auth::user()->role === 'admin')
                                <li class="sidebar-item">
                                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                                        <span><i class="fas fa-tachometer-alt me-3"></i>Dashboard Admin</span>
                                    </a>
                                </li>
                            @endif
                        @endauth
                        <li class="sidebar-item">
                            <a class="sidebar-link text-danger" onclick="confirmLogout()">
                                <span><i class="fas fa-sign-out-alt me-3"></i> Logout</span>
                            </a>
                        </li>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <script>
                        function confirmLogout() {
                            Swal.fire({
                                title: 'Apakah anda ingin logout?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Ya',
                                cancelButtonText: 'Tidak'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('logoutForm').submit();
                                }
                            });
                        }
                        </script>
                    </ul>
                </div>
            </div>
        </div>
        <div class="sidebar-footer px-3 pt-1 pb-3 border-top border-white border-opacity-10">
            <div class="mt-3">
                <a href="{{ route('profil.pengguna') }}" class="btn btn-sm btn-outline-light w-100" style="font-size: 0.8rem;">
                    <i class="fas fa-user-cog me-1"></i>Profil Pengguna
                </a>
            </div>
        </div>
    </div>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0" style="font-size: 24pt;" id="cctvCount">{{ $jumlahCCTV }}</h3>
                                    <p class="mb-0 small">Total CCTV</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-video fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #27ae60, #229954);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0" style="font-size: 24pt;" id="schoolCount">{{ $jumlahLokasi }}</h3>
                                    <p class="mb-0 small">Total Lokasi</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1" id="regionCount">{{ $jumlahWilayah }}</h3>
                                    <p class="mb-0 small">Total Wilayah</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1" id="activeCCTVCountCard">{{ $jumlahCCTVaktif }}</h3>
                                    <p class="mb-0 small">CCTV Aktif</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="control-panel">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-3">
                            <i class="fas fa-video text-primary me-2"></i>
                            Live CCTV Monitoring
                        </h5>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('panorama.panorama') }}" class="btn btn-sm btn-outline-primary me-2" title="Buka Panorama Publik" aria-label="Buka Panorama Publik">
                            <i class="fas fa-camera-retro me-1"></i> CCTV Panorama
                        </a>
                        <div class="badge bg-success fs-6 mb-2">
                            <i class="fas fa-shield-alt me-1"></i>
                            Sistem Normal
                        </div>
                        <p class="text-muted mb-0 small">
                            <i class="fas fa-circle text-success me-1"></i>
                            Monitoring real-time dari 5 kabupaten/kota
                        </p>
                    </div>
                </div>
            </div>
            <div class="cctv-grid" id="cctvGrid">
                <!-- Render on-demand: grid dikosongkan -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/dashboard_public.js') }}"></script>

    <script>
        // Index dari controller
        window.cctvIndex = @json($cctvIndex ?? []);
        window.cctvById = {};
        Object.values(window.cctvIndex).forEach(list => (list || []).forEach(it => window.cctvById[it.cardId] = it));

        function slugify(txt) {
            return (txt || '').toString().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, '');
        }
        function shortNameOf(name) {
            const words = (name || '').trim().split(/\s+/);
            return words.length > 3 ? words.map(w => (w[0]||'').toUpperCase()).join('') : (name || '');
        }

        window.toggleAllSchoolCCTV = function(namaLokasi) {
            const lokasiSlug = slugify(namaLokasi);
            const cbs = (document.getElementById(lokasiSlug) || document).querySelectorAll('.cctv-point-checkbox');

            const anyVisible = Array.from(document.querySelectorAll(`#cctvGrid .cctv-card[data-lokasi="${lokasiSlug}"]`))
                .some(card => card.style.display !== 'none');
            const targetShow = !anyVisible;

            cbs.forEach(cb => {
                cb.checked = targetShow;
                const id = cb.dataset.cardId;
                if (targetShow) {
                    if (ensureCardRenderedById(id)) {
                        const cardEl = document.getElementById(id);
                        showCard(cardEl);
                        const cardCheckbox = cardEl.querySelector('.toggle-switch input');
                        if (cardCheckbox) cardCheckbox.checked = true;
                    }
                } else {
                    const cardEl = document.getElementById(id);
                    hideCard(cardEl);
                    if (cardEl) {
                        const cardCheckbox = cardEl.querySelector('.toggle-switch input');
                        if (cardCheckbox) cardCheckbox.checked = false;
                    }
                }
            });

            updateSchoolEyeIcon(lokasiSlug);
            updateHideAllButtonState();
            updateActiveCCTVCount();
        };

        window.selectCCTV = function(namaLokasi, namaTitik) {
            const lokasiSlug = slugify(namaLokasi);
            const list = window.cctvIndex[lokasiSlug] || [];
            const found = list.find(x => (x.titik || '').toLowerCase() === (namaTitik || '').toLowerCase());
            if (!found) return;

            if (!ensureCardRenderedById(found.cardId)) return;
            const el = document.getElementById(found.cardId);
            if (!el) return;

            const currentlyVisible = el.style.display !== 'none';
            if (currentlyVisible) {
                hideCard(el);
                const cb = document.getElementById('checkbox-' + found.cardId);
                if (cb) cb.checked = false;
            } else {
                showCard(el);
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const cb = document.getElementById('checkbox-' + found.cardId);
                if (cb) cb.checked = true;
            }
            updateSchoolEyeIcon(lokasiSlug);
            updateHideAllButtonState();
            updateActiveCCTVCount();
        };

        document.getElementById('sidebarHideAllBtn').addEventListener('click', function() {
            if (this.disabled) return;
            hideAllVisibleCCTV();
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Sinkron awal ikon mata per-lokasi
            document.querySelectorAll('[id^="eye-"]').forEach(icon => {
                const slug = icon.id.replace('eye-', '');
                updateSchoolEyeIcon(slug);
            });
            updateHideAllButtonState();
            updateActiveCCTVCount(); // tampilkan jumlah aktif live saat awal
        });
    </script>
</body>