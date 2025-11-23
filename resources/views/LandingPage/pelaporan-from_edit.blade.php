<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Edit Laporan Progress - P-Mones</title>
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

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="{{ route('landingpage.index') }}" class="logo d-flex align-items-center me-auto">
                <h1 class="sitename">P-Mones</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    @auth
                    <li><a href="{{ route('landingpage.index') }}"
                            class="{{ request()->routeIs('landingpage.index') ? 'active' : '' }}">Home</a></li>
                    <li><a href="{{ route('landingpage.index.pelaporan') }}"
                            class="{{ request()->routeIs('landingpage.index.pelaporan*') ? 'active' : '' }}">Pelaporan</a>
                    </li>
                    <li><a href="{{ route('landingpage.index.dokumentasi') }}"
                            class="{{ request()->routeIs('landingpage.index.dokumentasi') ? 'active' : '' }}">Dokumentasi</a>
                    </li>
                    <li><a href="{{ route('landingpage.monitoring.progress') }}"
                            class="{{ request()->routeIs('landingpage.monitoring.progress') ? 'active' : '' }}">Monitoring</a>
                    </li>
                    <li><a href="{{ route('landingpage.monitoring.progress') }}"
                            class="{{ request()->routeIs('landingpage.monitoring.progress') ? 'active' : '' }}">Gambar</a>
                    </li>
                    @else
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#services">Layanan</a></li>
                    <li><a href="#contact">Kontak</a></li>
                    @endauth
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            @auth
            {{-- User Dropdown - Vendor Only --}}
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
                                    <span class="badge-role">Vendor</span>
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
            {{-- User belum login --}}
            <a class="btn-getstarted" href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right" style="margin-right: 5px;"></i>
                Login
            </a>
            @endauth

        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="form-container">

                <!-- Form Header -->
                <div class="form-header">
                    <h2><i class="bi bi-pencil-square"></i> Edit Laporan Progress Harian</h2>
                    <p>PT. CIPTA RANCANG KONSTRUKSI</p>
                </div>

                <!-- Form Body -->
                <div class="form-body">
                    <form id="progressEditForm" enctype="multipart/form-data">

                        <!-- ID Laporan (Hidden) -->
                        <input type="hidden" id="reportId" name="reportId" value="">

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
                                    <label for="pelapor" class="form-label">Nama Pelapor <span
                                            class="required">*</span></label>
                                    <input type="text" class="form-control" id="pelapor" name="pelapor"
                                        placeholder="Masukkan nama pelapor" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="pekerjaan" class="form-label">Nama Pekerjaan / Proyek <span
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

                            <!-- GPS Location  -->
                            <div class="mb-3">
                                <div class="alert alert-secondary d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill me-2" style="font-size: 24px;"></i>
                                    <div class="flex-grow-1">
                                        <strong>Lokasi GPS Tersimpan:</strong>
                                        <span id="locationDetail">Loading...</span>
                                        <input type="hidden" id="latitude" name="latitude">
                                        <input type="hidden" id="longitude" name="longitude">
                                    </div>
                                </div>
                            </div>
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
                                <label class="form-label">Kondisi Cuaca (Saat Laporan Dibuat)</label>
                                <div class="alert alert-info" id="weatherInfo">
                                    <i class="bi bi-cloud-sun-fill"></i> <span id="savedWeather">Loading...</span>
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

                        <!-- Dokumentasi Foto -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="bi bi-camera"></i>
                                <span>Dokumentasi Foto Pekerjaan</span>
                            </div>

                            <!-- Foto yang Sudah Ada -->
                            <div class="mb-3">
                                <label class="form-label">Foto yang Sudah Ter-upload</label>
                                <div class="photo-preview active" id="existingPhotoPreview">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Upload Foto Baru -->
                            <label class="form-label mt-3">Tambah Foto Baru (Opsional)</label>
                            <div class="photo-upload-area" id="uploadArea">
                                <i class="bi bi-cloud-upload"></i>
                                <h5>Klik atau Drag & Drop untuk Menambah Foto</h5>
                                <p class="text-muted mb-0">Format: JPG, PNG (Maks. 5MB per foto)</p>
                                <input type="file" id="fotoInput" name="fotoBaru[]" accept="image/*" multiple hidden>
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
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-danger" id="deleteBtn">
                                <i class="bi bi-trash"></i> Hapus Laporan
                            </button>

                            <div>
                                <button type="button" class="btn btn-cancel me-2"
                                    onclick="window.location.href='ringkasan.html'">
                                    <i class="bi bi-x-circle"></i> Batal
                                </button>
                                <button type="submit" class="btn-submit">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </div>
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
                        <li><i class="bi bi-chevron-right"></i> <a href="ringkasan.html">Pelaporan</a></li>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Pelaporan Edit JS -->
    <script src="assets/js/pelaporan_edit.js"></script>
    <script src="assets/js/main.js"></script>


</body>

</html>