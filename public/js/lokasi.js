// public/js/lokasi.js

let lokasiData = [];
let filteredData = [];
let currentPage = 1;
let itemsPerPage = 10;
// Pastikan csrfToken didefinisikan secara global, contoh:
// <script> const csrfToken = "{{ csrf_token() }}"; </script>
// Ini penting untuk autentikasi API.


// 1. Ambil data dari API
function loadLokasiData() {
    // Tampilkan loading state di tabel
    const tbody = document.getElementById("lokasi-tbody");
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Memuat data...
                </td>
            </tr>`;
    }

    fetch("/api/cctvlokasi", { headers: { Accept: "application/json" } })
        .then((res) => res.json())
        .then((json) => {
            if (!json.success) {
                return Swal.fire("Error", json.message, "error");
            }
            lokasiData = json.data;
            filteredData = lokasiData;
            currentPage = 1;
            renderTable();

            // Perbarui status toggle all berdasarkan data yang ada
            const allActive = lokasiData.length > 0 && lokasiData.every(item => item.is_active);
            const toggleCheckbox = document.getElementById("toggleAll");
            const label = document.getElementById("toggleAllLabel");

            if (toggleCheckbox && label) {
                toggleCheckbox.checked = allActive;
                label.textContent = allActive ? "Nonaktifkan Semua" : "Aktifkan Semua";
            }
        })
        .catch((err) => {
            console.error(err);
            Swal.fire("Error", "Gagal memuat data.", "error");
        });
}

function groupLokasiData(data) {
    const grouped = {};

    data.forEach((item) => {
        const key = `${item.nama_wilayah}||${item.nama_lokasi}`;
        if (!grouped[key]) {
            grouped[key] = {
                nama_wilayah: item.nama_wilayah,
                nama_lokasi: item.nama_lokasi,
                titik: [],
            };
        }
        grouped[key].titik.push(item);
    });

    Object.values(grouped).forEach((group) => {
        group.titik.sort((a, b) => {
            const nameA = (a.nama_cctv || "").toLowerCase();
            const nameB = (b.nama_cctv || "").toLowerCase();
            return nameA.localeCompare(nameB);
        });
    });

    const result = Object.values(grouped);

    result.sort((a, b) => {
        const wilayahCompare = (a.nama_wilayah || "").toLowerCase().localeCompare((b.nama_wilayah || "").toLowerCase());
        if (wilayahCompare !== 0) return wilayahCompare;
        return (a.nama_lokasi || "").toLowerCase().localeCompare((b.nama_lokasi || "").toLowerCase());
    });

    return result;
}

// 2. Render tabel berdasarkan filteredData & paging
function renderTable() {
    const tbody = document.getElementById("lokasi-tbody");
    tbody.innerHTML = "";

    const groupedData = groupLokasiData(filteredData);
    let totalTitik = filteredData.length; // Perbaikan: Gunakan filteredData.length

    const titikStartIndex = (currentPage - 1) * itemsPerPage;
    const pageData = filteredData.slice(titikStartIndex, titikStartIndex + itemsPerPage);
    
    // Perbaikan: Gunakan data yang sudah di-slice untuk rendering
    const groupedPageData = groupLokasiData(pageData);

    let start = totalTitik === 0 ? 0 : titikStartIndex + 1;
    let end = Math.min(titikStartIndex + itemsPerPage, totalTitik);

    document.getElementById("infoText").textContent = `Menampilkan ${start}–${end} dari ${totalTitik} data`;

    groupedPageData.forEach((group) => {
        const rowspan = group.titik.length;

        group.titik.forEach((item, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                ${index === 0 ? `<td class="text-center align-middle" rowspan="${rowspan}">${group.nama_wilayah}</td>` : ""}
                ${index === 0 ? `<td class="text-center align-middle" rowspan="${rowspan}">${group.nama_lokasi}</td>` : ""}
                <td class="text-center align-middle">${item.nama_cctv}</td>
                <td class="text-center align-middle">
                    <div class="d-flex justify-content-center gap-2">
                        <!-- Perbaikan: Mengirimkan ID, bukan objek, untuk keamanan dan menghindari error karakter -->
                        <button class="btn btn-sm btn-secondary" onclick='openEditModal(${item.id})'>Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLokasi(${item.id})">Delete</button>
                        <label class="switch">
                            <input type="checkbox" ${item.is_active ? 'checked' : ''} onchange="toggleActive(${item.id})">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    });

    renderPagination(totalTitik);
}

function toggleActive(id) {
    fetch(`/api/cctvlokasi/${id}/toggle`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
    })
        .then((res) => res.json())
        .then((res) => {
            if (res.success) {
                Swal.fire("Berhasil", res.message, "success");
                loadLokasiData();
            } else {
                Swal.fire("Gagal", res.message, "error");
            }
        })
        .catch((err) => {
            console.error(err);
            Swal.fire("Error", "Gagal mengubah status.", "error");
        });
}

function toggleAllActive(checkbox) {
    const newState = checkbox.checked;
    const label = document.getElementById("toggleAllLabel");
    label.textContent = newState ? "Nonaktifkan Semua" : "Aktifkan Semua";

    const idsToToggle = filteredData.map(item => item.id);

    fetch(`/api/cctvlokasi/bulk-toggle`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ ids: idsToToggle, active: newState }),
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire("Berhasil", res.message, "success");
                loadLokasiData();
            } else {
                Swal.fire("Gagal", res.message || "Gagal mengubah semua status", "error");
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire("Error", "Terjadi kesalahan saat mengubah status semua", "error");
        });
}

// 3. Render tombol pagination
function renderPagination(totalTitik) {
    const totalPages = Math.ceil(totalTitik / itemsPerPage);
    document.querySelector('button[onclick="prevPage()"]').disabled = currentPage === 1 || this.value === "all";
    document.querySelector('button[onclick="nextPage()"]').disabled = currentPage >= totalPages || this.value === "all";
}

// 4. Prev / Next
function scrollToTable() {
    const tableEl = document.getElementById("cctvTable"); // id tabel CCTV
    if (tableEl) {
        setTimeout(() => {
            tableEl.scrollIntoView({ behavior: "smooth", block: "start" });
        }, 50);
    }
}


function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
        scrollToTable();
    }
}

function nextPage() {
    const totalPages = Math.ceil((filteredData.length || lokasiData.length) / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
        scrollToTable();
    }
}


// 5. Search lokal
function searchcctvlokasi() {
    const q = document.getElementById("searchInput").value.trim().toLowerCase();
    filteredData = lokasiData.filter(
        (item) =>
            (item.nama_wilayah && item.nama_wilayah.toLowerCase().includes(q)) ||
            (item.nama_lokasi && item.nama_lokasi.toLowerCase().includes(q)) ||
            (item.nama_cctv && item.nama_cctv.toLowerCase().includes(q))
    );
    currentPage = 1;
    renderTable();
}

// Hapus Data
function deleteLokasi(id) {
    Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/cctvlokasi/${id}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
            })
                .then((r) => r.json())
                .then((res) => {
                    if (res.success) {
                        Swal.fire("Dihapus", res.message, "success");
                        loadLokasiData();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire("Error", "Gagal menghapus.", "error");
                });
        }
    });
}

// 7. Open modal edit/ add
function openAddModal() {
    document.getElementById("cctvForm").reset();
    document.getElementById("idLokasi").value = "";
    document.getElementById("cctvForm").setAttribute("action", "/api/cctvlokasi");
    document.getElementById("cctvForm").setAttribute("method", "POST");
    document.getElementById("cctvlokasiModalLabel").textContent = "Tambah CCTV Lokasi";
    document.getElementById("saveBtn").textContent = "Save";

    const modalElement = document.getElementById('cctvlokasiModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
}


// Perbaikan: Mengambil item dari data yang sudah ada berdasarkan ID
function openEditModal(id) {
    const item = lokasiData.find(d => d.id === id);
    if (!item) {
        Swal.fire("Error", "Data tidak ditemukan.", "error");
        return;
    }
    document.getElementById("idLokasi").value = item.id;
    document.getElementById("wilayah_id").value = item.wilayah_id;
    // Perbaikan: Gunakan ID yang sesuai, misal "nama_lokasi"
    document.getElementById("nama_lokasi").value = item.nama_lokasi;
    document.getElementById("nama_cctv").value = item.nama_cctv;
    document.getElementById("link_stream").value = item.link; // Perbaikan: Gunakan ID "link_stream"

    document.getElementById("cctvForm").setAttribute("action", `/api/cctvlokasi/${item.id}`);
    document.getElementById("cctvForm").setAttribute("method", "PUT");
    document.getElementById("cctvlokasiModalLabel").textContent = "Edit CCTV Lokasi";
    document.getElementById("saveBtn").textContent = "Update";

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById("cctvlokasiModal"));
    modal.show();
}


// 8. Inisialisasi ketika dokumen siap
document.addEventListener("DOMContentLoaded", () => {
    loadLokasiData();

    const rowsPerPageSelect = document.getElementById("rowsPerPage");
    if (rowsPerPageSelect) {
        rowsPerPageSelect.addEventListener("change", function () {
            if (this.value === "all") {
                itemsPerPage = filteredData.length || lokasiData.length;
                showAllMode = true;
            } else {
                itemsPerPage = parseInt(this.value);
                showAllMode = false;
            }
            currentPage = 1;
            renderTable();
            scrollToTable();
        });
    }
});


// Add dan Edit
document.getElementById("cctvForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("idLokasi").value;
    const method = id ? "PUT" : "POST";
    const url = id ? `/api/cctvlokasi/${id}` : "/api/cctvlokasi";

    // Perbaikan: Mengambil nilai dari elemen form dengan ID yang benar
    const data = {
        wilayah_id: document.getElementById("wilayah_id").value,
        nama_lokasi: document.getElementById("nama_lokasi").value,
        nama_cctv: document.getElementById("nama_cctv").value,
        link_stream: document.getElementById("link_stream").value,
    };

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(data),
    })
        .then((res) => res.json())
        .then((res) => {
            if (res.success) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: res.message
                }).then(() => {
                    // reset form
                    const form = document.getElementById("cctvForm");
                    form.reset();
                    document.getElementById("idLokasi").value = "";

                    // ambil instance modal
                    const modalElement = document.getElementById("cctvlokasiModal");
                    let modalInstance = bootstrap.Modal.getInstance(modalElement);
                    // Jika instance tidak ditemukan, buat baru
                    if (!modalInstance) {
                        modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                    }
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Pastikan overlay modal dihapus
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                    // reload data
                    loadLokasiData();
                });
            } else {
                let errorText = "";
                if (res.data && typeof res.data === "object") {
                    for (const key in res.data) {
                        errorText += `${key}: ${res.data[key].join(", ")}\n`;
                    }
                } else {
                    errorText = res.message;
                }
                Swal.fire("Gagal", errorText, "error");
            }
        })
        .catch((err) => {
            console.error(err);
            Swal.fire("Error", "Terjadi kesalahan saat menyimpan data.", "error");
        });
});


function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.querySelector(".overlay");

    if (!sidebar || !overlay) return;

    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");

    if (sidebar.classList.contains("active")) {
        document.body.style.overflow = "hidden";
    } else {
        document.body.style.overflow = "auto";
    }
}
