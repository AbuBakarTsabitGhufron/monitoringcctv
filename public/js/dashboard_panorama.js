//fungsi tampilkan semua cctv dalam wilayah yang dipilih
function toggleIcon(event, wilayah) {
    event.stopPropagation();
    event.preventDefault();

    const icon = event.target;
    const cctvToShow = document.querySelectorAll(
        `.cctv-view[data-wilayah="${wilayah}"]`
    );
    const checkboxes = document.querySelectorAll(
        `input[data-wilayah="${wilayah}"]`
    );

    let activeWilayah = JSON.parse(localStorage.getItem("activeWilayah")) || [];

    if (icon.classList.contains("fa-eye")) {
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

        cctvToShow.forEach((cctv) => {
            cctv.style.display = "block";
            const iframe = cctv.querySelector("iframe");
            if (iframe) {
                iframe.src = iframe.getAttribute("data-src");
            }
        });

        if (!activeWilayah.includes(wilayah)) {
            activeWilayah.push(wilayah);
        }

        checkboxes.forEach((checkbox) => {
            checkbox.checked = true;
            const cctvId = checkbox.id.replace("checkbox-", "");
            toggleCCTV(cctvId, checkbox);
        });
    } else {
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

        cctvToShow.forEach((cctv) => {
            cctv.style.display = "none";
        });

        activeWilayah = activeWilayah.filter((w) => w !== wilayah);

        checkboxes.forEach((checkbox) => {
            checkbox.checked = false;
            const cctvId = checkbox.id.replace("checkbox-", "");
            toggleCCTV(cctvId, checkbox);
        });
    }

    if (activeWilayah.length === 0) {
        localStorage.removeItem("activeWilayah");
    } else {
        localStorage.setItem("activeWilayah", JSON.stringify(activeWilayah));
    }
}

function filterSidebar() {
    const input = document.getElementById("searchSekolahSidebar"); // Sesuaikan dengan id input di HTML
    const filter = input.value.toLowerCase();

    // Ambil semua wilayah (.menu > .item)
    const wilayahItems = document.querySelectorAll(".menu > .item");

    wilayahItems.forEach((wilayahItem) => {
        // Ambil semua label titik di dalam sub-menu wilayah
        const labels = wilayahItem.querySelectorAll("label.form-check");
        let adaYangCocok = false;

        labels.forEach((label) => {
            const spanLabel = label.querySelector("span.form-check-label");
            if (!spanLabel) return; // safety check

            const namaTitik = spanLabel.textContent.toLowerCase();
            if (namaTitik.includes(filter)) {
                label.style.display = "";
                adaYangCocok = true;
            } else {
                label.style.display = "none";
            }
        });

        // Tampilkan wilayah jika ada titik yang cocok
        wilayahItem.style.display = adaYangCocok ? "" : "none";
    });
}

function toggleDaerah(id) {
    const subMenu = document.getElementById(id);
    const icon = document.getElementById("icon-" + id); // ID harus match

    const isVisible = subMenu.style.display === "block";

    subMenu.style.display = isVisible ? "none" : "block";
    icon.classList.toggle("fa-angle-right");
    icon.classList.toggle("fa-angle-down");
}

function toggleCCTV(id, checkbox) {
    const cctvContainer = document.getElementById(id);
    const iframe = cctvContainer.querySelector("iframe");

    if (checkbox.checked) {
        cctvContainer.style.display = "block";
        iframe.src = iframe.getAttribute("data-src");
    } else {
        cctvContainer.style.display = "none";
        iframe.src = "";
    }

    // Simpan status ke localStorage
    localStorage.setItem(checkbox.id, checkbox.checked);
}

window.onload = function () {
    const stored = localStorage.getItem("activeCCTVs");
    if (stored) {
        const cctvList = JSON.parse(stored);
        cctvList.forEach((id) => {
            const checkbox = document.querySelector(`#checkbox-${id}`);
            if (checkbox) {
                checkbox.checked = true;
                toggleCCTV(id, checkbox);
            }
        });
    }
};

function toggleSidebar() {
    const sidebar = document.querySelector(".side-bar");
    const pageCard = document.querySelector("#pagecard");
    const btnSidebar = document.querySelector(".btn-sidebar"); // Ambil tombol
    const btnMobile = document.querySelector(".btn-mobile"); // Ambil tombol

    sidebar.classList.toggle("active");
    pageCard.classList.toggle("col-md-9");
    pageCard.classList.toggle("col-md-12");

    // Sembunyikan/tampilkan tombol berdasarkan status sidebar
    if (sidebar.classList.contains("active")) {
        btnSidebar.style.display = "none";
        btnMobile.style.display = "none";
    } else {
        btnSidebar.style.display = "block";
        btnMobile.style.display = "block"; // Sesuaikan dengan kebutuhan tampilan mobile
    }
}

// Automatically hide sidebar on mobile when clicking outside
document.addEventListener("click", function (event) {
    const sidebar = document.querySelector(".side-bar");
    const toggleButton = document.querySelector(".btn-sidebar");
    const btnMobile = document.querySelector(".btn-mobile");
    const pageCard = document.querySelector("#pagecard");

    const isClickInside =
        sidebar.contains(event.target) ||
        toggleButton.contains(event.target) ||
        btnMobile.contains(event.target);

    if (!isClickInside && window.innerWidth <= 767.98) {
        sidebar.classList.remove("active");
        pageCard.classList.add("col-md-12");
        pageCard.classList.remove("col-md-9");
        toggleButton.style.display = "block";
        btnMobile.style.display = "block"; // Pastikan tombol mobile tetap terlihat
    }
});

// Mencegah label memicu fungsi toggleCCTV
document.querySelectorAll(".form-check-label").forEach((label) => {
    label.addEventListener("click", function (event) {
        event.preventDefault();
        const checkbox = document.querySelector(
            "#" + label.getAttribute("for")
        );
        checkbox.checked = !checkbox.checked;

        const id = checkbox.id.replace("checkbox-", "");
        toggleCCTV(id, checkbox);
    });
});

// Fungsi untuk menyembunyikan semua CCTV
function hideAllCCTV() {
    // Hentikan semua streaming dan sembunyikan CCTV
    document.querySelectorAll(".cctv-view").forEach((element) => {
        const iframe = element.querySelector("iframe");
        if (iframe) iframe.src = "";
        element.style.display = "none";
    });

    // Reset UI
    document.querySelectorAll(".icon-toggle.fa-eye-slash").forEach((icon) => {
        icon.classList.replace("fa-eye-slash", "fa-eye");
    });

    // Reset semua checkbox dan hapus statusnya dari localStorage
    document.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
        checkbox.checked = false;
        localStorage.removeItem(checkbox.id); // Hapus status per checkbox
    });

    // Reset dropdown
    const dropdown = document.getElementById("school-dropdown");
    if (dropdown) dropdown.value = "";

    // Hapus SEMUA data terkait CCTV dari localStorage
    localStorage.removeItem("activeWilayah");
    localStorage.removeItem("activeCCTVs");
    localStorage.removeItem("selectedSchool");

    // Hapus hash URL
    window.location.hash = "";
}

function removeCCTVFromHash(id) {
    const currentHash = window.location.hash.replace("#", "");
    const cctvList = currentHash ? decodeHash(currentHash).split(",") : [];
    const updatedList = cctvList.filter((cctv) => cctv !== id);
    window.location.hash = encodeHash(updatedList.join(",")); // Update URL hash dengan hash yang di-encode
}

// Ambil pilihan terakhir dari localStorage saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
    loadActiveSchoolsFromLocalStorage();
    updateStatistics();

    // 1. Pulihkan status checkbox dari localStorage
    document
        .querySelectorAll('input[type="checkbox"][id^="checkbox-"]')
        .forEach((checkbox) => {
            const savedState = localStorage.getItem(checkbox.id);
            if (savedState !== null) {
                checkbox.checked = savedState === "true";
                const containerId = checkbox.id.replace("checkbox-", "");
                toggleCCTV(containerId, checkbox);
            }
        });

    // 2. Restore dropdown lokasi
    const selectedSchool = localStorage.getItem("selectedSchool");
    if (selectedSchool) {
        document.getElementById("school-dropdown").value = selectedSchool;
        filterCCTVBySchool(selectedSchool);
    }
});

function loadActiveSchoolsFromLocalStorage() {
    const activeWilayah =
        JSON.parse(localStorage.getItem("activeWilayah")) || [];
    setTimeout(() => {
        activeWilayah.forEach((wilayah) => {
            const cctvToShow = document.querySelectorAll(
                `.cctv-view[data-wilayah="${wilayah}"]`
            );

            cctvToShow.forEach((cctv) => {
                cctv.style.display = "block";
                const iframe = cctv.querySelector("iframe");
                if (iframe) {
                    iframe.src = iframe.getAttribute("data-src");
                }
            });

            const icon = document.querySelector(
                `.icon-toggle[onclick*="${wilayah}"]`
            );

            if (icon) {
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }

            const checkboxes = document.querySelectorAll(
                `input[data-wilayah="${wilayah}"]`
            );
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
                toggleCCTV(checkbox.id.replace("checkbox-", ""), checkbox);
            });
        });
    }, 300); // delay kecil supaya DOM siap
}

// Fungsi untuk menghitung dan menampilkan statistik
function updateStatistics() {
    // Hitung jumlah CCTV
    const cctvCount = document.querySelectorAll(".cctv-view").length;

    // Ambil jumlah wilayah dari data yang disimpan di Blade
    const regionCount = document
        .getElementById("regionCountData")
        .getAttribute("data-region-count");

    // Update tampilan
    document.getElementById("cctvCount").textContent = cctvCount;
    document.getElementById("regionCount").textContent = regionCount;
}
