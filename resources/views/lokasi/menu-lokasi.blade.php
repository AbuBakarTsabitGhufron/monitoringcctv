@extends('layouts.user_type.auth')
@if(session('success'))
    <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger mx-3 mt-3">{{ session('error') }}</div>
@endif
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

@section('content')
    <main class="main-content">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card mb-4">
                        {{-- Header --}}
                        <div class="card-header pb-0">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h6 class="mb-2">CCTV Lokasi</h6>
                                    <input type="text" id="searchInput"
                                        class="form-control form-control-sm mt-1"
                                        placeholder="Search..."
                                        onkeyup="searchcctvlokasi()" />
                                </div>
                                <div class="col-md-8 d-flex flex-wrap justify-content-end align-items-center gap-2 mt-3 mt-md-0">
                                    <a href="{{ route('lokasi.template.download') }}" class="btn btn-outline-secondary btn-sm">
                                        Template
                                    </a>
                                    {{-- Import Excel (AJAX – no full-page redirect) --}}
                                    <input type="file" name="file" accept=".xlsx,.xls"
                                           id="uploadExcelInput" style="display:none;"
                                           onchange="doImportExcel(this)">
                                    <button type="button" class="btn btn-success btn-sm"
                                            onclick="document.getElementById('uploadExcelInput').click()">
                                        Import Excel
                                    </button>
                                    <a href="javascript:;" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#cctvlokasiModal"
                                        onclick="openAddModal()">Add</a>
                                    <div class="d-flex align-items-center ms-2">
                                        <label class="switch">
                                            <input type="checkbox" id="toggleAll" onchange="toggleAllActive(this)">
                                            <span class="slider round"></span>
                                        </label>
                                        <span id="toggleAllLabel" class="ms-2">Aktifkan Semua</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Table & Pagination -->
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive-sm">
                                {{-- Tables --}}
                                @include('lokasi.partials.table')
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-primary btn-sm ms-3 py-2 px-3" onclick="prevPage()">Prev</button>
                                <div class="d-flex align-items-center gap-2">
                                    <label for="rowsPerPage" class="mb-0">Items per page:</label>
                                    <select id="rowsPerPage" class="form-select form-select-sm px-3" style="width: auto; min-width: 70px;">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="all">All</option>
                                        
                                    </select>
                                    <small id="infoText" class="text-muted ms-2">Menampilkan ...</small>
                                </div>
                                <button class="btn btn-primary btn-sm me-3 py-2 px-3" onclick="nextPage()">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memanggil modal Add dan Edit dari partials -->
            @include('lokasi.partials.modals')

        </div>
    </main>
@endsection

@push('scriptsku')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 (opsional) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- js-cookie jika butuh token -->
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script>
        const csrfToken = "{{ csrf_token() }}";
        const token = Cookies.get('token') || '';

        // ── Import Excel via AJAX (no full-page redirect) ──────────────────
        function doImportExcel(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', csrfToken);

            // Reset input so the same file can be re-selected later
            input.value = '';

            Swal.fire({
                title: 'Mengimport data...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            fetch("{{ route('lokasi.import.manual') }}", {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: formData,
            })
            .then(res => res.json())
            .then(res => {
                const icon  = res.success ? 'success' : (res.imported === 0 ? 'warning' : 'error');
                const title = res.success ? 'Berhasil!' : (res.imported === 0 ? 'Perhatian' : 'Gagal');
                Swal.fire({ icon, title, text: res.message, timer: 3000, showConfirmButton: false });
                // Refresh table regardless of result (data may have partially imported)
                loadLokasiData();
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan saat mengimport.', 'error');
            });
        }
    </script>
    <!-- Custom Script -->
    <script src="{{ asset('js/lokasi.js') }}"></script>
@endpush