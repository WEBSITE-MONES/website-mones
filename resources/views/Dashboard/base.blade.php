<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    @php
    $setting = \App\Models\SettingAplikasi::first();
    @endphp

    <title>{{ $setting->nama_aplikasi ?? 'P-Mones' }}</title>

    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />

    <link rel="icon"
        href="{{ $setting && $setting->logo ? asset('img/mnp/'.$setting->logo) : asset('img/mnp/pelindo.png') }}"
        type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
    WebFont.load({
        google: {
            families: ["Public Sans:300,400,500,600,700"]
        },
        custom: {
            families: [
                "Font Awesome 5 Solid",
                "Font Awesome 5 Regular",
                "Font Awesome 5 Brands",
                "simple-line-icons",
            ],
            urls: ["{{ asset('assets/css/fonts.min.css') }}"],
        },
        active: function() {
            sessionStorage.fonts = true;
        },
    });
    </script>


    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset("assets/css/bootstrap.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/plugins.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/css/kaiadmin.min.css") }}" />

    <link rel="stylesheet" href="{{ asset("assets/css/demo.css") }}" />
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="white">
            <div class="sidebar-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="dark"
                    class="d-flex justify-content-center align-items-center w-100">
                    <a href="{{ route('dashboard.index') }}" class="logo">
                        <img src="{{ asset("assets/img/kaiadmin/logo_spjm2.png") }}" alt="navbar brand"
                            class="navbar-brand" height="95" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
                <!-- End Logo Header -->
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="user d-flex align-items-center">
                    <div class="avatar-sm avatar-margin">
                        <img src="{{ asset("assets/img/kaiadmin/user.png") }}" alt="..."
                            class="avatar-img rounded-circle">
                    </div>
                    <div class="info">
                        <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                            <span>
                                {{ Auth::user()->name }}
                                <span class="user-level">{{ Auth::user()->profile->jabatan ?? '-' }}</span>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="sidebar-content">
                    <ul class="nav nav-primary">
                        <li class="nav-item">
                            <a href="{{ route('dashboard.index') }}">
                                <i class="fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @if(auth()->user()->role === 'superadmin' || auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('pekerjaan.index') }}">
                                <i class="fas fa-tasks"></i>
                                <p>Rencana Kerja</p>
                            </a>
                        </li>
                        @endif

                        {{-- Progress Investasi (admin & user) --}}
                        @if(in_array(auth()->user()->role, ['superadmin','admin']))
                        <li class="nav-item">
                            <a href="#">
                                <i class="fas fa-money-bill-alt"></i>
                                <p>Anggaran</p>
                            </a>
                        </li>
                        @endif
                        @if(in_array(auth()->user()->role, ['superadmin','admin']))
                        <li class="nav-item">
                            <a href="{{ route('realisasi.index') }}">
                                <i class="fas fa-handshake"></i>
                                <p>Rencana Investasi</p>
                            </a>
                        </li>
                        @endif
                        {{-- Data Investasi (admin & user) --}}
                        <!-- @if(in_array(auth()->user()->role, ['superadmin','admin','user']))
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#realisasi" class="collapsed" aria-expanded="false">
                                <i class="fas fa-handshake"></i>
                                <p>Realisasi Investasi</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="realisasi">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="{{ route('realisasi.index') }}">
                                            <span class="sub-item">Investasi</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        @endif -->

                        {{-- Pengaturan (hanya superadmin) --}}
                        @if(auth()->user()->role === 'superadmin')
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">SPESIAL MENU</h4>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#pengaturan" class="collapsed" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                                <p>Pengaturan</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="pengaturan">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="{{ route('setting_aplikasi.index') }}">
                                            <span class="sub-item">Judul Aplikasi</span>
                                        </a>
                                    </li>
                                    <li><a href="#"><span class="sub-item">Approval Laporan</span></a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard.user') }}">
                                <i class="fas fa-user"></i>
                                <p>User</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#">
                                <i class="fas fa-user-shield"></i>
                                <p>Roles</p>
                                <span></span>
                            </a>
                        </li> -->
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="{{ asset("assets/img/kaiadmin/logo_light.svg") }}" alt="navbar brand"
                                class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <!--  selamat datang -->
                        @php
                        $setting = \App\Models\SettingAplikasi::first();
                        @endphp

                        <div class="text-blue d-none d-lg-block">
                            {{ $setting->ucapan ?? 'Selamat Datang di Aplikasi' }}
                            <span class="text-custom-blue">
                                {{ $setting->nama_perusahaan ?? 'PT Pelabuhan Indonesia (Pelindo)' }}
                            </span>
                        </div>

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic text-blue" href="#" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    {{ Auth::user()->name }} <i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn shadow">
                                    <!-- User Info -->
                                    <div class="user-box">
                                        <div class="avatar-lg"><img src="{{ asset("assets/img/kaiadmin/user.png") }}"
                                                alt="image profile" class="avatar-img rounded"></div>
                                        <div class="u-text">
                                            <h4>{{ Auth::user()->name }}</h4>
                                            <p class="text-muted">{{ Auth::user()->profile->jabatan ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <!-- Menu Items -->
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center"
                                            href="{{ route('account.index') }}">
                                            My Profile
                                            <i class="fa fa-user"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center"
                                            href="{{ route('account.setting') }}">
                                            Account Setting
                                            <i class="fa fa-cog"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center"
                                            href="#">
                                            Help
                                            <i class="fa fa-question-circle"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="dropdown-item d-flex justify-content-between align-items-center text-danger">
                                                Logout
                                                <i class="fa fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>

            <!--  isi tambahkan disini-->
            <div class="container">
                @yield('content')
            </div>

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-end">
                    <div class="copyright">
                        Copyright &copy; 2025 by
                        <span class="text-custom-blue">PT. Pelabuhan Indonesai (Pelindo)</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="{{ asset("assets/js/core/jquery-3.7.1.min.js") }}"></script>
    <script src="{{ asset("assets/js/core/popper.min.js") }}"></script>
    <script src="{{ asset("assets/js/core/bootstrap.min.js") }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset("assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js") }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <!-- jQuery Sparkline -->
    <script src="{{ asset("assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js") }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset("assets/js/plugin/chart-circle/circles.min.js") }}"></script>

    <!-- Datatables -->
    <script src=" {{ asset("assets/js/plugin/datatables/datatables.min.js") }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset("assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js") }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset("assets/js/plugin/jsvectormap/jsvectormap.min.js") }}"></script>
    <script src="{{ asset("assets/js/plugin/jsvectormap/world.js") }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset("assets/js/plugin/sweetalert/sweetalert.min.js")}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset("assets/js/kaiadmin.min.js") }}"></script>

    <!-- chart js -->
    <script src="{{ asset("assets/js/plugin/chart.js/chart.js/chart.min.jas") }}"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="{{ asset("assets/js/setting-demo.js") }}"></script>
    <script src="{{ asset("assets/js/demo.js") }}"></script>
    <script>
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#0068AC ",
        fillColor: "rgba(23, 125, 255, 0.14)",
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#f3545d",
        fillColor: "rgba(243, 84, 93, .14)",
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#ffa534",
        fillColor: "rgba(255, 165, 52, .14)",
    });
    </script>
    @stack('scripts')


</body>

</html>