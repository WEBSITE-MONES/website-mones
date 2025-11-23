<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Vendor - P-Mones</title>

    <!-- Fonts -->
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

    <!-- Vendor Profile CSS -->
    <link href="/LandingPage/assets/css/vendor-profile.css" rel="stylesheet">
</head>

<body>
    @include('LandingPage.partials.header')

    <main class="main">
        <div class="profile-container">
            <div class="container">
                {{-- Alert Messages --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" data-aos="fade-down">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-down">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-lg-4" data-aos="fade-right">
                        <div class="profile-header text-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffffff&color=4F46E5&size=200&bold=true"
                                alt="Avatar" class="profile-avatar mb-3">
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <p class="mb-0 opacity-90">
                                <i class="bi bi-briefcase me-2"></i>
                                {{ $profile->jabatan ?? 'Vendor' }}
                            </p>
                        </div>

                        <div class="info-card">
                            <h5><i class="bi bi-gear"></i> Pengaturan</h5>
                            <a href="{{ route('landingpage.profile.edit') }}" class="action-btn">
                                <span><i class="bi bi-pencil-square text-warning me-2"></i> Edit Profil</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="{{ route('landingpage.profile.password') }}" class="action-btn">
                                <span><i class="bi bi-key text-primary me-2"></i> Ubah Password</span>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>

                        <div class="info-card">
                            <h5><i class="bi bi-shield-check"></i> Status Akun</h5>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Status</span>
                                <span class="badge badge-status bg-success text-white">Aktif</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Role</span>
                                <span class="badge badge-status"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    Vendor
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-lg-8" data-aos="fade-left">
                        {{-- Personal Info --}}
                        <div class="info-card">
                            <h5><i class="bi bi-person"></i> Informasi Pribadi</h5>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-person-badge me-2"></i>Nama Lengkap</span>
                                <span class="info-value">{{ $user->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-calendar-event me-2"></i>Tanggal Lahir</span>
                                <span class="info-value">
                                    @if($profile && $profile->tanggal_lahir)
                                    {{ \Carbon\Carbon::parse($profile->tanggal_lahir)->isoFormat('D MMMM Y') }}
                                    <small class="text-muted">({{ \Carbon\Carbon::parse($profile->tanggal_lahir)->age }}
                                        tahun)</small>
                                    @else
                                    <span class="text-muted">Belum diisi</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-gender-ambiguous me-2"></i>Jenis Kelamin</span>
                                <span class="info-value">
                                    @if($profile && $profile->jenis_kelamin === 'L')
                                    <i class="bi bi-gender-male text-primary"></i> Laki-laki
                                    @elseif($profile && $profile->jenis_kelamin === 'P')
                                    <i class="bi bi-gender-female text-danger"></i> Perempuan
                                    @else
                                    <span class="text-muted">Belum diisi</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-circle-fill me-2"
                                        style="font-size: 8px;"></i>Agama</span>
                                <span class="info-value">{{ $profile->agama ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Contact Info --}}
                        <div class="info-card">
                            <h5><i class="bi bi-telephone"></i> Informasi Kontak</h5>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-envelope me-2"></i>Email</span>
                                <span class="info-value">{{ $user->email }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-phone me-2"></i>No. Telepon</span>
                                <span class="info-value">{{ $profile->nomor_telepon ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-geo-alt me-2"></i>Alamat</span>
                                <span class="info-value">{{ $profile->alamat ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Work Info --}}
                        <div class="info-card">
                            <h5><i class="bi bi-briefcase"></i> Informasi Pekerjaan</h5>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-person-workspace me-2"></i>Jabatan</span>
                                <span class="info-value">{{ $profile->jabatan ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-person-badge-fill me-2"></i>Username</span>
                                <span class="info-value">{{ $user->username }}</span>
                            </div>
                        </div>

                        {{-- Activity Timeline --}}
                        <div class="info-card">
                            <h5><i class="bi bi-clock-history"></i> Aktivitas Akun</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between mb-1">
                                            <strong>Akun Dibuat</strong>
                                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                        </div>
                                        <small
                                            class="text-muted">{{ $user->created_at->isoFormat('dddd, D MMMM Y [pukul] HH:mm') }}</small>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker" style="border-color: #10b981;"></div>
                                    <div class="timeline-content" style="border-left-color: #10b981;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <strong>Terakhir Diperbarui</strong>
                                            <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                        </div>
                                        <small
                                            class="text-muted">{{ $user->updated_at->isoFormat('dddd, D MMMM Y [pukul] HH:mm') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('LandingPage.partials.footer')

    <!-- Scripts -->
    <script src="/LandingPage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/LandingPage/assets/vendor/aos/aos.js"></script>
    <script src="/LandingPage/assets/js/main.js"></script>
    <script src="/LandingPage/assets/js/vendor-profile.js"></script>
</body>

</html>