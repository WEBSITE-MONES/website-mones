<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>P-Mones - Monitoring Investasi</title>
    <meta name="description" content="Sistem Monitoring Investasi PT Pelabuhan Indonesia">
    <meta name="keywords" content="monitoring, investasi, pelabuhan indonesia">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="P-Mones">
    <meta name="mobile-web-app-capable" content="yes">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Favicons & PWA Icons -->
    <link href="/LandingPage/assets/icons/icon-72x72.png" rel="icon">
    <link href="/LandingPage/assets/icons/icon-152x152.png" rel="apple-touch-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/LandingPage/assets/icons/icon-192x192.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&family=Jost:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="/LandingPage/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="/LandingPage/assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">

    <!-- Offline Indicator -->
    <div class="offline-indicator" id="offlineIndicator">
        📡 Tidak ada koneksi internet
    </div>

    <!-- Install PWA Prompt -->
    <div id="installPrompt">
        <div class="prompt-content">
            <div class="prompt-icon">📱</div>
            <div class="prompt-text">
                <strong>Install P-Mones</strong>
                <div style="font-size: 13px; opacity: 0.9;">Akses lebih cepat dari layar utama</div>
            </div>
            <div class="prompt-buttons">
                <button id="installBtn">Install</button>
                <button id="dismissBtn">Nanti</button>
            </div>
        </div>
    </div>

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

    <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero section dark-background">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center"
                        data-aos="zoom-out">
                        <h1>SELAMAT DATANG</h1>
                        <p>PT. Pelabuhan Indonesia (Persero) Regional 4</p>
                        <div class="d-flex">
                            <a href="#about" class="btn-get-started">Get Started</a>
                        </div>
                    </div>
                    <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="200">
                        <img src="{{ asset('LandingPage/assets/img/hero-img.png') }}" class="img-fluid animated" alt="">
                    </div>
                </div>
            </div>
        </section>

        <!-- Progress Monitoring -->
        <section id="skills" class="skills section">
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row">
                    <div class="col-lg-6 d-flex align-items-center">
                        <img src="{{ asset('LandingPage/assets/img/illustration/illustration-10.webp') }}"
                            class="img-fluid" alt="">
                    </div>

                    <div class="col-lg-6 pt-4 pt-lg-0 content">
                        <h3>Progress Monitoring Investasi</h3>
                        <p class="fst-italic">
                            Pantau progress investasi secara real-time untuk memastikan target tercapai sesuai rencana.
                        </p>

                        <div class="skills-content skills-animation">
                            <div class="progress">
                                <span class="skill"><span>Perencanaan</span> <i class="val">100%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill"><span>Pelelangan</span> <i class="val">90%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="90" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill"><span>Pelaksanaan</span> <i class="val">75%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="75" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="progress">
                                <span class="skill"><span>Serah Terima</span> <i class="val">55%</i></span>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="55" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section id="call-to-action" class="call-to-action section dark-background">
            <img src="{{ asset('LandingPage/assets/img/bg/bg-8.webp') }}" alt="">
            <div class="container">
                <div class="row" data-aos="zoom-in" data-aos-delay="100">
                    <div class="col-xl-9 text-center text-xl-start">
                        <h3>Mulai Monitoring Sekarang</h3>
                        <p>Pantau progress investasi Anda secara real-time dengan sistem monitoring yang terintegrasi
                            dan mudah digunakan. Buat laporan dan dokumentasi dengan cepat.</p>
                    </div>
                    <div class="col-xl-3 cta-btn-container text-center">
                        <a class="cta-btn align-middle" href="{{ route('landingpage.index.pelaporan') }}">Buat
                            Laporan</a>
                    </div>
                </div>
            </div>
        </section>

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
                        <li><i class="bi bi-chevron-right"></i> <a
                                href="{{ route('landingpage.index.dokumentasi') }}">Dokumentasi</a></li>
                        <li><i class="bi bi-chevron-right"></i> <a
                                href="{{ route('landingpage.monitoring.progress') }}">Monitoring</a></li>
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

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="/LandingPage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/LandingPage/assets/vendor/php-email-form/validate.js"></script>
    <script src="/LandingPage/assets/vendor/aos/aos.js"></script>
    <script src="/LandingPage/assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="/LandingPage/assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="/LandingPage/assets/vendor/waypoints/noframework.waypoints.js"></script>
    <script src="/LandingPage/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="/LandingPage/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

    <!-- Main JS File -->
    <script src="/LandingPage/assets/js/main.js"></script>

    <script>
    // Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', async () => {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                });
                console.log('✅ SW registered:', registration.scope);

                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker
                            .controller) {
                            console.log('🔄 Update available!');
                        }
                    });
                });
            } catch (err) {
                console.error('❌ SW registration failed:', err);
            }
        });
    }

    // PWA Install Prompt
    let deferredPrompt;
    const installPrompt = document.getElementById('installPrompt');
    const installBtn = document.getElementById('installBtn');
    const dismissBtn = document.getElementById('dismissBtn');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;

        const dismissedTime = localStorage.getItem('pwaPromptDismissed');
        const sevenDays = 7 * 24 * 60 * 60 * 1000;

        if (!dismissedTime || (Date.now() - dismissedTime) > sevenDays) {
            setTimeout(() => {
                installPrompt.style.display = 'block';
            }, 2000);
        }
    });

    installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) {
            alert('Install prompt tidak tersedia. Coba buka dari browser mobile.');
            return;
        }

        installPrompt.style.display = 'none';
        deferredPrompt.prompt();

        const {
            outcome
        } = await deferredPrompt.userChoice;
        console.log(`Install outcome: ${outcome}`);

        deferredPrompt = null;
    });

    dismissBtn.addEventListener('click', () => {
        installPrompt.style.display = 'none';
        localStorage.setItem('pwaPromptDismissed', Date.now());
    });

    window.addEventListener('appinstalled', () => {
        console.log('✅ PWA installed successfully!');
        installPrompt.style.display = 'none';
    });

    // Offline indicator
    const offlineIndicator = document.getElementById('offlineIndicator');
    window.addEventListener('offline', () => offlineIndicator.classList.add('show'));
    window.addEventListener('online', () => offlineIndicator.classList.remove('show'));
    if (!navigator.onLine) offlineIndicator.classList.add('show');

    // User Dropdown Toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');

    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (userMenuBtn && userDropdownMenu &&
                !userMenuBtn.contains(e.target) &&
                !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.classList.remove('show');
            }
        });

        // Close dropdown when clicking menu items
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                if (userDropdownMenu) {
                    userDropdownMenu.classList.remove('show');
                }
            });
        });
    }
    </script>

</body>

</html>