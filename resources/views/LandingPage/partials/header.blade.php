<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
        <a href="{{ route('landingpage.index') }}" class="logo d-flex align-items-center me-auto">
            <h1 class="sitename">P-Mones</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ route('landingpage.index') }}">Home</a></li>
                <li><a href="{{ route('landingpage.index.pelaporan') }}">Pelaporan</a></li>
                <li><a href="{{ route('landingpage.index.dokumentasi') }}">Dokumentasi</a></li>
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