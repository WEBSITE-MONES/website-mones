<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Monitoring Progress - P-Mones</title>

    <!-- Bootstrap & Icons -->
    <link href="/LandingPage/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/LandingPage/assets/css/main.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&family=Jost:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="/LandingPage/assets/css/main.css" rel="stylesheet">

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


    body {
        background: #f8f9fa;
        padding-top: 80px;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px 12px 0 0;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .stat-label {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .progress-custom {
        height: 8px;
        border-radius: 10px;
    }

    .table-wbs {
        font-size: 13px;
    }

    .table-wbs th {
        background: #495057;
        color: white;
        font-weight: 600;
        padding: 12px 8px;
        white-space: nowrap;
    }

    .level-indent-0 {
        font-weight: bold;
    }

    .level-indent-1 {
        padding-left: 30px !important;
        background: #f8f9fa;
    }

    .level-indent-2 {
        padding-left: 50px !important;
    }

    .select-pekerjaan {
        max-width: 500px;
        margin: 20px auto;
    }

    #loadingSpinner {
        text-align: center;
        padding: 40px;
        display: none;
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
                            class="{{ request()->routeIs('#') ? 'active' : '' }}">Gambar</a>
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

    <main class="main" style="min-height: 100vh; padding: 40px 0;">
        <div class="container">

            <!-- Page Title -->
            <div class="text-center mb-4">
                <h2 class="fw-bold" style="color: #495057;">
                    <i class="bi bi-graph-up-arrow"></i> Monitoring Progress Investasi
                </h2>
                <p class="text-muted">Pantau progress pekerjaan Anda secara real-time</p>
            </div>

            <!-- Select Pekerjaan -->
            <div class="select-pekerjaan">
                <select class="form-select form-select-lg" id="selectPekerjaan">
                    <option value="">-- Pilih Pekerjaan --</option>
                    @foreach($pekerjaans as $pekerjaan)
                    <option value="{{ $pekerjaan->id }}">
                        {{ $pekerjaan->nomor_prodef_sap }} - {{ $pekerjaan->nama_investasi }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat data progress...</p>
            </div>

            <!-- Content Container (Hidden by default) -->
            <div id="progressContent" style="display: none;">

                <!-- Info PO -->
                <div class="card shadow-sm mb-4" id="poInfoCard">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Nomor PO:</small>
                                <div class="fw-bold" id="nomorPO">-</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Pelaksana:</small>
                                <div class="fw-bold" id="pelaksana">-</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Nilai PO:</small>
                                <div class="fw-bold text-success" id="nilaiPO">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-label">Rencana Kumulatif</div>
                            <div class="stat-value text-primary" id="rencanaPct">0%</div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-primary" id="rencanaBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-label">Realisasi Kumulatif</div>
                            <div class="stat-value text-success" id="realisasiPct">0%</div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-success" id="realisasiBar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-label">Deviasi</div>
                            <div class="stat-value" id="deviasiPct" style="color: #17a2b8;">0%</div>
                            <small class="text-muted">Positif = Lebih cepat | Negatif = Terlambat</small>
                        </div>
                    </div>
                </div>

                <!-- Kurva-S Chart -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up"></i> Grafik Kurva-S Rencana vs Realisasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="curveSChart" height="80"></canvas>
                    </div>
                </div>

                <!-- Tabel WBS -->
                <div class="card shadow-sm">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-table"></i> Detail Progress Mingguan (WBS)
                        </h5>
                        <div>
                            <button class="btn btn-sm btn-light" onclick="expandAllRows()">
                                <i class="bi bi-plus-square"></i> Expand All
                            </button>
                            <button class="btn btn-sm btn-light ms-2" onclick="collapseAllRows()">
                                <i class="bi bi-dash-square"></i> Collapse All
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-wbs table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="width: 50px;">No</th>
                                        <th rowspan="2" style="min-width: 200px;">Item Pekerjaan</th>
                                        <th rowspan="2" style="width: 80px;">Volume</th>
                                        <th rowspan="2" style="width: 60px;">Satuan</th>
                                        <th rowspan="2" style="width: 80px;">Bobot (%)</th>
                                        <th colspan="20" class="text-center" id="monthHeaders">MINGGU</th>
                                    </tr>
                                    <tr id="weekHeaders">
                                        <!-- Dynamic week headers akan diisi via JS -->
                                    </tr>
                                </thead>
                                <tbody id="wbsTableBody">
                                    <tr>
                                        <td colspan="25" class="text-center text-muted py-4">
                                            Pilih pekerjaan untuk melihat detail progress
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
                    <a href="{{ route('landingpage.index') }}" class="d-flex align-items-center">
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
                        <li><i class="bi bi-chevron-right"></i> <a href="{{ route('landingpage.index') }}">Home</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a
                                href="{{ route('landingpage.index.pelaporan') }}">Pelaporan</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#gambar">Gambar</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a href="#korespondensi">Korespondensi</a></li>
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
    let currentChart = null;

    document.getElementById('selectPekerjaan').addEventListener('change', async function() {
        const pekerjaanId = this.value;

        if (!pekerjaanId) {
            document.getElementById('progressContent').style.display = 'none';
            return;
        }

        // Show loading
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('progressContent').style.display = 'none';

        try {
            const response = await fetch(`/landingpage/api/progress/${pekerjaanId}`);
            const result = await response.json();

            if (!result.success) {
                alert(result.message);
                return;
            }

            const data = result.data;

            // Update PO Info
            document.getElementById('nomorPO').textContent = data.po_info.nomor_po;
            document.getElementById('pelaksana').textContent = data.po_info.pelaksana || '-';
            document.getElementById('nilaiPO').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                data.po_info.nilai_po);

            // Update Summary
            const summary = data.summary;
            document.getElementById('rencanaPct').textContent = summary.rencana_pct + '%';
            document.getElementById('realisasiPct').textContent = summary.realisasi_pct + '%';
            document.getElementById('deviasiPct').textContent = (summary.deviasi_pct >= 0 ? '+' : '') +
                summary.deviasi_pct + '%';

            document.getElementById('rencanaBar').style.width = Math.min(summary.rencana_pct, 100) + '%';
            document.getElementById('realisasiBar').style.width = Math.min(summary.realisasi_pct, 100) +
                '%';

            // Color deviasi
            const deviasiEl = document.getElementById('deviasiPct');
            deviasiEl.style.color = summary.deviasi_pct >= 0 ? '#28a745' : '#dc3545';

            // Render Chart
            renderCurveSChart(data.chart_data);

            // Render Table
            renderWBSTable(data.items, data.master_minggu);

            // Show content
            document.getElementById('progressContent').style.display = 'block';

        } catch (error) {
            console.error('Error loading progress:', error);
            alert('Gagal memuat data progress. Silakan coba lagi.');
        } finally {
            document.getElementById('loadingSpinner').style.display = 'none';
        }
    });

    function renderCurveSChart(chartData) {
        const ctx = document.getElementById('curveSChart');

        if (currentChart) {
            currentChart.destroy();
        }

        currentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.week_label),
                datasets: [{
                        label: 'Rencana (%)',
                        data: chartData.map(d => d.rencana),
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Realisasi (%)',
                        data: chartData.map(d => d.realisasi),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Progress Kumulatif Rencana vs Realisasi',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            bottom: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                            },
                            afterBody: function(tooltipItems) {
                                const index = tooltipItems[0].dataIndex;
                                const deviasi = chartData[index].deviasi;
                                return '\nDeviasi: ' + (deviasi >= 0 ? '+' : '') + deviasi.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: value => value + '%',
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    function renderWBSTable(items, masterMinggu) {
        // Render week headers
        let weekHeadersHTML = '';
        masterMinggu.forEach(minggu => {
            weekHeadersHTML += `
                <th colspan="2" class="text-center" style="font-size: 11px; padding: 6px 4px;">
                    ${minggu.kode}<br>
                    <small style="font-weight: normal;">${minggu.tanggal}</small>
                </th>
            `;
        });

        weekHeadersHTML += masterMinggu.map(() =>
            '<th style="width: 50px; font-size: 10px; padding: 4px;">R</th><th style="width: 50px; font-size: 10px; padding: 4px;">A</th>'
        ).join('');

        document.getElementById('weekHeaders').innerHTML = weekHeadersHTML;

        // Render table body
        let bodyHTML = '';
        items.forEach((item, index) => {
            const rowClass = `level-indent-${item.level}`;
            const toggleIcon = item.has_children ?
                '<i class="bi bi-chevron-down toggle-icon"></i>' :
                '';

            bodyHTML += `
                <tr class="${rowClass}" data-id="${item.id}" data-level="${item.level}" onclick="${item.has_children ? `toggleRow(${item.id})` : ''}">
                    <td class="text-center">${index + 1}</td>
                    <td>${toggleIcon} ${item.kode} - ${item.nama}</td>
                    <td class="text-end">${item.volume || '-'}</td>
                    <td>${item.satuan || '-'}</td>
                    <td class="text-end">${item.bobot || '0.00'}%</td>
            `;

            // Add weekly progress cells
            masterMinggu.forEach(minggu => {
                const weekData = item.progress_data?. [minggu.id] || {
                    bobot_rencana: 0,
                    bobot_realisasi: 0
                };
                bodyHTML += `
                    <td class="text-end" style="font-size: 11px; padding: 6px 4px;">${weekData.bobot_rencana.toFixed(2)}</td>
                    <td class="text-end" style="font-size: 11px; padding: 6px 4px;">${weekData.bobot_realisasi.toFixed(2)}</td>
                `;
            });

            bodyHTML += '</tr>';
        });

        document.getElementById('wbsTableBody').innerHTML = bodyHTML;
    }

    function toggleRow(itemId) {
        // Implementasi collapse/expand (simplified)
        console.log('Toggle row:', itemId);
    }

    function expandAllRows() {
        document.querySelectorAll('.toggle-icon').forEach(icon => {
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        });
    }

    function collapseAllRows() {
        document.querySelectorAll('.toggle-icon').forEach(icon => {
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-right');
        });
    }
    </script>

</body>

</html>