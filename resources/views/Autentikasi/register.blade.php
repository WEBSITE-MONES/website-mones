<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="{{ asset('login/assets/Logo_Pelindo_2.png') }}">
    <title>Register P-MONES</title>
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
                    <li><a href="{{ url('/login') }}">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Ornamen kiri -->
    <img src="{{ asset('login/assets/Pattern 3.png') }}" alt="ornamen kiri" class="ornament ornament-left">
    <!-- Ornamen kanan -->
    <img src="{{ asset('login/assets/pattern.png') }}" alt="ornamen kanan" class="ornament ornament-right">

    <!-- Card Register -->
    <div class="login-card">
        <div class="brand">
            <img src="{{ asset('login/assets/M_logo.png') }}" alt="Logo P-MONES" class="logo-img">
            <h1>Register P-MONES</h1>
        </div>

        <!-- Pesan sukses -->
        @if(session('success'))
        <div class="alert alert-success"
            style="margin-bottom: 15px; padding: 10px; border-radius: 5px; background-color: #d4edda; color: #155724;">
            {{ session('success') }}
        </div>
        @endif

        <!-- Pesan error -->
        @if ($errors->any())
        <div class="alert alert-danger"
            style="margin-bottom: 15px; padding: 10px; border-radius: 5px; background-color: #f8d7da; color: #721c24;">
            <ul style="margin:0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="form">
            @csrf
            <label for="name">Nama Lengkap</label>
            <input id="name" name="name" type="text" placeholder="Masukkan nama lengkap" value="{{ old('name') }}"
                required />

            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="Masukkan email" value="{{ old('email') }}"
                required />

            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Masukkan password" required />
            <small>Password minimal 6 karakter</small>

            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password"
                required />
            <button class="btn" type="submit">DAFTAR</button>
        </form>

        <!-- Link ke login -->
        <p class="text-align-center" style="margin-top: 15px;">
            Sudah punya akun? <a href="{{ route('login') }}">Login sekarang</a>
        </p>
    </div>

    <!-- FOOTER -->
    <footer class="site-footer" style="margin-top:20px;">
        <p>Copyright © 2025 by <span>PT. Pelabuhan Indonesia (Pelindo)</span></p>
    </footer>

</body>

</html>