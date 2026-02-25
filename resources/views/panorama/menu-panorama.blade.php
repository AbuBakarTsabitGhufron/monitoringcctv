@extends('layouts.user_type.auth')

@section('content')
    <main class="main-content">
        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        {{-- Header --}}
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>CCTV Panorama</h6>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('panorama.export') }}" class="btn btn-success btn-sm w-100 w-sm-auto">Export</a>
                                <input type="text" id="searchInput" class="form-control form-control-sm me-2"
                                    placeholder="Search..." onkeyup="searchCctvPanorama()"
                                    style="height: 2.1rem; padding: 5px;">
                                <a href="javascript:;" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#cctvlokasiModal" onclick="openAddModal()">Add</a>
                            </div>
                            
                        </div>

                        <!-- Table & Pagination -->
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                {{-- Tables --}}
                                @include('panorama.partials.table')
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
            @include('panorama.partials.modals')

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
        const token = Cookies.get('token') || ''; // kalau pakai auth header
    </script>
    <!-- Custom Script -->
    <script src="{{ asset('js/panorama.js') }}"></script>
@endpush