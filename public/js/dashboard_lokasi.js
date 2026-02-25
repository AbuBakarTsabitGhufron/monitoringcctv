const cctvData = [];

        let allHidden = false;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            generateCCTVCards();
            updateTime();
            setInterval(updateTime, 1000);
        });

        // Sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        sidebarToggle.addEventListener('click', openSidebar);
        sidebarClose.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);

        // Dropdown functionality
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const isActive = dropdown.classList.toggle('active');

            const chevronId = dropdownId.replace('Dropdown', 'Chevron');
            const chevron = document.getElementById(chevronId);

            if (chevron) {
                chevron.classList.remove(isActive ? 'fa-chevron-right' : 'fa-chevron-down');
                chevron.classList.add(isActive ? 'fa-chevron-down' : 'fa-chevron-right');
            }
        }


        function toggleSubDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const isActive = dropdown.classList.toggle('active');

            const chevronId = dropdownId.replace('Dropdown', 'Chevron');
            const chevron = document.getElementById(chevronId);

            if (chevron) {
                chevron.classList.remove(isActive ? 'fa-chevron-right' : 'fa-chevron-down');
                chevron.classList.add(isActive ? 'fa-chevron-down' : 'fa-chevron-right');
            }
        }



        function toggleSchoolType(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle('active');
        }

        function toggleSchool(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const isActive = dropdown.classList.toggle('active');

            const chevronId = dropdownId + 'Chevron';
            const chevron = document.getElementById(chevronId);

            if (chevron) {
                chevron.classList.remove(isActive ? 'fa-chevron-right' : 'fa-chevron-down');
                chevron.classList.add(isActive ? 'fa-chevron-down' : 'fa-chevron-right');
            }
        }



        function selectCCTV(namaLokasi, namaTitik) {
            // Sembunyikan semua CCTV dulu
            const allCCTV = document.querySelectorAll('.cctv-card');
            allCCTV.forEach(card => {
                card.style.display = 'none';
            });

            // Temukan elemen CCTV berdasarkan nama lokasi dan titik
            const targetId = `${slugify(namaLokasi)}-${slugify(namaTitik)}`;
            const selectedCCTV = document.getElementById(targetId);
            
            if (selectedCCTV) {
                selectedCCTV.style.display = 'block';

                // Lazy load iframe jika belum dimuat
                const iframe = selectedCCTV.querySelector('iframe');
                if (iframe && !iframe.src) {
                    iframe.src = iframe.dataset.src;
                }
            }
        }

        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Ganti spasi dengan -
                .replace(/[^\w\-]+/g, '')       // Hapus karakter tak valid
                .replace(/\-\-+/g, '-')         // Ganti -- dengan -
                .replace(/^-+/, '')             // Hapus strip di awal
                .replace(/-+$/, '');            // Hapus strip di akhir
        }



        // Generate CCTV cards
        function generateCCTVCards() {
            const grid = document.getElementById('cctvGrid');
            grid.innerHTML = '';

            cctvData.forEach((cctv, index) => {
                const id = `${slugify(cctv.school)}-${slugify(cctv.location)}`;
                const singkatan = getSingkatan(cctv.location);

                const card = document.createElement('div');
                card.className = 'cctv-card';
                card.id = id;
                card.setAttribute('data-lokasi', slugify(cctv.school));
                card.setAttribute('data-wilayah', cctv.wilayah);
                card.setAttribute('data-titik', cctv.location);
                card.style.display = 'none';

                card.innerHTML = `
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <h6 class="text-center mb-1" style="font-weight: bold;">${cctv.school}</h6>
                            <p class="text-center mb-2" style="font-size: 0.8rem;">${singkatan}</p>
                            <div class="iframe-container">
                                <iframe data-src="${cctv.link}" frameborder="0" allowfullscreen title="CCTV Live Stream"></iframe>
                            </div>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function getSingkatan(namaTitik) {
            const kata = namaTitik.trim().split(' ');
            if (kata.length > 3) {
                return kata.map(w => w[0]?.toUpperCase()).join('');
            }
            return namaTitik;
        }



        // Toggle individual CCTV
        function toggleCCTV(checkbox, index) {
            const cards = document.querySelectorAll('.cctv-card');
            const card = cards[index];
            
            if (checkbox.checked) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
            
            updateActiveCCTVCount();
        }

        // Update active CCTV count
        function updateActiveCCTVCount() {
            const visibleCards = document.querySelectorAll('.cctv-card:not(.hidden)');
            const activeCount = visibleCards.length;
            document.getElementById('activeCCTVCountCard').textContent = activeCount;
        }

        // Hide/Show all CCTV from sidebar
        document.getElementById('sidebarHideAllBtn').addEventListener('click', function() {
            const cards = document.querySelectorAll('.cctv-card');
            const checkboxes = document.querySelectorAll('.toggle-switch input');
            const hideAllText = document.getElementById('sidebarHideAllText');
            const hideAllIcon = this.querySelector('i');
            
            allHidden = !allHidden;
            
            cards.forEach((card, index) => {
                if (allHidden) {
                    card.classList.add('hidden');
                    checkboxes[index].checked = false;
                } else {
                    card.classList.remove('hidden');
                    checkboxes[index].checked = true;
                }
            });
            
            if (allHidden) {
                hideAllText.textContent = 'Tampilkan Semua';
                hideAllIcon.className = 'fas fa-eye-slash me-2';
                this.style.background = 'var(--success-color)';
            } else {
                hideAllText.textContent = 'Sembunyikan Semua';
                hideAllIcon.className = 'fas fa-eye me-2';
                this.style.background = 'var(--accent-color)';
            }
            
            // Update active CCTV count
            updateActiveCCTVCount();
        });

        // Smart search functionality for sidebar - filters sidebar items only
        document.getElementById('sidebarSearchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            if (searchTerm.length > 0) {
                filterSidebarItems(searchTerm);
            } else {
                // Show all sidebar items when search is cleared
                showAllSidebarItems();
                collapseAllDropdowns();
            }
        });

        // Main page search functionality
        document.getElementById('mainSearchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterCCTVCards(searchTerm);
        });

        function filterCCTVCards(searchTerm) {
            const cards = document.querySelectorAll('.cctv-card');
            
            cards.forEach(card => {
                const school = card.querySelector('.cctv-title').textContent.toLowerCase();
                const location = card.querySelector('.cctv-location').textContent.toLowerCase();
                const region = cctvData.find(data => 
                    data.school === card.querySelector('.cctv-title').textContent && 
                    data.location === card.querySelector('.cctv-location').textContent
                )?.region.toLowerCase() || '';
                
                if (school.includes(searchTerm) || location.includes(searchTerm) || region.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterSidebarItems(searchTerm) {
            // Hide all dropdown items first
            const allDropdownItems = document.querySelectorAll('.dropdown-item');
            allDropdownItems.forEach(item => {
                item.style.display = 'none';
            });

            // Hide all dropdown contents
            const allDropdownContents = document.querySelectorAll('.dropdown-content');
            allDropdownContents.forEach(content => {
                content.style.display = 'none';
            });

            // Show main CCTV dropdown
            const cctvDropdown = document.getElementById('cctvDropdown');
            const cctvChevron = document.getElementById('cctvChevron');
            cctvDropdown.classList.add('active');
            cctvDropdown.style.display = 'block';
            cctvChevron.style.transform = 'rotate(180deg)';

            let hasMatches = false;

            // Check each dropdown item for matches
            allDropdownItems.forEach(item => {
                const itemText = item.textContent.toLowerCase();
                
                if (itemText.includes(searchTerm)) {
                    item.style.display = 'block';
                    hasMatches = true;
                    
                    // Show parent dropdowns
                    let parent = item.parentElement;
                    while (parent && parent.classList.contains('dropdown-content')) {
                        parent.style.display = 'block';
                        parent.classList.add('active');
                        parent = parent.parentElement?.parentElement;
                    }
                }
            });

            // If no matches, show message
            if (!hasMatches) {
                cctvDropdown.innerHTML = '<div class="dropdown-item" style="color: rgba(255,255,255,0.5); cursor: default;">Tidak ada hasil ditemukan</div>';
            }
        }

        function showAllSidebarItems() {
            // Show all dropdown items
            const allDropdownItems = document.querySelectorAll('.dropdown-item');
            allDropdownItems.forEach(item => {
                item.style.display = 'block';
            });

            // Show all dropdown contents
            const allDropdownContents = document.querySelectorAll('.dropdown-content');
            allDropdownContents.forEach(content => {
                content.style.display = 'block';
            });

            // Restore original CCTV dropdown content if it was modified
            const cctvDropdown = document.getElementById('cctvDropdown');
            if (cctvDropdown.innerHTML.includes('Tidak ada hasil ditemukan')) {
                location.reload(); // Simple way to restore original content
            }
        }

        function autoExpandDropdowns(searchTerm) {
            // Check if search matches region
            const regions = ['bantul', 'gunung kidul', 'kulonprogo', 'sleman', 'jogja'];
            const schoolTypes = ['sma', 'smk'];
            
            // Expand main CCTV dropdown
            const cctvDropdown = document.getElementById('cctvDropdown');
            const cctvChevron = document.getElementById('cctvChevron');
            if (!cctvDropdown.classList.contains('active')) {
                cctvDropdown.classList.add('active');
                cctvChevron.style.transform = 'rotate(180deg)';
            }
            
            // Check for region matches
            regions.forEach(region => {
                if (region.includes(searchTerm) || searchTerm.includes(region.split(' ')[0])) {
                    const regionDropdown = document.getElementById(region.replace(' ', '') + 'Dropdown');
                    if (regionDropdown && !regionDropdown.classList.contains('active')) {
                        regionDropdown.classList.add('active');
                    }
                }
            });
            
            // Check for school type matches
            schoolTypes.forEach(type => {
                if (type.includes(searchTerm)) {
                    // Expand all region dropdowns that have this school type
                    regions.forEach(region => {
                        const regionDropdown = document.getElementById(region.replace(' ', '') + 'Dropdown');
                        const schoolTypeDropdown = document.getElementById(region.replace(' ', '') + type.toUpperCase());
                        if (regionDropdown && !regionDropdown.classList.contains('active')) {
                            regionDropdown.classList.add('active');
                        }
                        if (schoolTypeDropdown && !schoolTypeDropdown.classList.contains('active')) {
                            schoolTypeDropdown.classList.add('active');
                        }
                    });
                }
            });
            
            // Check for specific school matches
            cctvData.forEach(data => {
                if (data.school.toLowerCase().includes(searchTerm) || data.location.toLowerCase().includes(searchTerm)) {
                    const region = data.region.toLowerCase().replace(' ', '');
                    const regionDropdown = document.getElementById(region + 'Dropdown');
                    
                    if (regionDropdown && !regionDropdown.classList.contains('active')) {
                        regionDropdown.classList.add('active');
                    }
                    
                    // Determine school type and expand accordingly
                    const schoolType = data.school.includes('SMA') ? 'SMA' : 'SMK';
                    const schoolTypeDropdown = document.getElementById(region + schoolType);
                    if (schoolTypeDropdown && !schoolTypeDropdown.classList.contains('active')) {
                        schoolTypeDropdown.classList.add('active');
                    }
                    
                    // Expand specific school dropdown
                    const schoolName = data.school.toLowerCase().replace(/\s+/g, '').replace(/\./g, '');
                    const schoolDropdown = document.getElementById(schoolName.replace('sman', 'sma').replace('smkn', 'smk') + region);
                    if (schoolDropdown && !schoolDropdown.classList.contains('active')) {
                        schoolDropdown.classList.add('active');
                    }
                }
            });
        }

        function collapseAllDropdowns() {
            const dropdowns = document.querySelectorAll('.dropdown-content');
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
            
            const chevrons = document.querySelectorAll('[id$="Chevron"]');
            chevrons.forEach(chevron => {
                chevron.style.transform = 'rotate(0deg)';
            });
        }

        // Update time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            document.getElementById('currentTime').textContent = timeString;
        }

(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9631325b71e96cf2',t:'MTc1MzE2OTA0OS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();

