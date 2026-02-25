// public/js/lokasi.js
let panoramaData = [];
let filteredData = [];
let currentPage = 1;
let itemsPerPage = 10;

// 1. Ambil data dari API
function loadpanoramaData() {
    fetch("/api/cctvpanorama", { headers: { Accept: "application/json" } })
        .then((res) => res.json())
        .then((json) => {
            if (!json.success) {
                return Swal.fire("Error", json.message, "error");
            }
            panoramaData = json.data;
            filteredData = panoramaData; // awalnya filter = semua data
            currentPage = 1;
            renderTable();
        })
        .catch((err) => {
            console.error(err);
            Swal.fire("Error", "Gagal memuat data.", "error");
        });
}

function grouppanoramaData(data) {
    const grouped = {};

    data.forEach((item) => {
        const key = `${item.namaWilayah}`;
        if (!grouped[key]) {
            grouped[key] = {
                namaWilayah: item.namaWilayah,
                titik: [],
            };
        }
        grouped[key].titik.push(item);
    });

    // Sorting titik di tiap grup berdasarkan namaTitik
    Object.values(grouped).forEach((group) => {
        group.titik.sort((a, b) => {
            return a.namaTitik
                .toLowerCase()
                .localeCompare(b.namaTitik.toLowerCase());
        });
    });

    // Ubah grouped jadi array lalu sorting berdasarkan namaWilayah
    const result = Object.values(grouped);
    result.sort((a, b) => {
        return a.namaWilayah
            .toLowerCase()
            .localeCompare(b.namaWilayah.toLowerCase());
    });

    return result;
}

// 2. Render tabel berdasarkan filteredData & paging
function renderTable() {
    const tbody = document.getElementById("sekolah-tbody");
    tbody.innerHTML = "";

    const groupedData = grouppanoramaData(filteredData);

    // Flatten titik array, simpan info wilayah di tiap titik
    const titikList = [];
    groupedData.forEach((group) => {
        group.titik.forEach((item, idx) => {
            titikList.push({
                ...item,
                wilayahNama:
                    group.namaWilayah === "KABUPATEN GK"
                        ? "KAB GUNUNG KIDUL"
                        : group.namaWilayah === "KABUPATEN KP"
                        ? "KAB KULONPROGO"
                        : group.namaWilayah === "KABUPATEN BANTUL"
                        ? "KAB BANTUL"
                        : group.namaWilayah === "KABUPATEN SLEMAN"
                        ? "KAB SLEMAN"
                        : group.namaWilayah,
                isFirstWilayah: idx === 0,
                wilayahRowspan: group.titik.length,
            });
        });
    });

    // Paging berdasarkan titik
    const start = (currentPage - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, titikList.length);
    const totalTitik = titikList.length;
    const pageData = titikList.slice(start, end);

    let lastWilayah = null;
    let wilayahRowspanMap = {};

    // Hitung berapa kali wilayah muncul di halaman ini
    pageData.forEach((item) => {
        wilayahRowspanMap[item.wilayahNama] = (wilayahRowspanMap[item.wilayahNama] || 0) + 1;
    });

    let wilayahPrinted = {};
    pageData.forEach((item, idx) => {
        const tr = document.createElement("tr");
        let wilayahCell = "";
        if (!wilayahPrinted[item.wilayahNama]) {
            wilayahCell = `<td class="text-center" rowspan="${wilayahRowspanMap[item.wilayahNama]}">${item.wilayahNama}</td>`;
            wilayahPrinted[item.wilayahNama] = true;
        }
        tr.innerHTML = `
            ${wilayahCell}
            <td class="text-center">${item.namaTitik}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-secondary" onclick='openEditModal(${JSON.stringify(
                    item
                )})'>Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteLokasi(${
                    item.id
                })">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Update infoText
    const infoTextEl = document.getElementById("infoText");
    if (infoTextEl) {
        if (totalTitik === 0) {
            infoTextEl.textContent = "Menampilkan 0 titik CCTV";
        } else {
            infoTextEl.textContent = `Menampilkan ${start + 1}–${end} dari ${totalTitik} titik CCTV`;
        }
    }

    renderPagination(titikList.length);
}

// 3. Render tombol pagination
function renderPagination(totalItems = null) {
    const totalPages = Math.ceil(
        (totalItems ?? filteredData.length) / itemsPerPage
    );
    document.querySelector('button[onclick="prevPage()"]').disabled =
        currentPage === 1;
    document.querySelector('button[onclick="nextPage()"]').disabled =
        currentPage === totalPages;
}

// 4. Prev / Next
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
}
function nextPage() {
    // Paging berdasarkan jumlah titik
    const groupedData = grouppanoramaData(filteredData);
    const titikList = [];
    groupedData.forEach((group) => {
        group.titik.forEach((item) => {
            titikList.push(item);
        });
    });
    const totalPages = Math.ceil(titikList.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
}

// 5. Search lokal
function searchCctvPanorama() {
    const q = document.getElementById("searchInput").value.trim().toLowerCase();
    filteredData = panoramaData.filter(
        (item) =>
            item.namaWilayah.toLowerCase().includes(q) ||
            item.namaTitik.toLowerCase().includes(q)
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
            fetch(`/api/cctvpanorama/${id}`, {
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
                        loadpanoramaData();
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

// 7. Open modal edit/ add (contoh)
function openAddModal() {
    $("#panoramaForm")[0].reset();
    $("#panoramaForm").attr("action", "/api/cctvpanorama");
    $("#panoramaForm").attr("method", "POST");
    $("#panoramaModalLabel").text("Tambah CCTV Panorama");
    $("#saveBtn").text("Save");
    new bootstrap.Modal($("#panoramaModal")).show();
}
function openEditModal(item) {
    $("#idLokasi").val(item.id);
    $("#namaWilayah").val(item.namaWilayah);
    $("#namaTitik").val(item.namaTitik);
    $("#link").val(item.link);

    $("#panoramaForm").attr("action", `/api/cctvpanorama/${item.id}`);
    $("#panoramaForm").attr("method", "PUT");
    $("#panoramaModalLabel").text("Edit CCTV Panorama");
    $("#saveBtn").text("Update");
    new bootstrap.Modal($("#panoramaModal")).show();
}

// 8. Inisialisasi ketika dokumen siap
document.addEventListener("DOMContentLoaded", loadpanoramaData);

// Add dan Edit
document
    .getElementById("panoramaForm")
    .addEventListener("submit", function (e) {
        e.preventDefault();

        const id = document.getElementById("idLokasi").value;
        const method = id ? "PUT" : "POST";
        const url = id ? `/api/cctvpanorama/${id}` : "/api/cctvpanorama";

        const data = {
            namaWilayah: document.getElementById("namaWilayah").value,
            namaTitik: document.getElementById("namaTitik").value,
            link: document.getElementById("link").value,
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
                    Swal.fire("Berhasil", res.message, "success");
                    document.getElementById("panoramaForm").reset();
                    document.getElementById("idLokasi").value = "";

                    const modalElement =
                        document.getElementById("panoramaModal");
                    const modalInstance =
                        bootstrap.Modal.getInstance(modalElement) ||
                        new bootstrap.Modal(modalElement);

                    // Fix overlay modal backdrop
                    document
                        .querySelectorAll(".modal-backdrop")
                        .forEach((el) => el.remove());
                    document.body.classList.remove("modal-open");
                    document.body.style.overflow = "";
                    document.body.style.paddingRight = "";
                    modalInstance.hide();

                    loadpanoramaData();
                } else {
                    let errorText = "";
                    if (res.data && typeof res.data === "object") {
                        for (const key in res.data) {
                            errorText += `${key}: ${res.data[key].join(
                                ", "
                            )}\n`;
                        }
                    } else {
                        errorText = res.message;
                    }
                    Swal.fire("Gagal", errorText, "error");
                }
            })
            .catch((err) => {
                console.error(err);
                Swal.fire(
                    "Error",
                    "Terjadi kesalahan saat menyimpan data.",
                    "error"
                );
            });
    });
document.addEventListener("DOMContentLoaded", () => {
    loadpanoramaData(); // ✅ ambil data CCTV panorama

    const rowsPerPageSelect = document.getElementById("rowsPerPage");
    if (rowsPerPageSelect) {
        rowsPerPageSelect.addEventListener("change", function () {
            if (this.value === "all") {
                // Hitung total titik
                const groupedData = grouppanoramaData(filteredData.length ? filteredData : panoramaData);
                let totalTitik = 0;
                groupedData.forEach(group => totalTitik += group.titik.length);
                itemsPerPage = totalTitik;
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


function scrollToTable() {
    const tableEl = document.getElementById("cctvTable"); 
    if (tableEl) {
        setTimeout(() => {
            tableEl.scrollIntoView({ behavior: "smooth", block: "start" });
        }, 50);
    }
}
