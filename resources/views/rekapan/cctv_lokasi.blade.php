@extends('layouts.user_type.auth')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-uppercase mb-0">Rekapan Jumlah CCTV Lokasi</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body table-responsive" id="tableContainer">
            <div class="d-flex justify-content-start gap-2 mb-3 flex-wrap">
                <a href="{{ route('lokasi.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export
                </a>
                <button onclick="printTable()" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>

            <div class="d-flex gap-3 mb-3 flex-wrap align-items-center">
                <select id="filterWilayah" class="form-select w-25">
                    <option value="">Semua Wilayah</option>
                    @foreach ($jumlahCCTVPerLokasi->pluck('namaWilayah')->unique() as $wilayah)
                        <option value="{{ strtolower($wilayah) }}">{{ $wilayah }}</option>
                    @endforeach
                </select>

                <select id="jenisLokasiFilter" class="form-select w-25">
                    <option value="">Semua Lokasi</option>
                    <option value="sma">SMA</option>
                    <option value="smk">SMK</option>
                </select>

                <input type="text" id="searchInput" class="form-control w-25" placeholder="Search....">
            </div>

            <style>
                /* ...existing code... */
                #rekapTable tbody tr {
                    cursor: pointer;
                    transition: background 0.15s;
                }
                #rekapTable tbody tr:hover {
                    background: #f1f1f1;
                }
                /* Tambahkan agar link di modal tidak overflow */
                #detailTable td a {
                    display: inline-block;
                    max-width: 250px; /* atau sesuaikan */
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    vertical-align: middle;
                }

            </style>

            <table class="table table-hover align-middle text-center" id="rekapTable">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th class="text-start">Nama Lokasi</th>
                        <th class="text-start">Kabupaten / Kota</th>
                        <th>Jumlah CCTV</th>
                        <!-- Hapus kolom Detail -->
                    </tr>
                </thead>
                <tbody id="rekapBody">
                    @forelse ($jumlahCCTVPerLokasi as $index => $lokasi)
                        <tr data-lokasi-id="{{ $lokasi->lokasiId }}">
                            <td class="nomor-urut"></td>
                            <td class="text-start">{{ $lokasi->namaLokasi }}</td>
                            <td class="text-start">{{ $lokasi->namaWilayah }}</td>
                            <td><strong>{{ $lokasi->total_cctv }}</strong></td>
                            <!-- Hapus tombol detail -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Data tidak tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                <div>
                    <button id="prevBtn" class="btn btn-sm btn-primary" onclick="prevPage()">Prev</button>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="rowsPerPage" class="mb-0">Items per page:</label>
                    <select id="rowsPerPage" class="form-select form-select-sm px-3" style="width: auto; min-width: 70px;">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">Semua Data</option>
                    </select>
                    <small id="infoText" class="text-muted ms-2">Menampilkan ...</small>
                </div>
                <div>
                    <button id="nextBtn" class="btn btn-sm btn-primary" onclick="nextPage()">Next</button>
                </div>
            </div>
            <!-- Modal/tabel detail -->
            <div id="detailModal" class="modal" tabindex="-1" style="display:none; background:rgba(0,0,0,0.3); position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:9999;">
                <div style="background:#fff; margin:5% auto; padding:20px; border-radius:8px; max-width:500px; position:relative;">
                    <button typFe="button" id="closeDetailModal" class="btn btn-danger btn-sm" style="position:absolute; top:10px; right:10px;">&times;</button>
                    <h5 id="detailModalTitle" class="mb-3"></h5>
                    <div style="max-height: 400px; overflow-y: auto;"> <!-- scroll area -->
                    <table class="table table-bordered" id="detailTable">
                        <thead>
                            <tr>
                                <th>Nama Titik</th>
                                <th>Link CCTV</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const allRows = Array.from(document.querySelectorAll('#rekapBody tr'));
    let currentPage = 1;
    let rowsPerPage = 10;
    let filteredRows = [...allRows];

    function updateFilteredRows() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const jenisValue = document.getElementById('jenisLokasiFilter').value.toLowerCase();
        const wilayahValue = document.getElementById('filterWilayah').value;

        filteredRows = allRows.filter(row => {
            const namaLokasi = row.children[1].innerText.toLowerCase();
            const namaWilayah = row.children[2].innerText.toLowerCase();

            const matchSearch = namaLokasi.includes(searchValue) || namaWilayah.includes(searchValue);
            const matchJenis = jenisValue === '' ||
                (jenisValue === 'sma' && namaLokasi.startsWith('sma')) ||
                (jenisValue === 'smk' && namaLokasi.startsWith('smk'));
            const matchWilayah = wilayahValue === '' || namaWilayah === wilayahValue;

            return matchSearch && matchJenis && matchWilayah;
        });

        currentPage = 1;
        displayRows();
    }

    function displayRows() {
        const rowsPerPageSelect = document.getElementById('rowsPerPage');
        const rowsPerPageValue = rowsPerPageSelect.value;
        const total = filteredRows.length;

        // Reset display for all rows
        allRows.forEach(row => row.style.display = 'none');

        let start = 0;
        let end = total;

        if (rowsPerPageValue !== 'all') {
            rowsPerPage = parseInt(rowsPerPageValue);
            start = (currentPage - 1) * rowsPerPage;
            end = start + rowsPerPage;
        } else {
            rowsPerPage = total;
        }

        const visibleRows = filteredRows.slice(start, end);
        visibleRows.forEach((row, index) => {
            row.style.display = '';
            const noCell = row.querySelector('.nomor-urut');
            if (noCell) noCell.textContent = start + index + 1;
        });

        const infoText = document.getElementById('infoText');
        const showingStart = total === 0 ? 0 : start + 1;
        const showingEnd = Math.min(end, total);
        infoText.textContent = `${showingStart}–${showingEnd} dari ${total} entri`;

        document.getElementById('prevBtn').disabled = currentPage === 1 || rowsPerPageValue === 'all';
        document.getElementById('nextBtn').disabled = end >= total || rowsPerPageValue === 'all';
    }

    function nextPage() {
        if ((currentPage * rowsPerPage) < filteredRows.length) {
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

    document.getElementById('searchInput').addEventListener('input', updateFilteredRows);
    document.getElementById('jenisLokasiFilter').addEventListener('change', updateFilteredRows);
    document.getElementById('filterWilayah').addEventListener('change', updateFilteredRows);
    document.getElementById('rowsPerPage').addEventListener('change', () => {
        currentPage = 1;
        displayRows();
        
        // Menggunakan `scrollIntoView` pada kontainer tabel untuk menggulir seluruh jendela
        // Ini lebih efektif untuk memastikan tampilan disesuaikan di berbagai peramban
        const tableContainer = document.getElementById('tableContainer');
        if (tableContainer) {
            tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    // Initialize the page with all rows displayed
    displayRows();

    function printTable() {
        const printWindow = window.open('', '', 'width=1000,height=700');
        const logoUrl = "{{ asset('images/lifemedia_logo_background.png') }}";

        const style = `
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 40px;
                    position: relative;
                }

                .watermark-logo {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    opacity: 0.08;
                    width: 300px;
                    z-index: 0;
                    pointer-events: none;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 40px;
                    z-index: 1;
                    position: relative;
                }

                th, td {
                    border: 1px solid #999;
                    padding: 8px;
                    text-align: left;
                    background-color: #fff;
                }

                th {
                    background-color: #d0c8c8;
                }

                h2 {
                    text-align: center;
                    margin-bottom: 20px;
                    position: relative;
                    z-index: 1;
                }
            </style>
        `;

        // Get all filtered rows to print
        let tableContent = `
            <img src="${logoUrl}" class="watermark-logo" />
            <h2>Rekapan Jumlah CCTV Lokasi</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lokasi</th>
                        <th>Kabupaten / Kota</th>
                        <th>Jumlah CCTV</th>
                    </tr>
                </thead>
                <tbody>
        `;

        filteredRows.forEach((row, index) => {
            const cells = row.querySelectorAll('td');
            tableContent += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${cells[1].innerText}</td>
                    <td>${cells[2].innerText}</td>
                    <td>${cells[3].innerText}</td>
                </tr>
            `;
        });

        tableContent += `
                </tbody>
            </table>
        `;

        printWindow.document.write(`<html><head><title>Cetak</title>${style}</head><body>${tableContent}</body></html>`);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }

    // Data detail CCTV per lokasi (dari PHP ke JS)
    const detailCCTV = @json($detailCCTV);

    // Show detail modal saat baris diklik
    document.querySelectorAll('#rekapBody tr[data-lokasi-id]').forEach(row => {
        row.addEventListener('click', function() {
            const lokasiId = this.getAttribute('data-lokasi-id');
            const rows = detailCCTV[lokasiId] || [];
            const modal = document.getElementById('detailModal');
            const tbody = modal.querySelector('#detailTable tbody');
            const title = modal.querySelector('#detailModalTitle');
            const lokasiName = this.children[1].innerText;
            title.textContent = 'Detail CCTV: ' + lokasiName;
            tbody.innerHTML = '';
            if (rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="2">Tidak ada data titik CCTV.</td></tr>';
            } else {
                rows.forEach(row => {
                    tbody.innerHTML += `<tr>
                        <td>${row.namaTitik}</td>
                        <td style="word-break:break-all;max-width:250px;"><a href="${row.linkStream}" target="_blank">${row.linkStream}</a></td>
                    </tr>`;
                });
            }
            modal.style.display = 'block';
        });
    });

    // Close modal
    document.getElementById('closeDetailModal').onclick = function() {
        document.getElementById('detailModal').style.display = 'none';
    };

    // Tutup modal jika klik di luar konten
    document.getElementById('detailModal').onclick = function(e) {
        if (e.target === this) this.style.display = 'none';
    };
</script>
@endsection
