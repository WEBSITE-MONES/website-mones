<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <!-- ✅ CSRF Token untuk AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <style>
    .btn-outline-light {
        border: 2px solid rgba(255, 255, 255, 0.5);
        color: #fff;
        transition: all 0.3s ease;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.8);
        color: #fff;
    }

    .user-menu-btn {
        display: flex;
        align-items: center;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        padding: 8px 12px;
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

    /* Responsive */
    @media (max-width: 768px) {
        .user-menu-btn {
            padding: 8px;
        }

        .btn-outline-light {
            padding: 6px 12px;
            font-size: 14px;
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
                    <h2><i class="bi bi-clipboard-data"></i> Form Pelaporan Progress Harian</h2>
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
                                    <label for="pelapor" class="form-label">Nama Pelapor <span
                                            class="required">*</span></label>
                                    <input type="text" class="form-control" id="pelapor" name="pelapor"
                                        value="{{ Auth::user()->name }}" readonly style="background-color: #f8f9fa;">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle"></i> Otomatis terisi berdasarkan akun yang login
                                    </small>
                                </div>
                            </div>

                            <!-- ✅ 1. TAMBAHKAN: Pilih Wilayah (BARU) -->
                            <div class="mb-3">
                                <label for="wilayah" class="form-label">
                                    Wilayah / Regional <span class="required">*</span>
                                </label>
                                <select class="form-select" id="wilayah" name="wilayah" required>
                                    <option value="">-- Pilih Wilayah --</option>
                                </select>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> Pilih wilayah terlebih dahulu untuk memfilter
                                    pekerjaan
                                </small>
                            </div>

                            <!-- ✅ 2. UPDATE: Pilih Pekerjaan (akan terisi setelah pilih wilayah) -->
                            <div class="mb-3">
                                <label for="pekerjaan" class="form-label">
                                    Nama Pekerjaan / Proyek <span class="required">*</span>
                                </label>
                                <select class="form-select" id="pekerjaan" name="pekerjaan" required disabled>
                                    <option value="">-- Pilih Wilayah Terlebih Dahulu --</option>
                                </select>
                            </div>

                            <!-- 3. Pilih PO (muncul setelah pilih pekerjaan) -->
                            <div class="mb-3" id="poWrapper" style="display: none;">
                                <label for="po" class="form-label">
                                    Nomor PO / Kontrak <span class="required">*</span>
                                </label>
                                <select class="form-select" id="po" name="po_id" required>
                                    <option value="">-- Pilih PO --</option>
                                </select>
                                <small class="text-muted" id="poInfo"></small>
                            </div>

                            <!-- 4. Pilih Item Pekerjaan (muncul setelah pilih PO) -->
                            <div class="mb-3" id="itemPekerjaanWrapper" style="display: none;">
                                <label for="pekerjaan_item" class="form-label">
                                    Item Pekerjaan yang Dikerjakan <span class="required">*</span>
                                </label>
                                <select class="form-select" id="pekerjaan_item" name="pekerjaan_item_id" required>
                                    <option value="">-- Pilih Item Pekerjaan --</option>
                                </select>
                                <div id="itemInfo" class="mt-2" style="display: none;">
                                    <div class="alert alert-info">
                                        <strong>Info Item:</strong>
                                        <div id="itemDetails"></div>
                                    </div>
                                </div>
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

    <!-- Pelaporan JS -->
    <script src="assets/js/pelaporan.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Cascade script loaded');
        loadWilayahOptions();
    });

    function loadWilayahOptions() {
        const wilayahSelect = document.getElementById('wilayah');
        if (!wilayahSelect) return;

        wilayahSelect.innerHTML = '<option value="">⏳ Memuat wilayah...</option>';
        wilayahSelect.disabled = true;

        fetch('/landingpage/api/wilayah')
            .then(response => response.json())
            .then(result => {
                wilayahSelect.disabled = false;
                if (result.success && result.data.length > 0) {
                    wilayahSelect.innerHTML = '<option value="">-- Pilih Wilayah --</option>';
                    result.data.forEach(wilayah => {
                        const option = document.createElement('option');
                        option.value = wilayah.id;
                        option.textContent = `${wilayah.nama} (${wilayah.jumlah_pekerjaan} pekerjaan)`;
                        wilayahSelect.appendChild(option);
                    });
                    console.log('✅ Wilayah loaded:', result.data.length);
                }
            })
            .catch(error => {
                console.error('❌ Error loading wilayah:', error);
                wilayahSelect.disabled = false;
                wilayahSelect.innerHTML = '<option value="">❌ Gagal memuat wilayah</option>';
            });
    }

    // Wilayah → Pekerjaan
    document.addEventListener('DOMContentLoaded', function() {
        const wilayahSelect = document.getElementById('wilayah');
        if (wilayahSelect) {
            wilayahSelect.addEventListener('change', function() {
                const wilayahId = this.value;
                const pekerjaanSelect = document.getElementById('pekerjaan');
                const poWrapper = document.getElementById('poWrapper');
                const itemWrapper = document.getElementById('itemPekerjaanWrapper');

                if (poWrapper) poWrapper.style.display = 'none';
                if (itemWrapper) itemWrapper.style.display = 'none';

                if (!wilayahId) {
                    pekerjaanSelect.innerHTML =
                        '<option value="">-- Pilih Wilayah Terlebih Dahulu --</option>';
                    pekerjaanSelect.disabled = true;
                    return;
                }

                pekerjaanSelect.innerHTML = '<option value="">⏳ Memuat pekerjaan...</option>';
                pekerjaanSelect.disabled = true;

                fetch(`/landingpage/api/pekerjaan/wilayah/${wilayahId}`)
                    .then(response => response.json())
                    .then(result => {
                        pekerjaanSelect.disabled = false;
                        if (result.success && result.data.length > 0) {
                            pekerjaanSelect.innerHTML =
                                '<option value="">-- Pilih Pekerjaan --</option>';
                            result.data.forEach(pekerjaan => {
                                const option = document.createElement('option');
                                option.value = pekerjaan.id;
                                option.textContent = pekerjaan.nama_investasi;
                                pekerjaanSelect.appendChild(option);
                            });
                            console.log('✅ Pekerjaan loaded:', result.data.length);
                        } else {
                            pekerjaanSelect.innerHTML =
                                '<option value="">⚠️ Tidak ada pekerjaan</option>';
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error loading pekerjaan:', error);
                        pekerjaanSelect.disabled = false;
                        pekerjaanSelect.innerHTML =
                            '<option value="">❌ Gagal memuat pekerjaan</option>';
                    });
            });
        }
    });

    // Pekerjaan → PO
    document.addEventListener('DOMContentLoaded', function() {
        const pekerjaanSelect = document.getElementById('pekerjaan');
        if (pekerjaanSelect) {
            pekerjaanSelect.addEventListener('change', function() {
                const pekerjaanId = this.value;
                const poWrapper = document.getElementById('poWrapper');
                const poSelect = document.getElementById('po');
                const itemWrapper = document.getElementById('itemPekerjaanWrapper');

                if (pekerjaanId) {
                    poWrapper.style.display = 'block';
                    poSelect.innerHTML = '<option value="">⏳ Memuat PO...</option>';
                    poSelect.disabled = true;
                    itemWrapper.style.display = 'none';

                    fetch(`/landingpage/api/po/pekerjaan/${pekerjaanId}`)
                        .then(response => response.json())
                        .then(result => {
                            poSelect.disabled = false;
                            if (result.success && result.data.length > 0) {
                                poSelect.innerHTML = '<option value="">-- Pilih PO --</option>';
                                result.data.forEach(po => {
                                    const option = document.createElement('option');
                                    option.value = po.id;
                                    option.textContent =
                                        `${po.nomor_po} - ${po.pelaksana || 'N/A'}`;
                                    poSelect.appendChild(option);
                                });
                            } else {
                                poSelect.innerHTML =
                                    '<option value="">⚠️ Tidak ada PO tersedia</option>';
                            }
                        })
                        .catch(error => {
                            console.error('❌ Error loading PO:', error);
                            poSelect.disabled = false;
                            poSelect.innerHTML = '<option value="">❌ Gagal memuat data</option>';
                        });
                } else {
                    poWrapper.style.display = 'none';
                    itemWrapper.style.display = 'none';
                }
            });
        }
    });

    // PO → Item Pekerjaan
    document.addEventListener('DOMContentLoaded', function() {
        const poSelect = document.getElementById('po');
        if (poSelect) {
            poSelect.addEventListener('change', function() {
                const poId = this.value;
                const itemWrapper = document.getElementById('itemPekerjaanWrapper');
                const itemSelect = document.getElementById('pekerjaan_item');

                if (poId) {
                    itemWrapper.style.display = 'block';
                    itemSelect.innerHTML = '<option value="">⏳ Memuat item...</option>';
                    itemSelect.disabled = true;

                    fetch(`/landingpage/api/pekerjaan-items/po/${poId}`)
                        .then(response => response.json())
                        .then(result => {
                            itemSelect.disabled = false;
                            if (result.success && result.data.length > 0) {
                                itemSelect.innerHTML =
                                    '<option value="">-- Pilih Item Pekerjaan --</option>';
                                result.data.forEach(item => {
                                    const option = document.createElement('option');
                                    option.value = item.id;
                                    option.innerHTML = item.display;
                                    itemSelect.appendChild(option);
                                });
                            } else {
                                itemSelect.innerHTML =
                                    '<option value="">⚠️ Tidak ada item</option>';
                            }
                        })
                        .catch(error => {
                            console.error('❌ Error loading items:', error);
                            itemSelect.disabled = false;
                            itemSelect.innerHTML = '<option value="">❌ Gagal memuat data</option>';
                        });
                } else {
                    itemWrapper.style.display = 'none';
                }
            });
        }
    });
    </script>


</body>

</html>