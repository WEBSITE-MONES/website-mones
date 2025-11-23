<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ubah Password - P-Mones</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&family=Jost:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="/LandingPage/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/LandingPage/assets/vendor/aos/aos.css" rel="stylesheet">

    <!-- Main CSS -->
    <link href="/LandingPage/assets/css/main.css" rel="stylesheet">
    <link href="/LandingPage/assets/css/vendor-profile.css" rel="stylesheet">
</head>

<body>
    @include('LandingPage.partials.header')

    <main class="main">
        <div class="password-container">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        {{-- Alert Messages --}}
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-down">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Error!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" data-aos="fade-down">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-down">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        {{-- Form Card --}}
                        <div class="form-card" data-aos="fade-up">
                            <h4>
                                <i class="bi bi-shield-lock"></i>
                                Ubah Password
                            </h4>
                            <p class="form-subtitle">
                                Pastikan password baru Anda aman dan mudah diingat.
                            </p>

                            <form action="{{ route('landingpage.profile.password.update') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">
                                        Password Lama<span class="required-mark">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password" placeholder="Masukkan password lama" required>
                                        <i class="bi bi-eye toggle-password" data-target="current_password"></i>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">
                                        Password Baru<span class="required-mark">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password"
                                            name="new_password" placeholder="Masukkan password baru" required>
                                        <i class="bi bi-eye toggle-password" data-target="new_password"></i>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">
                                        Konfirmasi Password Baru<span class="required-mark">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password_confirmation"
                                            name="new_password_confirmation" placeholder="Ulangi password baru"
                                            required>
                                        <i class="bi bi-eye toggle-password"
                                            data-target="new_password_confirmation"></i>
                                    </div>
                                </div>

                                {{-- Password Requirements Info --}}
                                <div class="password-requirements">
                                    <h6 class="mb-2" style="color: #374151; font-weight: 600;">
                                        <i class="bi bi-info-circle me-2"></i>Persyaratan Password:
                                    </h6>
                                    <ul>
                                        <li>Minimal 8 karakter</li>
                                        <li>Kombinasi huruf dan angka lebih aman</li>
                                        <li>Hindari password yang mudah ditebak</li>
                                        <li>Jangan gunakan informasi pribadi</li>
                                    </ul>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-3 justify-content-end mt-4">
                                    <a href="{{ route('landingpage.profile') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('LandingPage.partials.footer')

    {{-- Scripts --}}
    <script src="/LandingPage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/LandingPage/assets/vendor/aos/aos.js"></script>
    <script src="/LandingPage/assets/js/main.js"></script>
    <script src="/LandingPage/assets/js/vendor-profile.js"></script>
</body>

</html>