<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dokumentasi Proyek - P-Mones</title>

    <!-- Vendor CSS Files -->
    <link href="/LandingPage/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/LandingPage/assets/css/main.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&family=Jost:wght@400;600;700&display=swap"
        rel="stylesheet">
    </style>
    <!-- Leaflet for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="/LandingPage/assets/css/main.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
    .user-menu-btn {
        display: flex;
        align-items: center;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        padding: 8px 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .user-menu-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .dropdown-menu-custom {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        min-width: 280px;
        z-index: 1000;
        animation: slideDown 0.3s ease;
    }

    .dropdown-menu-custom.show {
        display: block;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header {
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px 12px 0 0;
        color: #fff;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar {
        font-size: 50px;
        line-height: 1;
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 4px;
    }

    .user-email {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 6px;
    }

    .badge-role {
        background: rgba(255, 255, 255, 0.2);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .dropdown-divider {
        height: 1px;
        background: #eee;
        margin: 8px 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 14px;
    }

    .dropdown-item i {
        font-size: 18px;
        width: 20px;
        text-align: center;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
    }

    .logout-btn {
        color: #dc3545;
    }

    .logout-btn:hover {
        background: #fff5f5;
    }

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

    #map {
        height: 450px;
        border-radius: 15px;
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
            {{-- User sudah login - Tampilkan nama & dropdown --}}
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

                    <a href="{{ route('account.index') }}" class="dropdown-item">
                        <i class="bi bi-person"></i>
                        <span>Profile Saya</span>
                    </a>

                    <a href="{{ route('account.setting') }}" class="dropdown-item">
                        <i class="bi bi-gear"></i>
                        <span>Pengaturan</span>
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
            {{-- User belum login - Tampilkan tombol login --}}
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

        <!-- Stats Cards -->
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

        <!-- Filters -->
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

        <!-- Loading -->
        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat dokumentasi...</p>
        </div>

        <!-- Photos Grid -->
        <div class="container mb-5" id="photosContainer">
            <div class="row g-3" id="photosGrid">
                <!-- Photos will be rendered here -->
            </div>
        </div>
    </main>

    <!-- Lightbox -->
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

    <script>
    // Toggle dropdown
    document.getElementById('userMenuBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('userDropdownMenu');
        dropdown.classList.toggle('show');
    });

    // Close dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('userDropdownMenu');
        const button = document.getElementById('userMenuBtn');

        if (!button.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Close dropdown saat klik item menu
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            document.getElementById('userDropdownMenu').classList.remove('show');
        });
    });
    </script>

    <script>
    let allPhotos = [];
    let currentPhotoIndex = 0;

    // Load photos on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📸 Dokumentasi page loaded');
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

            console.log('🔍 Fetching from:', url);

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('📥 Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('📦 Result:', result);

            if (result.success) {
                allPhotos = result.data.photos;
                console.log('✅ Photos loaded:', allPhotos.length);

                // Update stats
                document.getElementById('totalPhotos').textContent = result.data.stats.total_photos;
                document.getElementById('uniqueLocations').textContent = result.data.stats.unique_locations;
                document.getElementById('activeProjects').textContent = result.data.stats.active_projects;
                document.getElementById('lastUpdate').textContent = result.data.stats.last_update;

                // Render photos
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
            return;
        }

        photos.forEach((photo, index) => {
            const photoCard = `
            <div class="col-md-3">
                <div class="photo-card" onclick="openLightbox(${index})">
                    <div style="position: relative;">
                        <img src="${photo.url}" alt="${photo.title}" onerror="this.src='https://via.placeholder.com/400x200?text=Image+Error'">
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
    }

    function applyFilters() {
        const filters = {
            pekerjaan_id: document.getElementById('filterPekerjaan').value,
            tanggal_mulai: document.getElementById('filterTanggalMulai').value,
            tanggal_akhir: document.getElementById('filterTanggalAkhir').value
        };

        console.log('🔍 Applying filters:', filters);
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

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (document.getElementById('lightbox').classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevPhoto();
            if (e.key === 'ArrowRight') nextPhoto();
        }
    });

    // Touch swipe for mobile
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
                nextPhoto(); // Swipe left
            } else {
                prevPhoto(); // Swipe right
            }
        }
    }
    </script>
</body>

</html>