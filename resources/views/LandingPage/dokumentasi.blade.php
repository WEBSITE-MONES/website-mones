<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Dokumentasi Proyek - P-Mones</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Leaflet for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/dokumentasi.css" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="index.html" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">P-Mones</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="pelaporan.html">Pelaporan</a></li>
                    <li><a href="#services">Gambar</a></li>
                    <li><a href="dokumentasi.html" class="active">Dokumentasi</a></li>
                    <li><a href="#team">Korespondensi</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="index.html">Kembali</a>

        </div>
    </header>

    <!-- Main Content -->
    <main class="main">

        <!-- Page Title -->
        <div class="container" style="padding-top: 100px;">
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
                                <div class="display-4 fw-bold" id="totalPhotos">248</div>
                            </div>
                            <i class="bi bi-camera" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card green">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="opacity: 0.8; font-size: 14px;">Lokasi Unik</div>
                                <div class="display-4 fw-bold" id="uniqueLocations">12</div>
                            </div>
                            <i class="bi bi-geo-alt" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card purple">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="opacity: 0.8; font-size: 14px;">Proyek Aktif</div>
                                <div class="display-4 fw-bold" id="activeProjects">5</div>
                            </div>
                            <i class="bi bi-folder" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stats-card orange">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div style="opacity: 0.8; font-size: 14px;">Update Terakhir</div>
                                <div class="h4 fw-bold mt-2" id="lastUpdate">Hari ini</div>
                            </div>
                            <i class="bi bi-clock" style="font-size: 48px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs & Filters -->
        <div class="container mb-4">
            <div class="tab-navigation">
                <!-- View Tabs -->
                <div class="d-flex gap-4 border-bottom pb-3 mb-3">
                    <button class="tab-btn active" id="tabTimeline" onclick="switchTab('timeline')">
                        <i class="bi bi-calendar-event"></i> Timeline
                    </button>
                    <button class="tab-btn" id="tabMap" onclick="switchTab('map')">
                        <i class="bi bi-map"></i> Peta
                    </button>
                    <button class="tab-btn" id="tabGrid" onclick="switchTab('grid')">
                        <i class="bi bi-grid-3x3"></i> Gallery
                    </button>
                </div>

                <!-- Filters -->
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <span class="text-muted me-2 fw-semibold">Filter:</span>
                        <span class="filter-chip active" data-filter="all">
                            <i class="bi bi-check-circle"></i> Semua
                        </span>
                        <span class="filter-chip" data-filter="revitalisasi">
                            🏗️ Revitalisasi Pelabuhan
                        </span>
                        <span class="filter-chip" data-filter="dermaga">
                            ⚓ Dermaga Baru
                        </span>
                        <span class="filter-chip" data-filter="gudang">
                            📦 Gudang Logistik
                        </span>
                    </div>

                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <input type="date" class="form-control form-control-sm" id="filterDate"
                            style="max-width: 150px;">
                        <button class="btn btn-primary btn-sm">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="container mb-5">

            <!-- Timeline View -->
            <div id="viewTimeline">

                <!-- Day Group -->
                <div class="day-group">
                    <div class="day-header">
                        <div class="date-badge">
                            <div class="day">28</div>
                            <div class="month">OKT</div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Selasa, 28 Oktober 2025</h3>
                            <p class="text-muted mb-0">6 foto dokumentasi</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Photo Card 1 -->
                        <div class="col-md-3">
                            <div class="photo-card" onclick="openLightbox(0)">
                                <div style="position: relative;">
                                    <img src="https://images.unsplash.com/photo-1581094271901-8022df4466f9?w=400"
                                        alt="Dokumentasi">
                                    <div class="weather-badge">
                                        <span>28°C</span>
                                        <span>☀️</span>
                                    </div>
                                    <div class="gps-badge">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>GPS</span>
                                    </div>
                                </div>
                                <div class="photo-card-body">
                                    <p class="mb-1 fw-semibold">Pemasangan Fender</p>
                                    <p class="text-muted mb-0" style="font-size: 12px;">08:30 WIB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Photo Card 2 -->
                        <div class="col-md-3">
                            <div class="photo-card" onclick="openLightbox(1)">
                                <div style="position: relative;">
                                    <img src="https://images.unsplash.com/photo-1590856029826-c7a73142bbf1?w=400"
                                        alt="Dokumentasi">
                                    <div class="weather-badge">
                                        <span>29°C</span>
                                        <span>⛅</span>
                                    </div>
                                    <div class="gps-badge">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>GPS</span>
                                    </div>
                                </div>
                                <div class="photo-card-body">
                                    <p class="mb-1 fw-semibold">Progress Pengecoran</p>
                                    <p class="text-muted mb-0" style="font-size: 12px;">10:15 WIB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Photo Card 3 -->
                        <div class="col-md-3">
                            <div class="photo-card" onclick="openLightbox(2)">
                                <div style="position: relative;">
                                    <img src="https://images.unsplash.com/photo-1597008641621-cefdcf718025?w=400"
                                        alt="Dokumentasi">
                                    <div class="weather-badge">
                                        <span>27°C</span>
                                        <span>🌧️</span>
                                    </div>
                                    <div class="gps-badge">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>GPS</span>
                                    </div>
                                </div>
                                <div class="photo-card-body">
                                    <p class="mb-1 fw-semibold">Instalasi Crane</p>
                                    <p class="text-muted mb-0" style="font-size: 12px;">14:45 WIB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Photo Card 4 -->
                        <div class="col-md-3">
                            <div class="photo-card" onclick="openLightbox(3)">
                                <div style="position: relative;">
                                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400"
                                        alt="Dokumentasi">
                                    <div class="weather-badge">
                                        <span>26°C</span>
                                        <span>⛅</span>
                                    </div>
                                    <div class="gps-badge">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>GPS</span>
                                    </div>
                                </div>
                                <div class="photo-card-body">
                                    <p class="mb-1 fw-semibold">Quality Check</p>
                                    <p class="text-muted mb-0" style="font-size: 12px;">16:20 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Day Group 2 -->
                <div class="day-group">
                    <div class="day-header">
                        <div class="date-badge" style="background: #6c757d;">
                            <div class="day">27</div>
                            <div class="month">OKT</div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Senin, 27 Oktober 2025</h3>
                            <p class="text-muted mb-0">8 foto dokumentasi</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="photo-card" onclick="openLightbox(4)">
                                <div style="position: relative;">
                                    <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=400"
                                        alt="Dokumentasi">
                                    <div class="weather-badge">
                                        <span>30°C</span>
                                        <span>☀️</span>
                                    </div>
                                    <div class="gps-badge">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <span>GPS</span>
                                    </div>
                                </div>
                                <div class="photo-card-body">
                                    <p class="mb-1 fw-semibold">Struktur Beton</p>
                                    <p class="text-muted mb-0" style="font-size: 12px;">09:00 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Map View -->
            <div id="viewMap" style="display: none;">
                <div class="day-group">
                    <div class="mb-4">
                        <h3 class="fw-bold mb-2">📍 Peta Lokasi Dokumentasi</h3>
                        <p class="text-muted">Klik marker untuk melihat foto di lokasi tersebut</p>
                    </div>
                    <div id="map"></div>

                    <!-- Location List -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <div class="location-card">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px; flex-shrink: 0;">
                                        <i class="bi bi-geo-alt-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">Pelabuhan Makassar</h5>
                                        <p class="text-muted mb-1" style="font-size: 14px;">-5.1477, 119.4327</p>
                                        <p class="text-muted mb-0" style="font-size: 13px;">42 foto • Revitalisasi
                                            Pelabuhan</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="location-card">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px; flex-shrink: 0;">
                                        <i class="bi bi-geo-alt-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">Dermaga Baru</h5>
                                        <p class="text-muted mb-1" style="font-size: 14px;">-5.1456, 119.4310</p>
                                        <p class="text-muted mb-0" style="font-size: 13px;">28 foto • Pembangunan
                                            Dermaga</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid View -->
            <div id="viewGrid" style="display: none;">
                <div class="day-group">
                    <div class="row g-3">
                        <!-- Compact grid layout -->
                        <div class="col-6 col-md-4 col-lg-2">
                            <div class="photo-card" onclick="openLightbox(0)">
                                <img src="https://images.unsplash.com/photo-1581094271901-8022df4466f9?w=300" alt="Dok"
                                    style="height: 150px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer id="footer" class="footer">
        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="index.html" class="d-flex align-items-center">
                        <span class="sitename">PT Pelabuhan Indonesia (Persero)</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>Portaverse, sebagai People Development Super Apps, mengintegrasikan
                            pengelolaan aset intelektual, pembelajaran, dan manajemen talenta untuk
                            mendukung perjalanan PT Pelabuhan Indonesia (Persero) mencapai visi
                            sebagai pemimpin global dalam ekosistem maritim.</p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bi bi-chevron-right"></i> <a href="index.html">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="pelaporan.html">Pelaporan</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="ringkasan.html">Ringkasan</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="dokumentasi.html">Dokumentasi</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Korespondensi</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Kantor Pusat</h4>
                    <div class="footer-contact pt-1">
                        <p>PT Pelabuhan Indonesia</p>
                        <p>Jl. Pasoso No.1, Tanjung Priok, Jakarta Utara,</p>
                        <p class="mt-3"><strong>14310</strong> <span>Indonesia</span></p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <h4>Follow Us</h4>
                    <p>Sosial Media</p>
                    <div class="social-links d-flex">
                        <a href=""><i class="bi bi-twitter-x"></i></a>
                        <a href=""><i class="bi bi-facebook"></i></a>
                        <a href=""><i class="bi bi-instagram"></i></a>
                        <a href=""><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">PT. Pelabuhan Indonesia (Persero)</strong>
                <span>All Rights Reserved</span></p>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <button onclick="closeLightbox()" class="lightbox-close">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="" class="lightbox-image">

            <div class="lightbox-info">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="fw-bold mb-1" id="lightboxTitle">Pemasangan Fender</h3>
                        <p class="mb-0" style="opacity: 0.8;" id="lightboxDate">Selasa, 28 Oktober 2025 • 08:30 WIB</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light btn-sm">
                            <i class="bi bi-share"></i> Share
                        </button>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <p style="opacity: 0.7; margin-bottom: 5px;">📍 Lokasi GPS</p>
                        <p class="fw-semibold mb-0">-5.1477, 119.4327</p>
                        <p style="font-size: 12px; opacity: 0.6;">Akurasi: ±8m</p>
                    </div>
                    <div class="col-md-4">
                        <p style="opacity: 0.7; margin-bottom: 5px;">🌤️ Cuaca</p>
                        <p class="fw-semibold mb-0">28°C - Cerah ☀️</p>
                        <p style="font-size: 12px; opacity: 0.6;">Kelembaban: 75%</p>
                    </div>
                    <div class="col-md-4">
                        <p style="opacity: 0.7; margin-bottom: 5px;">🏗️ Proyek</p>
                        <p class="fw-semibold mb-0">Revitalisasi Pelabuhan</p>
                        <p style="font-size: 12px; opacity: 0.6;">PT. Cipta Rancang</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button onclick="prevPhoto()" class="lightbox-nav prev">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button onclick="nextPhoto()" class="lightbox-nav next">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/dokumentasi.js"></script>

    <script>
    // Sample data
    const photos = [{
            id: 1,
            url: 'https://images.unsplash.com/photo-1581094271901-8022df4466f9?w=800',
            title: 'Pemasangan Fender',
            date: '2025-10-28',
            time: '08:30',
            gps: {
                lat: -5.1477,
                lon: 119.4327,
                accuracy: 8
            },
            weather: {
                temp: 28,
                desc: 'Cerah',
                icon: '☀️',
                humidity: 75
            },
            project: 'Revitalisasi Pelabuhan'
        },
        {
            id: 2,
            url: 'https://images.unsplash.com/photo-1590856029826-c7a73142bbf1?w=800',
            title: 'Progress Pengecoran',
            date: '2025-10-28',
            time: '10:15',
            gps: {
                lat: -5.1480,
                lon: 119.4330,
                accuracy: 12
            },
            weather: {
                temp: 29,
                desc: 'Berawan',
                icon: '⛅',
                humidity: 70
            },
            project: 'Revitalisasi Pelabuhan'
        },
        {
            id: 3,
            url: 'https://images.unsplash.com/photo-1597008641621-cefdcf718025?w=800',
            title: 'Instalasi Crane',
            date: '2025-10-28',
            time: '14:45',
            gps: {
                lat: -5.1456,
                lon: 119.4310,
                accuracy: 10
            },
            weather: {
                temp: 27,
                desc: 'Hujan Ringan',
                icon: '🌧️',
                humidity: 85
            },
            project: 'Dermaga Baru'
        },
        {
            id: 4,
            url: 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800',
            title: 'Quality Check',
            date: '2025-10-28',
            time: '16:20',
            gps: {
                lat: -5.1465,
                lon: 119.4315,
                accuracy: 9
            },
            weather: {
                temp: 26,
                desc: 'Berawan',
                icon: '⛅',
                humidity: 78
            },
            project: 'Revitalisasi Pelabuhan'
        },
        {
            id: 5,
            url: 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=800',
            title: 'Struktur Beton',
            date: '2025-10-27',
            time: '09:00',
            gps: {
                lat: -5.1470,
                lon: 119.4320,
                accuracy: 11
            },
            weather: {
                temp: 30,
                desc: 'Cerah',
                icon: '☀️',
                humidity: 68
            },
            project: 'Dermaga Baru'
        }
    ];

    let currentPhotoIndex = 0;
    let map = null;

    // Initialize Map
    function initMap() {
        if (!map) {
            map = L.map('map').setView([-5.1477, 119.4327], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add markers
            photos.forEach((photo, index) => {
                const marker = L.marker([photo.gps.lat, photo.gps.lon]).addTo(map);
                marker.bindPopup(`
            <div style="text-align: center;">
              <img src="${photo.url}" style="width:200px;height:120px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
              <strong>${photo.title}</strong><br>
              <small>${photo.weather.temp}°C ${photo.weather.icon}</small>
            </div>
          `);
                marker.on('click', () => openLightbox(index));
            });
        }
    }

    // Tab Switching
    function switchTab(tab) {
        // Reset all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Hide all views
        document.getElementById('viewTimeline').style.display = 'none';
        document.getElementById('viewMap').style.display = 'none';
        document.getElementById('viewGrid').style.display = 'none';

        // Show selected
        if (tab === 'timeline') {
            document.getElementById('tabTimeline').classList.add('active');
            document.getElementById('viewTimeline').style.display = 'block';
        } else if (tab === 'map') {
            document.getElementById('tabMap').classList.add('active');
            document.getElementById('viewMap').style.display = 'block';
            setTimeout(() => {
                initMap();
                if (map) map.invalidateSize();
            }, 100);
        } else if (tab === 'grid') {
            document.getElementById('tabGrid').classList.add('active');
            document.getElementById('viewGrid').style.display = 'block';
        }
    }

    // Filter chips
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Lightbox Functions
    function openLightbox(index) {
        currentPhotoIndex = index;
        updateLightbox();
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function updateLightbox() {
        const photo = photos[currentPhotoIndex];
        document.getElementById('lightboxImage').src = photo.url;
        document.getElementById('lightboxTitle').textContent = photo.title;

        const dateObj = new Date(photo.date);
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
            'Oktober', 'November', 'Desember'
        ];
        const dayName = days[dateObj.getDay()];
        const dateStr = `${dayName}, ${dateObj.getDate()} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;

        document.getElementById('lightboxDate').textContent = `${dateStr} • ${photo.time} WIB`;

        // Update GPS, Weather, Project info
        const infoHTML = `
        <div class="row g-4">
          <div class="col-md-4">
            <p style="opacity: 0.7; margin-bottom: 5px;">📍 Lokasi GPS</p>
            <p class="fw-semibold mb-0">${photo.gps.lat.toFixed(4)}, ${photo.gps.lon.toFixed(4)}</p>
            <p style="font-size: 12px; opacity: 0.6;">Akurasi: ±${photo.gps.accuracy}m</p>
          </div>
          <div class="col-md-4">
            <p style="opacity: 0.7; margin-bottom: 5px;">🌤️ Cuaca</p>
            <p class="fw-semibold mb-0">${photo.weather.temp}°C - ${photo.weather.desc} ${photo.weather.icon}</p>
            <p style="font-size: 12px; opacity: 0.6;">Kelembaban: ${photo.weather.humidity}%</p>
          </div>
          <div class="col-md-4">
            <p style="opacity: 0.7; margin-bottom: 5px;">🏗️ Proyek</p>
            <p class="fw-semibold mb-0">${photo.project}</p>
            <p style="font-size: 12px; opacity: 0.6;">PT. Cipta Rancang</p>
          </div>
        </div>
      `;

        // Update the info section
        const lightboxInfo = document.querySelector('.lightbox-info');
        const existingInfo = lightboxInfo.querySelector('.row');
        if (existingInfo) {
            existingInfo.outerHTML = infoHTML;
        }
    }

    function prevPhoto() {
        currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
        updateLightbox();
    }

    function nextPhoto() {
        currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
        updateLightbox();
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevPhoto();
            if (e.key === 'ArrowRight') nextPhoto();
        }
    });

    // Close lightbox on background click
    document.getElementById('lightbox').addEventListener('click', (e) => {
        if (e.target.id === 'lightbox') closeLightbox();
    });

    // Mobile nav toggle (if exists in main.js)
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📸 Dokumentasi Page Ready!');
    });
    </script>

</body>