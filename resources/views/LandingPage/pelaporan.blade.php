<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Ringkasan Progres Harian - P-Mones</title>
    <meta name="description" content="Ringkasan Laporan Progress Harian Proyek - P-Mones">
    <meta name="keywords" content="summary, progress, harian, p-mones, pelabuhan">

    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link href="assets/css/main.css" rel="stylesheet">
    <link href="assets/css/pelaporan.css" rel="stylesheet">
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
        border-radius: 8px;
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

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
        .d-flex.align-items-center.gap-3 {
            flex-direction: column;
            gap: 10px !important;
        }

        .btn-primary.btn-sm {
            width: 100%;
        }

        .user-dropdown {
            width: 100%;
        }

        .user-menu-btn {
            width: 100%;
            justify-content: center;
        }
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
                    <li><a href="{{ route('landingpage.index.pelaporan') }}" class="active">Pelaporan</a></li>
                    <li><a href="{{ route('landingpage.index.dokumentasi') }}">Dokumentasi</a></li>
                    <li><a href="#services">Gambar</a></li>
                    <li><a href="#team">Korespondensi</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            @auth
            {{-- User sudah login - Tampilkan nama & dropdown --}}
            <div class="d-flex align-items-center gap-3">
                {{-- Tombol Laporan Baru --}}
                <a class="btn btn-primary btn-sm" href="{{ route('landingpage.index.pelaporanform') }}"
                    style="white-space: nowrap;">
                    <i class="bi bi-plus-circle"></i> Laporan Baru
                </a>

                {{-- User Dropdown --}}
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
            </div>

            @else
            {{-- User belum login - Redirect ke login --}}
            <a class="btn-getstarted" href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right" style="margin-right: 5px;"></i>
                Login
            </a>
            @endauth

        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container summary-container">

            <!-- Header Halaman -->
            <div class="summary-header">
                <h2><i class="bi bi-bar-chart-line-fill"></i> Ringkasan Laporan Progres Harian</h2>
                <p class="text-muted">PT. CIPTA RANCANG KONSTRUKSI</p>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card card-total">
                        <div class="card-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="card-content">
                            <h3 id="totalLaporan">24</h3>
                            <p>Total Laporan</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="summary-card card-approved">
                        <div class="card-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="card-content">
                            <h3 id="totalDisetujui">18</h3>
                            <p>Disetujui</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="summary-card card-pending">
                        <div class="card-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="card-content">
                            <h3 id="totalPending">4</h3>
                            <p>Menunggu Approval</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="summary-card card-revision">
                        <div class="card-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="card-content">
                            <h3 id="totalRevisi">2</h3>
                            <p>Perlu Revisi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="filter-controls">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filterPekerjaan" class="form-label fw-bold">Pekerjaan / Proyek</label>
                        <select class="form-select" id="filterPekerjaan">
                            <option value="">-- Semua Pekerjaan --</option>
                            <option value="revitalisasi_pelabuhan">Pekerjaan Revitalisasi Pelabuhan</option>
                            <option value="pembangunan_dermaga">Pembangunan Dermaga Baru</option>
                            <option value="renovasi_gudang">Renovasi Gudang Logistik</option>
                            <option value="instalasi_crane">Instalasi Crane Container</option>
                            <option value="perbaikan_jalan">Perbaikan Jalan Akses</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterTanggalMulai" class="form-label fw-bold">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="filterTanggalMulai">
                    </div>
                    <div class="col-md-3">
                        <label for="filterTanggalAkhir" class="form-label fw-bold">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="filterTanggalAkhir">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" id="btnFilter">
                            <i class="bi bi-filter"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="summary-table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableReports">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Pekerjaan</th>
                                <th>Pelapor</th>
                                <th>Lokasi GPS</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat oleh JavaScript -->
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="p-3">
                    <ul class="pagination justify-content-end mb-0" id="pagination">
                        <!-- Pagination akan dimuat oleh JavaScript -->
                    </ul>
                </nav>
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
                        <li><i class="bi bi-chevron-right"></i> <a href="#">Gambar</a></li>
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
                <span>All Rights Reserved</span>
            </p>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ringkasan.js"></script>

    <script>
    const userMenuBtn = document.getElementById('userMenuBtn');
    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('userDropdownMenu');
            dropdown.classList.toggle('show');
        });
    }

    // Close dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('userDropdownMenu');
        const button = document.getElementById('userMenuBtn');

        // Periksa apakah button dan dropdown ada
        if (button && dropdown && !button.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Close dropdown saat klik item menu
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            const dropdown = document.getElementById('userDropdownMenu');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        });
    });
    </script>

</body>

</html>