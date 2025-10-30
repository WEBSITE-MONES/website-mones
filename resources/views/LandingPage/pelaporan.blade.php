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
</head>

<body>

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="index.html" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">P-Mones</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="pelaporan.html" class="active">Pelaporan</a></li>
                    <li><a href="#services">Gambar</a></li>
                    li><a href="dokumentasi.html">Dokumentasi</a></li>
                    <li><a href="#team">Korespondensi</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="pelaporan-form.html">
                <i class="bi bi-plus-circle"></i> Laporan Baru
            </a>

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
    <script src="{{ asset("assets/js/ringkasan.js") }}"></script>

</body>

</html>