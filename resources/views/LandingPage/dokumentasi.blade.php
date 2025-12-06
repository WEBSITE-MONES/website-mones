<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dokumentasi Proyek - P-Mones</title>

    <link href="/LandingPage/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/LandingPage/assets/css/main.css" rel="stylesheet">

    <!-- User Dropdown CSS -->
    <link href="/LandingPage/assets/css/user-dropdown.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&family=Jost:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
    /* ✅ HANYA STYLE UNTUK PAGE-SPECIFIC (BUKAN USER DROPDOWN) */
    :root {
        --primary-color: #1d6ba8;
        --secondary-color: #2b7ab5;
    }

    body {
        padding-top: 80px;
    }

    .stats-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .photo-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 20px;
    }

    .photo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .photo-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .weather-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .gps-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(13, 110, 253, 0.9);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
    }

    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 10000;
    }

    .lightbox.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-image {
        max-width: 90%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 12px;
    }

    .lightbox-nav {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 32px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        z-index: 10001;
    }

    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-50%) scale(1.1);
    }

    .lightbox-nav.prev {
        left: 20px;
    }

    .lightbox-nav.next {
        right: 20px;
    }

    .lightbox-close {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10001;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 32px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .lightbox-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .photo-checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 24px;
        height: 24px;
        cursor: pointer;
        z-index: 10;
        accent-color: #1d6ba8;
    }

    .photo-card.selected {
        border: 3px solid #1d6ba8;
        box-shadow: 0 0 15px rgba(29, 107, 168, 0.5);
    }
    </style>
</head>

<body>
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">
            <a href="{{ route('landingpage.index') }}" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">P-Mones</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ route('landingpage.index') }}">Home</a></li>
                    <li><a href="{{ route('landingpage.index.pelaporan') }}">Pelaporan</a></li>
                    <li><a href="{{ route('landingpage.index.dokumentasi') }}" class="active">Dokumentasi</a></li>
                    <li><a href="{{ route('landingpage.monitoring.progress') }}">Monitoring</a></li>
                    <li><a href="#">Gambar</a></li>
                </ul>
            </nav>

            @auth
            <div class="user-dropdown" style="position: relative;">
                <button class="btn-getstarted user-menu-btn" id="userMenuBtn" type="button">
                    <i class="bi bi-person-circle" style="font-size: 18px; margin-right: 8px;"></i>
                    {{ Auth::user()->name }}
                    <i class="bi bi-chevron-down" style="font-size: 12px; margin-left: 5px;"></i>
                </button>

                <div id="userDropdownMenu" class="dropdown-menu-custom">
                    <div class="dropdown-header">
                        <div class="user-info">
                            <div class="user-avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div class="user-details">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-email">{{ Auth::user()->email }}</div>
                                <div class="user-role">
                                    <span class="badge-role">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-divider"></div>

                    <a href="{{ route('landingpage.profile') }}" class="dropdown-item">
                        <i class="bi bi-person"></i>
                        <span>Profile Saya</span>
                    </a>

                    <a href="{{ route('landingpage.profile.password') }}" class="dropdown-item">
                        <i class="bi bi-key"></i>
                        <span>Ubah Password</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>

            @else
            <a class="btn-getstarted" href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right" style="margin-right: 5px;"></i>
                Login
            </a>
            @endauth
        </div>
    </header>

    <main class="main">
        <div class="container" style="padding-top: 20px;">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="display-5 fw-bold text-primary">
                        <i class="bi bi-camera"></i> Dokumentasi Proyek
                    </h1>
                    <p class="text-muted">Galeri foto dokumentasi progress dengan GPS & weather tracking</p>
                </div>
            </div>
        </div>

        <div class="container mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="opacity: 0.8; font-size: 14px;">Total Foto</div>
                                <div class="display-4 fw-bold" id="totalPhotos">0</div>
                            </div>
                            <i class="bi bi-camera" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="opacity: 0.8; font-size: 14px;">Lokasi Unik</div>
                                <div class="display-4 fw-bold" id="uniqueLocations">0</div>
                            </div>
                            <i class="bi bi-geo-alt" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="opacity: 0.8; font-size: 14px;">Proyek Aktif</div>
                                <div class="display-4 fw-bold" id="activeProjects">0</div>
                            </div>
                            <i class="bi bi-folder" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card" style="background: linear-gradient(135deg, #fd7e14 0%, #ff922b 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="opacity: 0.8; font-size: 14px;">Update Terakhir</div>
                                <div class="h5 fw-bold mt-2" id="lastUpdate">-</div>
                            </div>
                            <i class="bi bi-clock" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter Pekerjaan</label>
                            <select class="form-select" id="filterPekerjaan">
                                <option value="">Semua Pekerjaan</option>
                                @foreach($pekerjaans as $pekerjaan)
                                <option value="{{ $pekerjaan->id }}">{{ $pekerjaan->nama_investasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="filterTanggalMulai">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="filterTanggalAkhir">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mb-3" id="exportControls" style="display: none;">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3">
                                <button class="btn btn-outline-primary" onclick="selectAllPhotos()">
                                    <i class="bi bi-check-all"></i> Pilih Semua
                                </button>
                                <button class="btn btn-outline-secondary" onclick="deselectAllPhotos()">
                                    <i class="bi bi-x-circle"></i> Batal Pilih
                                </button>
                                <span class="text-muted fw-semibold" id="selectedCount">0 foto dipilih</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-danger" onclick="exportToPDF()" id="exportBtn" disabled>
                                <i class="bi bi-file-pdf"></i> Export ke PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat dokumentasi...</p>
        </div>

        <div class="container mb-5" id="photosContainer">
            <div class="row g-3" id="photosGrid"></div>
        </div>
    </main>

    <div id="lightbox" class="lightbox">
        <button onclick="closeLightbox()" class="lightbox-close">
            <i class="bi bi-x-lg"></i>
        </button>

        <button onclick="prevPhoto()" class="lightbox-nav prev">
            <i class="bi bi-chevron-left"></i>
        </button>

        <button onclick="nextPhoto()" class="lightbox-nav next">
            <i class="bi bi-chevron-right"></i>
        </button>

        <img id="lightboxImage" src="" alt="" class="lightbox-image">

        <div
            style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.8); color: white; padding: 20px; border-radius: 12px; max-width: 800px; width: 90%; z-index: 10002;">
            <h4 id="lightboxTitle" class="mb-2"></h4>
            <p id="lightboxInfo" class="mb-0" style="line-height: 1.6;"></p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/LandingPage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ User Dropdown JS - MUST BE LOADED BEFORE page-specific scripts -->
    <script src="/LandingPage/assets/js/user-dropdown.js"></script>

    <!-- Page-specific scripts -->
    <script src="/LandingPage/assets/js/main.js"></script>

    <script>
    // ✅ HANYA SCRIPT UNTUK PHOTO GALLERY (BUKAN USER DROPDOWN)
    let allPhotos = [];
    let currentPhotoIndex = 0;
    let selectedPhotoIds = new Set();

    document.addEventListener('DOMContentLoaded', function() {
        loadPhotos();
    });

    async function loadPhotos(filters = {}) {
        const loadingSpinner = document.getElementById('loadingSpinner');
        const photosGrid = document.getElementById('photosGrid');

        loadingSpinner.style.display = 'block';
        photosGrid.innerHTML = '';

        try {
            const params = new URLSearchParams(filters);
            const url = `/landingpage/api/dokumentasi?${params}`;

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                allPhotos = result.data.photos;

                document.getElementById('totalPhotos').textContent = result.data.stats.total_photos;
                document.getElementById('uniqueLocations').textContent = result.data.stats.unique_locations;
                document.getElementById('activeProjects').textContent = result.data.stats.active_projects;
                document.getElementById('lastUpdate').textContent = result.data.stats.last_update;

                renderPhotos(allPhotos);
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Error loading photos:', error);
            photosGrid.innerHTML = `
            <div class="col-12 text-center text-danger py-5">
                <i class="bi bi-exclamation-triangle" style="font-size: 64px;"></i>
                <p class="mt-3">Gagal memuat foto dokumentasi</p>
                <p class="text-muted">${error.message}</p>
                <button class="btn btn-primary mt-3" onclick="loadPhotos()">
                    <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                </button>
            </div>
        `;
        } finally {
            loadingSpinner.style.display = 'none';
        }
    }

    function renderPhotos(photos) {
        const grid = document.getElementById('photosGrid');
        grid.innerHTML = '';

        if (photos.length === 0) {
            grid.innerHTML = `
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-camera" style="font-size: 64px;"></i>
                <p class="mt-3">Belum ada dokumentasi foto</p>
                <p class="text-muted">Upload foto melalui halaman Pelaporan</p>
            </div>
        `;
            document.getElementById('exportControls').style.display = 'none';
            return;
        }

        document.getElementById('exportControls').style.display = 'block';

        photos.forEach((photo, index) => {
            const isSelected = selectedPhotoIds.has(photo.id);
            const photoCard = `
            <div class="col-md-3">
                <div class="photo-card ${isSelected ? 'selected' : ''}" id="photo-${photo.id}">
                    <div style="position: relative;">
                        <input type="checkbox" 
                               class="photo-checkbox" 
                               id="checkbox-${photo.id}"
                               ${isSelected ? 'checked' : ''}
                               onclick="togglePhotoSelection(${photo.id}, event)">
                        <img src="${photo.url}" 
                             alt="${photo.title}" 
                             onclick="openLightbox(${index})"
                             onerror="this.src='https://via.placeholder.com/400x200?text=Image+Error'">
                        <div class="weather-badge">
                            <span>${photo.weather.temp}°C</span>
                            <span>${photo.weather.icon}</span>
                        </div>
                        <div class="gps-badge">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span>GPS</span>
                        </div>
                    </div>
                    <div class="photo-card-body p-3">
                        <p class="mb-1 fw-semibold">${photo.title}</p>
                        <p class="text-muted mb-0" style="font-size: 12px;">
                            ${photo.date} ${photo.time} WIB
                        </p>
                    </div>
                </div>
            </div>
        `;
            grid.insertAdjacentHTML('beforeend', photoCard);
        });

        updateSelectedCount();
    }

    function applyFilters() {
        const filters = {
            pekerjaan_id: document.getElementById('filterPekerjaan').value,
            tanggal_mulai: document.getElementById('filterTanggalMulai').value,
            tanggal_akhir: document.getElementById('filterTanggalAkhir').value
        };

        loadPhotos(filters);
    }

    function openLightbox(index) {
        currentPhotoIndex = index;
        const photo = allPhotos[index];

        if (!photo) {
            console.error('Photo not found at index:', index);
            return;
        }

        document.getElementById('lightboxImage').src = photo.url;
        document.getElementById('lightboxTitle').textContent = photo.title;
        document.getElementById('lightboxInfo').innerHTML = `
        📍 ${photo.location_name}<br>
        🏗️ ${photo.projectName}<br>
        🌤️ ${photo.weather.temp}°C - ${photo.weather.desc} (Kelembaban: ${photo.weather.humidity}%)<br>
        📅 ${photo.date} ${photo.time} WIB<br>
        👷 ${photo.pelapor}
    `;

        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function nextPhoto() {
        currentPhotoIndex = (currentPhotoIndex + 1) % allPhotos.length;
        openLightbox(currentPhotoIndex);
    }

    function prevPhoto() {
        currentPhotoIndex = (currentPhotoIndex - 1 + allPhotos.length) % allPhotos.length;
        openLightbox(currentPhotoIndex);
    }

    document.addEventListener('keydown', (e) => {
        if (document.getElementById('lightbox').classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevPhoto();
            if (e.key === 'ArrowRight') nextPhoto();
        }
    });

    let touchStartX = 0;
    let touchEndX = 0;

    document.getElementById('lightbox')?.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.getElementById('lightbox')?.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextPhoto();
            } else {
                prevPhoto();
            }
        }
    }

    function togglePhotoSelection(photoId, event) {
        event.stopPropagation();

        const checkbox = document.getElementById(`checkbox-${photoId}`);
        const photoCard = document.getElementById(`photo-${photoId}`);

        if (checkbox.checked) {
            selectedPhotoIds.add(photoId);
            photoCard.classList.add('selected');
        } else {
            selectedPhotoIds.delete(photoId);
            photoCard.classList.remove('selected');
        }

        updateSelectedCount();
    }

    function selectAllPhotos() {
        selectedPhotoIds.clear();
        allPhotos.forEach(photo => {
            selectedPhotoIds.add(photo.id);
        });

        document.querySelectorAll('.photo-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
        document.querySelectorAll('.photo-card').forEach(card => {
            card.classList.add('selected');
        });

        updateSelectedCount();
    }

    function deselectAllPhotos() {
        selectedPhotoIds.clear();

        document.querySelectorAll('.photo-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        document.querySelectorAll('.photo-card').forEach(card => {
            card.classList.remove('selected');
        });

        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = selectedPhotoIds.size;
        document.getElementById('selectedCount').textContent = `${count} foto dipilih`;
        document.getElementById('exportBtn').disabled = count === 0;
    }

    async function exportToPDF() {
        if (selectedPhotoIds.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Foto',
                text: 'Silakan pilih minimal 1 foto untuk diexport'
            });
            return;
        }

        Swal.fire({
            title: 'Membuat PDF...',
            html: `Memproses ${selectedPhotoIds.size} foto`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const formData = new FormData();

            const photoIdsArray = Array.from(selectedPhotoIds);
            photoIdsArray.forEach(id => {
                formData.append('photo_ids[]', id);
            });

            const pekerjaanId = document.getElementById('filterPekerjaan').value;
            if (pekerjaanId) {
                formData.append('pekerjaan_id', pekerjaanId);
            }

            formData.append('judul', 'Dokumentasi Proyek P-Mones');

            const response = await fetch('/landingpage/api/dokumentasi/export-pdf', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/pdf'
                },
                body: formData
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Gagal export PDF');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Dokumentasi_${new Date().getTime()}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `PDF dengan ${selectedPhotoIds.size} foto berhasil didownload`,
                timer: 2000
            });

            deselectAllPhotos();

        } catch (error) {
            console.error('Error exporting PDF:', error);
            Swal.fire({
                icon: 'error',
                title: 'Export Gagal',
                text: error.message || 'Terjadi kesalahan saat membuat PDF'
            });
        }
    }
    </script>
</body>

</html>