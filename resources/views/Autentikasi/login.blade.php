<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="P-MONES">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- iOS Icons -->
    <link rel="apple-touch-icon" href="{{ asset('LandingPage/assets/icons/icon-192x192.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('login/assets/Logo_Pelindo_2.png') }}">

    <title>Login P-MONES</title>
    <link rel="stylesheet" href="{{ asset('login/css/style.css') }}" />
</head>

<body class="login-page">
    <!-- HEADER -->
    <header class="site-header">
        <div class="container">
            <img src="{{ asset('login/assets/Logo_Pelindo_1.png') }}" alt="Logo P-MONES" class="logo-header">
            <nav>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <img src="{{ asset('login/assets/Pattern 3.png') }}" alt="ornamen kiri" class="ornament ornament-left">
    <img src="{{ asset('login/assets/pattern.png') }}" alt="ornamen kanan" class="ornament ornament-right">

    <!-- Card Login -->
    <div class="login-card">
        <div class="brand">
            <img src="{{ asset('login/assets/M_logo.png') }}" alt="Logo P-MONES" class="logo-img">
            <h1>P-MONES</h1>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" class="form">
            @csrf
            <label for="username">Username</label>
            <input id="username" name="username" type="text" placeholder="Masukkan username"
                value="{{ old('username') }}" required />

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Masukkan password" required />

            <button class="btn" type="submit">LOG IN</button>
        </form>

        <!-- Link ke register -->
        <p class="text-align-center" style="margin-top: 15px;">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </div>

    <!-- FOOTER -->
    <footer class="site-footer">
        <p>Copyright © 2025 by <span>PT. Pelabuhan Indonesia (Pelindo)</span></p>
    </footer>

    <!-- PWA Service Worker Registration -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => {
                    console.log('✅ SW registered:', registration.scope);
                })
                .catch(error => {
                    console.error('❌ SW registration failed:', error);
                });
        });
    }
    </script>
</body>

</html>