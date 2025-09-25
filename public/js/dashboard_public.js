// Global functions that need to be accessible from onclick attributes
// (debug logs removed)
        
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('active');
            
            const chevron = document.getElementById(dropdownId.replace('Dropdown', 'Chevron'));
            if (chevron) {
                chevron.classList.toggle('fa-chevron-down');
                chevron.classList.toggle('fa-chevron-right');
            }
        }

        function toggleSubDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('active');
            
            const chevron = document.getElementById(dropdownId.replace('Dropdown', 'Chevron'));
            if (chevron) {
                chevron.classList.toggle('fa-chevron-down');
                chevron.classList.toggle('fa-chevron-right');
            }
        }

        function toggleSchool(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('active');
            
            const chevron = document.getElementById(dropdownId + 'Chevron');
            if (chevron) {
                chevron.classList.toggle('fa-chevron-down');
                chevron.classList.toggle('fa-chevron-right');
            }
        }

        // Helper to sync a chevron icon with the dropdown state
        function syncChevronForDropdown(dropdownId) {
            try {
                const dropdown = document.getElementById(dropdownId);
                if (!dropdown) return;
                const chevronId = dropdownId.replace('Dropdown', 'Chevron');
                const chevron = document.getElementById(chevronId);
                if (!chevron) return;
                const isOpen = dropdown.classList.contains('active') || dropdown.classList.contains('show') || (dropdown.style && dropdown.style.display && dropdown.style.display !== 'none');
                chevron.classList.remove('fa-chevron-down', 'fa-chevron-right');
                chevron.classList.add(isOpen ? 'fa-chevron-down' : 'fa-chevron-right');
            } catch (e) { /* ignore */ }
        }

        // Sync all chevrons under sidebar (called on init)
        function syncAllChevrons() {
            document.querySelectorAll('[id$="Dropdown"]').forEach(drop => {
                syncChevronForDropdown(drop.id);
            });
        }

        function renderCard(item) {
            const grid = document.getElementById('cctvGrid');
            if (!grid || document.getElementById(item.cardId)) return;
            const wrap = document.createElement('div');
            wrap.className = 'cctv-card';
            wrap.id = item.cardId;
                // Accept either a provided sekolahSlug or derive one from the wilayah/name
                wrap.dataset.sekolah = item.sekolahSlug || (typeof slugify === 'function' ? slugify(item.wilayah || item.sekolah || '') : (item.wilayah || item.sekolah || ''));
            wrap.dataset.wilayah = item.wilayah;
            wrap.dataset.titik = item.titik;
            wrap.dataset.isActive = item.active ? '1' : '0';
            wrap.style.display = 'none';
            // Resolve display name: prefer explicit sekolah, then mapped wilayah name, then raw wilayah
            const sekolahName = item.sekolah || (window.namaWilayahLengkap ? (window.namaWilayahLengkap[item.wilayah] || item.wilayah) : (item.wilayah || ''));
            wrap.innerHTML = `
                <div class="card shadow-sm h-100">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">${sekolahName}</h6>
                            <label class="toggle-switch">
                                <input type="checkbox" ${item.active ? 'checked' : ''} onchange="toggleCCTV(this, '${item.cardId}')">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <p class="text-center mb-2" style="font-size: 0.8rem;">${shortNameOf(item.titik) || (item.titik || '(tidak ada nama titik)')}</p>
                        <div class="iframe-container">
                            <div class="iframe-loader">Memuat...</div>
                            <iframe loading="lazy" data-src="${item.link}" frameborder="0" allowfullscreen title="CCTV Live Stream"></iframe>
                        </div>
                        <!-- STATUS DISPLAY DISABLED: Untuk menonaktifkan sementara tampilan status Online/Offline
                             Untuk mengaktifkan kembali, kembalikan blok HTML berikut:
                             <div class="status-indicator mt-2">
                                 <div class="status-dot"></div>
                                 <span>${item.active ? 'Online' : 'Offline'}</span>
                             </div>
                        -->
                    </div>
                </div>
            `;
            grid.appendChild(wrap);
        }

        // Helper to find the checkbox inside a card (supports multiple markup variants)
        function getCardCheckbox(cardEl) {
            if (!cardEl) return null;
            // Prefer explicit checkbox inputs inside the card
            return cardEl.querySelector('input[type="checkbox"]') || cardEl.querySelector('.form-check-input') || cardEl.querySelector('.toggle-switch input');
        }

        function ensureCardRenderedById(cardId) {
                    if (document.getElementById(cardId)) {
                        return true;
                    }
                    // Safe lookup: prefer fast map but fallback to searching the index if map missing
                    let item = (window.cctvById && window.cctvById[cardId]) || null;
                    if (!item) {
                        try {
                            const idx = window.cctvIndex || {};
                            for (const list of Object.values(idx)) {
                                if (!list) continue;
                                const found = list.find(i => i && i.cardId === cardId);
                                if (found) { item = found; break; }
                            }
                        } catch (e) {
                            // ignore and continue - item will be null
                        }
                    }
                    if (!item) {
                        return false;
                    }
                    // rendering from index
                    renderCard(item);
                    const ok = !!document.getElementById(cardId);
                    return ok;
        }
        function showCard(cardEl) {
            if (!cardEl) return;
            try { console.debug('[showCard]', cardEl.id); } catch(e){}
            const iframe = cardEl.querySelector('iframe');
            if (iframe && !iframe.src) iframe.src = iframe.getAttribute('data-src') || '';
            cardEl.style.display = '';
            cardEl.dataset.isActive = '1';
            // Sync card toggle
                const cardToggle = getCardCheckbox(cardEl);
                if (cardToggle) cardToggle.checked = true;
            // Sync sidebar checkbox
            const sidebarCheckbox = document.getElementById('checkbox-' + cardEl.id);
            if (sidebarCheckbox) sidebarCheckbox.checked = true;
            /* STATUS UPDATES DISABLED: blok berikut men-set teks "Online" dan warna dot.
               Untuk mengaktifkan kembali, hapus komentar ini dan restore isi try/catch.
            try {
                const statusSpan = cardEl.querySelector('.status-indicator span');
                const statusDot = cardEl.querySelector('.status-dot');
                if (statusSpan) statusSpan.textContent = 'Online';
                if (statusDot) {
                    statusDot.style.background = getComputedStyle(document.documentElement).getPropertyValue('--success-color') || '#27ae60';
                }
            } catch (e) { }
            */
            updateSchoolEyeIcon(cardEl.dataset.sekolah);
            updateHideAllButtonState();
            updateActiveCCTVCount();
            saveActiveCCTVState();
        }
        function hideCard(cardEl) {
            if (!cardEl) return;
            try { console.debug('[hideCard]', cardEl.id); } catch(e){}
            cardEl.style.display = 'none';
            cardEl.dataset.isActive = '0';
                const cardToggle = getCardCheckbox(cardEl);
                if (cardToggle) cardToggle.checked = false;
            const sidebarCheckbox = document.getElementById('checkbox-' + cardEl.id);
            if (sidebarCheckbox) sidebarCheckbox.checked = false;
            /* STATUS UPDATES DISABLED: blok berikut men-set teks "Offline" dan warna dot.
               Untuk mengaktifkan kembali, hapus komentar ini dan restore isi try/catch.
            try {
                const statusSpan = cardEl.querySelector('.status-indicator span');
                const statusDot = cardEl.querySelector('.status-dot');
                if (statusSpan) statusSpan.textContent = 'Offline';
                if (statusDot) {
                    statusDot.style.background = '#6c757d';
                }
            } catch (e) { }
            */
            updateSchoolEyeIcon(cardEl.dataset.sekolah);
            updateHideAllButtonState();
            updateActiveCCTVCount();
            saveActiveCCTVState();
        }

        function visibleCardsCount() {
            return Array.from(document.querySelectorAll('#cctvGrid .cctv-card'))
                .filter(card => {
                    // Explicit opt-out: cards marked with data-countable="0" or class 'no-count' should not be counted
                    if (card.dataset && card.dataset.countable === '0') return false;
                    if (card.classList && card.classList.contains('no-count')) return false;

                    // Note: do not exclude cards from count based on status text (Online/Offline)
                    // Previous behavior filtered out cards containing "Offline" which caused
                    // the top hide/show control to ignore visible cards. We want to count
                    // any card that is currently visible or whose dataset.isActive is set.
                    // Prefer explicit dataset flag if present
                    if (card.dataset && card.dataset.isActive === '1') return true;
                    // Fallback to inline style display
                    if (card.style && card.style.display && card.style.display !== 'none') return true;
                    // Final fallback: check the toggle inside the card
                    const toggle = card.querySelector('.toggle-switch input[type="checkbox"]');
                    if (toggle && toggle.checked) return true;
                    return false;
                }).length;
        }

        function updateHideAllButtonState() {
            const btn = document.getElementById('sidebarHideAllBtn');
            const icon = document.getElementById('sidebarHideAllIcon');
            const hasVisible = visibleCardsCount() > 0;

            btn.disabled = !hasVisible;
            icon.classList.remove('fa-eye', 'fa-eye-slash');
            // ada yang tampil => tombol “Sembunyikan” aktif (ikon eye-slash), tidak ada => ikon eye
            icon.classList.add(hasVisible ? 'fa-eye-slash' : 'fa-eye');
            // Toggle visual 'active' state to match Sekolah theme
            if (hasVisible) {
                btn.classList.add('active-hide');
            } else {
                btn.classList.remove('active-hide');
            }
        }

        // Ikon per-sekolah berdasarkan visibilitas kartu di grid
        function updateSchoolEyeIcon(sekolahSlug) {
            const eye = document.getElementById('eye-' + sekolahSlug);
            if (!eye) return;
            const anyVisible = Array.from(document.querySelectorAll(`#cctvGrid .cctv-card[data-sekolah="${sekolahSlug}"]`))
                .some(card => (card.dataset && card.dataset.isActive === '1') || (card.style && card.style.display && card.style.display !== 'none'));
            eye.classList.remove('fa-eye', 'fa-eye-slash');
            eye.classList.add(anyVisible ? 'fa-eye' : 'fa-eye-slash');
        }

        // Helper: toggle/select by cardId (used by Panorama sidebar)
        function selectCCTVById(cardId) {
            if (!ensureCardRenderedById(cardId)) return;
            const el = document.getElementById(cardId);
            if (!el) return;
            const currentlyVisible = el.dataset && el.dataset.isActive === '1';
            if (currentlyVisible) {
                hideCard(el);
            } else {
                // ensure iframe loading UX
                const iframe = el.querySelector('iframe');
                const loader = el.querySelector('.iframe-loader');
                if (iframe && !iframe.src && loader) {
                    loader.style.display = 'block';
                    iframe.src = iframe.dataset.src;
                    iframe.onload = () => { if (loader) loader.style.display = 'none'; };
                }
                showCard(el);
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            // Sync icons/counts
            updateHideAllButtonState();
            if (el && el.dataset && el.dataset.sekolah) updateSchoolEyeIcon(el.dataset.sekolah);
        }

        function hideAllVisibleCCTV() {
            const cards = document.querySelectorAll('.cctv-card');

            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    card.style.display = 'none';
                    card.dataset.isActive = '0';

                    const cardCheckbox = card.querySelector('.toggle-switch input[type="checkbox"]');
                    if (cardCheckbox) cardCheckbox.checked = false;

                    const sidebarCheckbox = document.getElementById(`checkbox-${card.id}`);
                    if (sidebarCheckbox) sidebarCheckbox.checked = false;
                }
            });

            // Sinkronkan semua ikon mata per-sekolah setelah disembunyikan
            document.querySelectorAll('[id^="eye-"]').forEach(icon => {
                const slug = icon.id.replace('eye-', '');
                updateSchoolEyeIcon(slug);
            });

            updateHideAllButtonState();
            updateActiveCCTVCount();
            saveActiveCCTVState();
        }

        // Fungsi untuk memilih CCTV dari sidebar
        function selectCCTV(namaSekolah, namaTitik) {
            const targetId = `${slugify(namaSekolah)}-${slugify(namaTitik)}`;
            // Pastikan kartu tersedia / dirender
            if (!ensureCardRenderedById(targetId)) return;
            const selectedCCTV = document.getElementById(targetId);
            if (!selectedCCTV) return;

            const alreadyVisible = selectedCCTV.style.display !== 'none';
            // Jika sudah terlihat -> klik lagi = sembunyikan saja
            if (alreadyVisible) {
                hideCard(selectedCCTV);
                return;
            }

            const iframe = selectedCCTV.querySelector('iframe');
            const loader = selectedCCTV.querySelector('.iframe-loader');

            if (iframe && !iframe.src && loader) {
                loader.style.display = 'block';
                iframe.src = iframe.dataset.src;
                iframe.onload = () => { if (loader) loader.style.display = 'none'; };
            }

            // Mode multi: langsung tampilkan tanpa menyembunyikan yang lain
            // Tampilkan CCTV yang dipilih (showCard akan sync toggle & checkbox)
            showCard(selectedCCTV);

            updateActiveCCTVCount();
            saveActiveCCTVState();
        }

        // Fungsi untuk menampilkan semua CCTV di sekolah tertentu
        function toggleAllSchoolCCTV(namaSekolah) {
                    const schoolSlug = slugify(namaSekolah);
                    // toggleAllSchoolCCTV debug removed

                    // Collect card IDs for this school: DOM cards + index entries if any
                    let cardEls = Array.from(document.querySelectorAll(`.cctv-card[data-sekolah="${schoolSlug}"]`));
                    const domIds = cardEls.map(c => c.id);
                    const indexList = (window.cctvIndex && window.cctvIndex[schoolSlug]) || [];
                    const indexIds = indexList.map(i => i.cardId).filter(id => !domIds.includes(id));

                    // If there are index ids that aren't rendered yet, render them so we can toggle reliably
                    indexIds.forEach(id => { ensureCardRenderedById(id); });

                    // Recollect cards after potential rendering
                    cardEls = Array.from(document.querySelectorAll(`.cctv-card[data-sekolah="${schoolSlug}"]`));

                    // Determine current state by dataset flag or visible style
                    const anyActive = cardEls.some(card => (card.dataset && card.dataset.isActive === '1') || (card.style && card.style.display && card.style.display !== 'none'));
                    const targetShow = !anyActive;

                    // Toggle each card directly and then sync sidebar checkbox state so UI follows immediately
                    cardEls.forEach(card => {
                        const el = document.getElementById(card.id);
                        if (!el) return; // nothing to do for this id

                        if (targetShow) {
                            showCard(el);
                        } else {
                            hideCard(el);
                        }

                        // Sync sidebar checkbox if exists (no need to dispatch change)
                        const sidebarCheckbox = document.getElementById(`checkbox-${card.id}`);
                        if (sidebarCheckbox) sidebarCheckbox.checked = targetShow;
                    });

                    // Update the eye icon directly and sync counts/state
                    const eyeIcon = document.getElementById(`eye-${schoolSlug}`);
                    if (eyeIcon) {
                        eyeIcon.classList.remove('fa-eye', 'fa-eye-slash');
                        eyeIcon.classList.add(targetShow ? 'fa-eye' : 'fa-eye-slash');
                    }

                    updateSchoolEyeIcon(schoolSlug);
                    updateHideAllButtonState();
                    updateActiveCCTVCount();
                    saveActiveCCTVState();
        }

        // Toggle dari switch pada kartu di grid (satu-satunya definisi; hapus duplikat di bawah)
        function toggleCCTV(el, cardId) {
            const card = document.getElementById(cardId);
            if (!card) return;

            const sidebarCheckbox = document.getElementById('checkbox-' + cardId);
            const checked = !!el.checked;

            if (checked) {
                showCard(card);
                card.dataset.isActive = '1';
                if (sidebarCheckbox) sidebarCheckbox.checked = true;
                const iframe = card.querySelector('iframe');
                if (iframe && !iframe.src) iframe.src = iframe.dataset.src;
            } else {
                hideCard(card);
                card.dataset.isActive = '0';
                if (sidebarCheckbox) sidebarCheckbox.checked = false;
            }

            updateSchoolEyeIcon(card.dataset.sekolah);
            updateHideAllButtonState();
            updateActiveCCTVCount();
            saveActiveCCTVState();
        }

        // Toggle dari checkbox di sidebar per titik CCTV
        function toggleCCTVFromSidebar(checkbox) {
            const cardId = checkbox.dataset.cardId;
            let card = document.getElementById(cardId);

            // Jika belum dirender dan ingin ditampilkan -> render dulu
            if (!card && checkbox.checked) {
                ensureCardRenderedById(cardId);
                card = document.getElementById(cardId);
            }
            if (!card) return;

            if (checkbox.checked) {
                card.style.display = 'block';
                card.dataset.isActive = '1';
                    const cardCheckbox = getCardCheckbox(card);
                    if (cardCheckbox) cardCheckbox.checked = true;
                const iframe = card.querySelector('iframe');
                if (iframe && !iframe.src) iframe.src = iframe.dataset.src;
                /* STATUS UPDATES DISABLED: mark online block
                try {
                    const statusSpan = card.querySelector('.status-indicator span');
                    const statusDot = card.querySelector('.status-dot');
                    if (statusSpan) statusSpan.textContent = 'Online';
                    if (statusDot) statusDot.style.background = getComputedStyle(document.documentElement).getPropertyValue('--success-color') || '#27ae60';
                } catch (e) {}
                */
            } else {
                card.style.display = 'none';
                card.dataset.isActive = '0';
                    const cardCheckbox = getCardCheckbox(card);
                    if (cardCheckbox) cardCheckbox.checked = false;
                /* STATUS UPDATES DISABLED: mark offline block
                try {
                    const statusSpan = card.querySelector('.status-indicator span');
                    const statusDot = card.querySelector('.status-dot');
                    if (statusSpan) statusSpan.textContent = 'Offline';
                    if (statusDot) statusDot.style.background = '#6c757d';
                } catch (e) {}
                */
            }

            const sekolahSlug = card.dataset.sekolah;
            // Sync per-school eye icon and the top hide-all control.
            updateSchoolEyeIcon(sekolahSlug);
            // Update both implementations (newer state function + legacy text/icon updater)
            updateHideAllButtonState();
            if (typeof updateHideAllButton === 'function') updateHideAllButton();
            updateActiveCCTVCount();
            saveActiveCCTVState();
        }

    // (Definisi toggleCCTV duplikat dihapus untuk mencegah perilaku tidak konsisten)

        // Tampilkan jumlah CCTV aktif (kartu yang terlihat) secara live
        function updateActiveCCTVCount() {
            const el = document.getElementById('activeCCTVCountCard');
            if (!el) return;
            el.textContent = visibleCardsCount();
        }

        // Fungsi helper untuk slugify
        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        // Fungsi untuk dashboard admin
        function showDashboard() {
            alert('Dashboard Admin - Fitur ini akan menampilkan statistik lengkap, laporan keamanan, dan pengaturan sistem CCTV.');
            closeSidebar();
        }

        // Fungsi untuk about
        function showAbout() {
            alert('Tentang Sistem:\n\nDashboard CCTV Sekolah DIY\nVersi 1.0\n\nSistem monitoring keamanan sekolah yang terintegrasi untuk memantau kondisi keamanan di seluruh sekolah di Daerah Istimewa Yogyakarta.\n\nDikembangkan oleh Dinas Pendidikan DIY');
            closeSidebar();
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Update tombol hide/show berdasarkan CCTV yang terlihat
        function updateHideAllButton() {
            const visibleCards = document.querySelectorAll('.cctv-card[style*="display: block"]');
            const hideAllBtn = document.getElementById('sidebarHideAllBtn');
            const hideAllText = document.getElementById('sidebarHideAllText');
            const hideAllIcon = hideAllBtn.querySelector('i');
            
            if (visibleCards.length > 0) {
                hideAllText.textContent = 'Sembunyikan';
                hideAllIcon.className = 'fas fa-eye-slash me-1';
                hideAllBtn.disabled = false;
                hideAllBtn.style.opacity = '1';
            }
        }

        // Helper: Save active CCTV card IDs to localStorage
        function saveActiveCCTVState() {
            const activeIds = Array.from(document.querySelectorAll('#cctvGrid .cctv-card'))
                .filter(card => card.style.display !== 'none')
                .map(card => card.id);
            localStorage.setItem('activeCCTVCardIds', JSON.stringify(activeIds));
        }

        // Helper: Restore active CCTV card IDs from localStorage (respect user preference)
        function restoreActiveCCTVState() {
            const restorePref = JSON.parse(localStorage.getItem('restoreActiveOnLoad') || 'false');
            const activeIds = JSON.parse(localStorage.getItem('activeCCTVCardIds') || '[]');

            activeIds.forEach(cardId => {
                if (!ensureCardRenderedById(cardId)) return;
                const card = document.getElementById(cardId);
                if (!card) return;

                if (restorePref) {
                    // User opted in: show card but do lazy load (loader handled in showCard/toggle handlers)
                    // Use showCard which will set iframe.src and sync states
                    showCard(card);
                } else {
                    // Default: keep cards hidden and ensure toggles/checkboxes are unchecked
                    card.style.display = 'none';
                    card.dataset.isActive = '0';
                    const sidebarCheckbox = document.getElementById('checkbox-' + cardId);
                    if (sidebarCheckbox) sidebarCheckbox.checked = false;
                    const cardCheckbox = card.querySelector('.toggle-switch input');
                    if (cardCheckbox) cardCheckbox.checked = false;
                }
            });

            // Sync icons and counts without triggering iframe loads unnecessarily
            document.querySelectorAll('[id^="eye-"]').forEach(icon => {
                const slug = icon.id.replace('eye-', '');
                updateSchoolEyeIcon(slug);
            });
            updateHideAllButtonState();
            updateActiveCCTVCount();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi sidebar
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Hitung tinggi navbar aktual lalu set CSS variable agar jarak atas konsisten di mobile & desktop
            const nav = document.querySelector('.navbar');
            if (nav) {
                const h = nav.getBoundingClientRect().height;
                document.documentElement.style.setProperty('--navbar-height', h + 'px');
            }

            // Initialize card toggles and wire change listeners to keep top controls in sync
            document.querySelectorAll('.toggle-switch input').forEach(toggle => {
                toggle.checked = false;
                // ensure hide-all icon updates when a card toggle changes
                toggle.addEventListener('change', function() {
                    try { updateHideAllButtonState(); updateActiveCCTVCount(); if (this.closest && this.closest('.cctv-card')) updateSchoolEyeIcon(this.closest('.cctv-card').dataset.sekolah); } catch(e) {}
                });
            });

            // Initialize sidebar checkboxes and wire change listeners so top hide button follows immediately
            document.querySelectorAll('.cctv-point-checkbox').forEach(sidebarCheckbox => {
                sidebarCheckbox.checked = false;
                sidebarCheckbox.addEventListener('change', function() {
                    // Slight delay to let existing handlers run (if they render cards)
                    setTimeout(() => {
                        try {
                            updateHideAllButtonState();
                            updateActiveCCTVCount();
                            // If related card exists, update its school eye icon
                            const id = this.dataset && this.dataset.cardId;
                            if (id) {
                                const card = document.getElementById(id);
                                if (card && card.dataset && card.dataset.sekolah) updateSchoolEyeIcon(card.dataset.sekolah);
                            }
                        } catch (e) {}
                    }, 30);
                });
            });

        document.querySelectorAll('.cctv-card').forEach(card => {
            card.style.display = 'none';
        });

        updateActiveCCTVCount();

            function openSidebar() {
                sidebar.classList.add('active');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                document.getElementById('currentTime').textContent = timeString;
            }

            // Panggil sekali untuk isi awal
            updateTime();

            // Perbarui setiap detik
            setInterval(updateTime, 1000);

            sidebarToggle.addEventListener('click', openSidebar);
            sidebarClose.addEventListener('click', closeSidebar);
            sidebarOverlay.addEventListener('click', closeSidebar);
            
            // Fungsi untuk hide/show semua CCTV
            document.getElementById('sidebarHideAllBtn').addEventListener('click', function() {
                // Collect all cards and pick those that are effectively visible
                const allCards = Array.from(document.querySelectorAll('#cctvGrid .cctv-card'));
                const visibleCards = allCards.filter(card => (card.dataset && card.dataset.isActive === '1') || (card.style && card.style.display && card.style.display !== 'none'));

                if (visibleCards.length > 0) {
                    // Hide each visible card using hideCard to keep state in sync
                    visibleCards.forEach(card => {
                        try { hideCard(card); } catch (e) { 
                            // fallback: manual hide
                            card.style.display = 'none';
                            card.dataset.isActive = '0';
                            const cb = getCardCheckbox(card);
                            if (cb) cb.checked = false;
                            const sidebarCheckbox = document.getElementById(`checkbox-${card.id}`);
                            if (sidebarCheckbox) sidebarCheckbox.checked = false;
                        }
                    });
                }

                // After hiding all, sync icons, controls and persist state
                document.querySelectorAll('[id^="eye-"]').forEach(icon => {
                    const slug = icon.id.replace('eye-', '');
                    updateSchoolEyeIcon(slug);
                });
                updateHideAllButtonState();
                if (typeof updateHideAllButton === 'function') updateHideAllButton();
                updateActiveCCTVCount();
                saveActiveCCTVState();
            });

            // Fungsi untuk pencarian di sidebar - pasang sesuai elemen yang tersedia
            (function() {
                function normalize(s) { try { return (s||'').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^\w\s-]/g,' ').replace(/\s+/g,' ').trim().toLowerCase(); } catch(e) { return (s||'').toString().toLowerCase().replace(/[^a-z0-9\s-]/g,' ').replace(/\s+/g,' ').trim(); } }

                const legacy = document.getElementById('sidebarSearchInput');
                const panorama = document.getElementById('sidebarSearchInputPanorama');

                // search init logs removed

                if (legacy) {
                    legacy.addEventListener('input', function() {
                        const q = (this.value||'').toLowerCase();
                        const items = document.querySelectorAll('.dropdown-item');
                        if (!q) { items.forEach(i=>i.style.display='block'); return; }
                        items.forEach(it => { it.style.display = (it.textContent||'').toLowerCase().indexOf(q) !== -1 ? 'block' : 'none'; });
                        return;
                    });
                }

                if (panorama) {
                    // panorama listener attached
                    panorama.addEventListener('input', function() {
                        const raw = this.value || '';
                        const q = normalize(raw);
                        const topItems = document.querySelectorAll('#cctvDropdown > .dropdown-item');
                        if (!q) { topItems.forEach(i=>i.style.display='block'); return; }
                        let matched = 0;
                        topItems.forEach(it => {
                            const left = it.querySelector('div');
                            const label = left ? left.textContent : it.textContent;
                            const nl = normalize(label || '');
                            const match = nl.indexOf(q) !== -1 || nl.split(' ').some(t=>t.indexOf(q)!==-1);
                            if (match) matched++;
                            // Use hidden + important display to avoid CSS overriding inline display
                            try {
                                if (match) {
                                    it.hidden = false;
                                    it.removeAttribute('aria-hidden');
                                    // remove forced inline display so stylesheet controls layout (flex/inline-flex etc)
                                    it.style.removeProperty('display');
                                } else {
                                    it.hidden = true;
                                    it.setAttribute('aria-hidden', 'true');
                                    it.style.setProperty('display', 'none', 'important');
                                }
                            } catch (e) {
                                // fallback
                                it.style.display = match ? '' : 'none';
                            }
                        });
                        // panorama matched log removed
                    });
                }
            })();

            // Initialize restore preference UI (if present) and wire persistence
            try {
                const restoreToggle = document.getElementById('restoreToggle');
                const pref = JSON.parse(localStorage.getItem('restoreActiveOnLoad') || 'false');
                if (restoreToggle) {
                    restoreToggle.checked = !!pref;
                    restoreToggle.addEventListener('change', function() {
                        localStorage.setItem('restoreActiveOnLoad', JSON.stringify(!!this.checked));
                    });
                }
            } catch (e) {
                // ignore storage issues
            }

            // Ensure every eye icon has a reliable click handler (prevent relying on inline handlers)
            document.querySelectorAll('[id^="eye-"]').forEach(icon => {
                    try {
                    icon.style.cursor = 'pointer';
                    // Remove any previous bound listener by cloning the node (defensive)
                    const newIcon = icon.cloneNode(true);
                    // Remove inline onclick attribute so we don't call toggle twice (inline + listener)
                    try { newIcon.removeAttribute && newIcon.removeAttribute('onclick'); } catch (e) {}
                    icon.parentNode.replaceChild(newIcon, icon);
                    newIcon.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const slug = (this.id || '').replace('eye-', '');
                        try { toggleAllSchoolCCTV(slug); } catch (err) { console.error(err); }
                    });
                } catch (err) {
                    // ignore per-icon errors
                }
            });

            // Sync chevrons and restore state
            syncAllChevrons();
            restoreActiveCCTVState();
        });