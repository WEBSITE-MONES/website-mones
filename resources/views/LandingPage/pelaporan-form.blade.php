<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Pelaporan Progress Harian - P-Mones</title>
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

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
    <!-- Pelaporan CSS -->
    <link href="assets/css/pelaporan.css" rel="stylesheet">
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
                    <li><a href="pelaporan.html" class="active">Pelaporan</a></li>
                    <li><a href="#services">Gambar</a></li>
                    li><a href="dokumentasi.html">Dokumentasi</a></li>
                    <li><a href="#team">Korespondensi</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="index.html">Kembali</a>

        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="form-container">

                <!-- Form Header -->
                <div class="form-header">
                    <h2><i class="bi bi-clipboard-data"></i> Form Pelaporan Progress Harian</h2>
                    <p>PT. CIPTA RANCANG KONSTRUKSI</p>
                </div>

                <!-- Form Body -->
                <div class="form-body">
                    <form id="progressForm" enctype="multipart/form-data">

                        <!-- Informasi Dasar -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-info-circle"></i>
                                <span>Informasi Dasar</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal" class="form-label">Tanggal Laporan <span
                                            class="required">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="pelapor" class="form-label">Nama Pelapor (Berdasarkan Akun yang
                                        diarahkan)<span class="required">*</span></label>
                                    <input type="text" class="form-control" id="pelapor" name="pelapor"
                                        placeholder="Masukkan nama pelapor" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="pekerjaan" class="form-label">Nama Pekerjaan / Proyek (Pilih pekerjaan dan
                                    nanti muncul sub pekerjaan yang ada di pekerjaan itu)<span
                                        class="required">*</span></label>
                                <select class="form-select" id="pekerjaan" name="pekerjaan" required>
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    <option value="revitalisasi_pelabuhan">Pekerjaan Revitalisasi Pelabuhan</option>
                                    <option value="pembangunan_dermaga">Pembangunan Dermaga Baru</option>
                                    <option value="renovasi_gudang">Renovasi Gudang Logistik</option>
                                    <option value="instalasi_crane">Instalasi Crane Container</option>
                                    <option value="perbaikan_jalan">Perbaikan Jalan Akses</option>
                                </select>
                            </div>

                            <!-- GPS Location (Auto Detect - Hidden Fields) -->
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                        </div>

                        <!-- Progress Pekerjaan -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Progress Pekerjaan Hari Ini</span>
                            </div>

                            <div class="mb-3">
                                <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan yang Dilakukan <span
                                        class="required">*</span></label>
                                <input type="text" class="form-control" id="jenis_pekerjaan" name="jenis_pekerjaan"
                                    placeholder="Contoh: Pemasangan Fender, Pengecoran, dll" required>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="volume" class="form-label">Volume Pekerjaan</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" id="volume" name="volume"
                                            placeholder="0.00">
                                        <input type="text" class="form-control" id="satuan" name="satuan"
                                            placeholder="Satuan (m³, m², unit, dll)" style="max-width: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Detail Pekerjaan <span
                                        class="required">*</span></label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                    placeholder="Jelaskan secara detail pekerjaan yang telah diselesaikan hari ini..."
                                    required></textarea>
                            </div>
                        </div>

                        <!-- Sumber Daya -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-people-fill"></i>
                                <span>Sumber Daya yang Digunakan</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_pekerja" class="form-label">Jumlah Tenaga Kerja</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="jumlah_pekerja"
                                            name="jumlah_pekerja" placeholder="0">
                                        <span class="input-group-text">Orang</span>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="alat_berat" class="form-label">Alat Berat yang Digunakan</label>
                                    <input type="text" class="form-control" id="alat_berat" name="alat_berat"
                                        placeholder="Contoh: Excavator, Crane, Truk, dll">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="material" class="form-label">Material yang Digunakan</label>
                                <textarea class="form-control" id="material" name="material" rows="3"
                                    placeholder="Contoh: Semen 10 sak, Pasir 5 m³, Besi 200 kg, dll"></textarea>
                            </div>
                        </div>

                        <!-- Kondisi Cuaca & Lapangan -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-cloud-sun"></i>
                                <span>Kondisi Cuaca & Lapangan</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kondisi Cuaca Saat Ini </label>
                                <div id="weatherInfo" class="weather-info">
                                    <div class="loading-spinner"></div>
                                    <div class="weather-details">
                                        <p>⏳ Mengambil data cuaca live...</p>
                                    </div>
                                </div>
                                <input type="hidden" id="cuaca_suhu" name="cuaca_suhu">
                                <input type="hidden" id="cuaca_deskripsi" name="cuaca_deskripsi">
                                <input type="hidden" id="cuaca_kelembaban" name="cuaca_kelembaban">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jam_kerja" class="form-label">Jam Kerja Efektif</label>
                                    <div class="input-group">
                                        <input type="number" step="0.5" class="form-control" id="jam_kerja"
                                            name="jam_kerja" placeholder="0">
                                        <span class="input-group-text">Jam</span>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="kondisi_lapangan" class="form-label">Kondisi Lapangan</label>
                                    <select class="form-select" id="kondisi_lapangan" name="kondisi_lapangan">
                                        <option value="normal">✅ Normal</option>
                                        <option value="becek">🌧️ Becek/Basah</option>
                                        <option value="kering">☀️ Sangat Kering</option>
                                        <option value="licin">⚠️ Licin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="kendala" class="form-label">Kendala / Hambatan (jika ada)</label>
                                <textarea class="form-control" id="kendala" name="kendala" rows="3"
                                    placeholder="Jelaskan kendala yang dihadapi jika ada..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="solusi" class="form-label">Solusi / Tindakan yang Diambil</label>
                                <textarea class="form-control" id="solusi" name="solusi" rows="3"
                                    placeholder="Jelaskan solusi atau tindakan yang telah dilakukan..."></textarea>
                            </div>
                        </div>

                        <!-- Dokumentasi Foto dengan GPS -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-camera"></i>
                                <span>Dokumentasi Foto Pekerjaan (Live GPS)</span>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Info:</strong> Foto akan otomatis menyimpan koordinat GPS dan data cuaca saat
                                pengambilan.
                                Klik untuk menggunakan <strong>Kamera</strong> atau pilih dari <strong>Galeri</strong>.
                            </div>

                            <div class="photo-upload-area" id="uploadArea">
                                <i class="bi bi-cloud-upload"></i>
                                <h5>📸 Klik untuk Ambil Foto / Upload</h5>
                                <p class="text-muted mb-0">Format: JPG, PNG (Maks. 5MB per foto)</p>
                                <p class="text-muted">Upload minimal 2 foto dokumentasi dengan GPS</p>
                                <input type="file" id="fotoInput" name="foto[]" accept="image/*" multiple hidden>
                            </div>

                            <div class="photo-preview" id="photoPreview"></div>
                        </div>

                        <!-- Rencana Esok Hari -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-calendar-check"></i>
                                <span>Rencana Pekerjaan Esok Hari</span>
                            </div>

                            <div class="mb-3">
                                <label for="rencana_besok" class="form-label">Rencana Kerja Besok <span
                                        class="required">*</span></label>
                                <textarea class="form-control" id="rencana_besok" name="rencana_besok" rows="4"
                                    placeholder="Jelaskan rencana pekerjaan untuk esok hari..." required></textarea>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-cancel me-2" onclick="resetForm()">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-send"></i> Submit Laporan
                            </button>
                        </div>

                    </form>
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
                <span>All Rights Reserved</span></p>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Pelaporan JS -->
    <script src="assets/js/pelaporan.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>