@extends('layouts.user_type.auth')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-uppercase mb-0">Daftar Lokasi</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali</a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <input type="text" id="searchInput" class="form-control mb-3 w-25" placeholder="Cari nama lokasi atau kota...">

            <table class="table table-hover align-middle text-center" id="lokasiTable">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lokasi</th>
                        <th>Kabupaten / Kota</th>
                    </tr>
                </thead>
                <tbody id="lokasiBody">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lokasi</th>
                                <th>Wilayah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lokasi as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->nama_lokasi }}</td>
                                    <td>{{ $item->wilayah->nama_wilayah ?? 'Wilayah tidak ditemukan' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data lokasi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-sm btn-primary" onclick="prevPage()">Prev</button>
                <button class="btn btn-sm btn-primary" onclick="nextPage()">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
    const rows = Array.from(document.querySelectorAll('#lokasiBody tr'));
    const rowsPerPage = 10;
    let currentPage = 1;

    function displayRows() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? '' : 'none';
        });
    }

    function nextPage() {
        if ((currentPage * rowsPerPage) < rows.length) {
            currentPage++;
            displayRows();
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            displayRows();
        }
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        currentPage = 1;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Tampilkan halaman pertama saat load
    displayRows();
</script>
@endsection
