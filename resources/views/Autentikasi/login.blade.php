<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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

        <form action="{{ route('login.submit') }}" method="POST" class="form">
            @csrf
            <label for="username">Username</label>
            <input id="username" name="username" type="username" placeholder="Masukkan username" required />

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Masukkan password" required />

            <button class="btn" type="submit">LOG IN</button>
        </form>
        <!-- Link ke register -->
        <p class="text-aligh-center">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </div>

    <!-- FOOTER -->
    <footer class="site-footer">
        <p>Copyright © 2025 by <span>PT. Pelabuhan Indonesia (Pelindo)</span></p>
    </footer>
</body>

</html>