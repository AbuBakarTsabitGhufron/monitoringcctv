@extends('layouts.app')

@php
    // --- Pemanggilan Database Asli (Sesuai Permintaan Tidak Diubah) ---
    use App\Models\panorama;
    use Illuminate\Support\Str;

    $groupedCctvs = panorama::select('id', 'namaWilayah', 'namaTitik', 'link')
        ->orderBy('namaWilayah', 'asc')
        ->orderBy('namaTitik', 'asc')
        ->get()
        ->groupBy('namaWilayah'); // Hanya group by wilayah saja

    // --- Penambahan Logika untuk Menyuplai Data ke Template Baru ---
    // Variabel di bawah ini dibuat dari hasil query di atas tanpa mengubahnya.
    
    // 1. Ambil semua item panorama untuk perhitungan dan pembuatan index
    $allPanoramas = $groupedCctvs->flatten();

    // 2. Buat JavaScript index untuk rendering kartu CCTV on-demand
    $panoramaIndex = [];
    foreach ($allPanoramas as $item) {
        $wilayahSlug = Str::slug($item->namaWilayah);
        $cardId = Str::slug($item->namaWilayah . '-' . $item->namaTitik);
        $panoramaIndex[$wilayahSlug][] = [
            'cardId'    => $cardId,
            'wilayah'   => $item->namaWilayah,
            'titik'     => $item->namaTitik,
            'link'      => $item->link,
        ];
    }

    // 3. Hitung statistik untuk ditampilkan di kartu ringkasan
    $jumlahPanorama = $allPanoramas->count();
    $jumlahWilayah = $groupedCctvs->count();

    // 4. Map nama wilayah singkat ke nama lengkap untuk tampilan
    $namaWilayahLengkap = [
        'KABUPATEN GK' => 'KAB GUNUNG KIDUL',
        'KABUPATEN KP' => 'KAB KULONPROGO',
        'KABUPATEN BANTUL' => 'KAB BANTUL',
        'KABUPATEN SLEMAN' => 'KAB SLEMAN',
        'KOTA YOGYAKARTA' => 'KOTA YOGYAKARTA'
    ];
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link href="{{ asset("css/lokasi.css")') }}" rel="stylesheet">
    <style>
    /* Force primary color to Bootstrap blue on this page to match Sekolah */
    :root {
        --bs-primary: #0d6efd;
    }
    .panorama-page .text-primary { color: #0d6efd !important; }
    .panorama-page .btn-outline-primary { color: #0d6efd !important; border-color: #0d6efd !important; }
    .panorama-page .btn-outline-primary:hover { background-color: #0d6efd !important; color: #fff !important; }
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
    <style>
    /* Fix: override overly-broad panorama white-text rule for CCTV cards
       so that namaTitik (card subtitle) remains readable on white cards. */
    .panorama-page .cctv-card,
    .panorama-page .cctv-card .card,
    .panorama-page .cctv-card .card-header,
    .panorama-page .cctv-card .card-title-text,
    .panorama-page .cctv-card .card-title,
    .panorama-page .cctv-card .card-subtitle {
        color: #2c3e50 !important; /* dark slate for readability */
    }
    /* ensure small subtitle uses normal weight and visible size */
    .panorama-page .cctv-card .card-subtitle { font-weight: 500; opacity: 0.95; }
    </style>
    <style>
    /* Stronger override to beat existing panorama white-text rules (high specificity + !important) */
    html body.panorama-page .main-content .cctv-grid .cctv-card .card .card-body,
    html body.panorama-page .main-content .cctv-grid .cctv-card .card .card-body p,
    html body.panorama-page .main-content .cctv-grid .cctv-card .card .card-header .card-title-text,
    html body.panorama-page .main-content .cctv-grid .cctv-card .card .card-header .card-title-text p {
        color: #2c3e50 !important;
    }

    /* target the common render path that uses center-aligned subtitle */
    html body.panorama-page .main-content .cctv-grid .cctv-card .card .card-body .text-center {
        color: #2c3e50 !important;
    }
    </style>
</head>

<body class="panorama-page">
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <button class="btn btn-outline-primary me-3" type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand">
                <div>
                    <i class="fas fa-video text-primary me-2"></i>
                    CCTV PANORAMA
                </div>
                <div style="font-size: 0.7rem; color: #6c757d; font-weight: normal;">
                    Memantau Kondisi Panorama DIY
                </div>
            </div>
            <div class="ms-auto navbar-status">
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
                <i class="fas fa-video me-2"></i>Panorama Control
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
                            <!-- gunakan id khusus untuk Panorama agar tidak bertabrakan dengan halaman Lokasi -->
                                <input type="text" class="form-control form-control-sm" id="sidebarSearchInputPanorama" placeholder="Cari..." style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; font-size: 0.8rem; padding: 8px 15px 8px 35px;">
                                <!-- khusus Panorama: gunakan id berbeda agar tidak konflik dengan halaman Lokasi -->
                        </div>
                    </div>
                    <div class="col-4">
                        <button id="sidebarHideAllBtn" class="btn btn-sm hide-all-btn w-100" style="font-size: 0.7rem; padding: 8px 5px; border-radius: 50px;" disabled>
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
                        <div class="sidebar-link d-flex justify-content-between align-items-center" onclick="toggleDropdown('cctvDropdown')">
                            <span><i class="fas fa-camera-retro me-2"></i>Panorama Monitoring</span>
                            <i class="fas fa-chevron-right" id="cctvChevron"></i>
                        </div>
                        <div class="dropdown-content" id="cctvDropdown">
                            @foreach ($groupedCctvs as $wilayah => $panoramas)
                                @php $wilayahSlug = Str::slug($wilayah); @endphp
                                <div class="dropdown-item d-flex justify-content-between align-items-center" onclick="toggleSubDropdown('{{ $wilayahSlug }}Dropdown')">
                                    <div><i class="fas fa-map-marker-alt me-2"></i>{{ $namaWilayahLengkap[$wilayah] ?? $wilayah }}</div>
                                    <div>
                                        <i id="eye-{{ $wilayahSlug }}" class="fas fa-eye-slash me-2"
                                            onclick="event.stopPropagation(); toggleAllSchoolCCTV('{{ $wilayah }}')"
                                            title="Tampilkan/sembunyikan CCTV {{ $wilayah }}"
                                            style="cursor: pointer; opacity: 0.7;"></i>
                                        <i class="fas fa-chevron-right float-end" id="{{ $wilayahSlug }}Chevron"></i>
                                    </div>
                                </div>
                                <div class="dropdown-content sub-dropdown ps-3" id="{{ $wilayahSlug }}Dropdown">
                                    @foreach ($panoramas as $panorama)
                                        @php $cardId = Str::slug($panorama->namaWilayah . '-' . $panorama->namaTitik); @endphp
                                        <div class="dropdown-item d-flex justify-content-between align-items-center" style="padding: 8px 12px;">
                                            <div class="d-flex align-items-center flex-grow-1" onclick="selectCCTVById('{{ $cardId }}')" style="cursor: pointer;">
                                                <i class="fas fa-video me-2"></i>
                                                <span>{{ $panorama->namaTitik }}</span>
                                            </div>
                                            <div class="form-check form-switch me-0" style="min-width: 40px;">
                                                <input class="form-check-input cctv-point-checkbox"
                                                    type="checkbox"
                                                    id="checkbox-{{ $cardId }}"
                                                    data-card-id="{{ $cardId }}"
                                                    onchange="toggleCCTVFromSidebar(this)"
                                                    style="margin: 0; cursor: pointer;"
                                                    onclick="event.stopPropagation();">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </li>
                    </ul>
                </div>
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
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    </ul>
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
    </div>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0" style="font-size: 24pt;">{{ $jumlahPanorama }}</h3>
                                    <p class="mb-0 small">Total Panorama</p>
                                </div>
                                <i class="fas fa-video fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #27ae60, #229954);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1">{{ $jumlahWilayah }}</h3>
                                    <p class="mb-0 small">Total Wilayah</p>
                                </div>
                                <i class="fas fa-map-marker-alt fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1" id="activeCCTVCountCard">0</h3>
                                    <p class="mb-0 small">Panorama Aktif</p>
                                </div>
                                <i class="fas fa-eye fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="control-panel">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-video text-primary me-2"></i>Live Panorama Monitoring</h5>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('lokasi.index') }}" class="btn btn-sm btn-outline-primary me-2" title="Buka CCTV Lokasi" aria-label="Buka CCTV Lokasi">
                            <i class="fas fa-video me-1"></i> CCTV Lokasi
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="cctv-grid" id="cctvGrid">
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="{{ asset('js/dashboard_public.js') }}"></script>

    <script>
        // --- Data dari Controller/PHP ---
        window.cctvIndex = @json($panoramaIndex ?? []);
        window.namaWilayahLengkap = @json($namaWilayahLengkap ?? []);

        // Ubah index menjadi objek untuk pencarian cepat berdasarkan cardId
        window.cctvById = {};
        Object.values(window.cctvIndex).forEach(list => (list || []).forEach(it => window.cctvById[it.cardId] = it));

        // --- Fungsi Helper ---
        function shortNameOf(name) {
            const words = (name || '').trim().split(/\s+/);
            return words.length > 3 ? words.map(w => (w[0] || '').toUpperCase()).join('') : (name || '');
        }
        
        // --- Fungsi Rendering Utama ---
        function createCardHTML(item) {
            const shortTitik = shortNameOf(item.titik);
            const fullWilayah = window.namaWilayahLengkap[item.wilayah] || item.wilayah;
            const lokasiSlug = slugify(item.wilayah);

            return `
                <div class="col-lg-3 col-md-4 col-sm-6 cctv-card" id="${item.cardId}" data-lokasi="${lokasiSlug}" data-wilayah="${item.wilayah}" data-titik="${item.titik}" style="display: none;">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title-text">
                                <p class="card-title mb-0 fw-bold">${fullWilayah}</p>
                                <p class="card-subtitle mb-0">${shortTitik || (item.titik || '(tidak ada nama titik)')}</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-switch" type="checkbox" onchange="toggleCCTV(this, '${item.cardId}')">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="iframe-container">
                                <iframe data-src="${item.link}" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            updateHideAllButtonState();
            updateActiveCCTVCount();
        });
        
        // Konfirmasi Logout
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
</body>