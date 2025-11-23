<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil - P-Mones</title>

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
        <div class="edit-container">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        {{-- Alert Messages --}}
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-down">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Error!</strong> Ada kesalahan pada input Anda.
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

                        {{-- Form Card --}}
                        <div class="form-card" data-aos="fade-up">
                            <h4>
                                <i class="bi bi-pencil-square"></i>
                                Edit Profil Vendor
                            </h4>

                            <form action="{{ route('landingpage.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Informasi Pribadi --}}
                                <div class="mb-4">
                                    <h6 class="text-muted mb-3">
                                        <i class="bi bi-person me-2"></i>Informasi Pribadi
                                    </h6>

                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Nama Lengkap<span class="required-mark">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap"
                                            required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="tanggal_lahir" class="form-label">
                                                Tanggal Lahir<span class="required-mark">*</span>
                                            </label>
                                            <input type="date" class="form-control" id="tanggal_lahir"
                                                name="tanggal_lahir"
                                                value="{{ old('tanggal_lahir', $profile->tanggal_lahir) }}" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="agama" class="form-label">Agama</label>
                                            <select name="agama" id="agama" class="form-select">
                                                <option value="">-- Pilih Agama --</option>
                                                @foreach(['Islam','Kristen Katolik','Kristen
                                                Protestan','Hindu','Buddha','Konghucu','Lainnya'] as $agama)
                                                <option value="{{ $agama }}"
                                                    {{ old('agama', $profile->agama) == $agama ? 'selected' : '' }}>
                                                    {{ $agama }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label d-block">
                                            Jenis Kelamin<span class="required-mark">*</span>
                                        </label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin"
                                                id="laki-laki" value="L"
                                                {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'L' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="laki-laki">
                                                <i class="bi bi-gender-male text-primary"></i> Laki-laki
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin"
                                                id="perempuan" value="P"
                                                {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'P' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="perempuan">
                                                <i class="bi bi-gender-female text-danger"></i> Perempuan
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                {{-- Informasi Kontak & Pekerjaan --}}
                                <div class="mb-4">
                                    <h6 class="text-muted mb-3">
                                        <i class="bi bi-telephone me-2"></i>Informasi Kontak & Pekerjaan
                                    </h6>

                                    <div class="mb-3">
                                        <label for="jabatan" class="form-label">Jabatan</label>
                                        <input type="text" class="form-control" id="jabatan" name="jabatan"
                                            value="{{ old('jabatan', $profile->jabatan) }}"
                                            placeholder="Masukkan jabatan">
                                    </div>

                                    <div class="mb-3">
                                        <label for="nomor_telepon" class="form-label">No. Telepon</label>
                                        <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon"
                                            value="{{ old('nomor_telepon', $profile->nomor_telepon) }}"
                                            placeholder="Contoh: 08123456789" maxlength="15">
                                    </div>

                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea name="alamat" id="alamat" class="form-control" rows="3"
                                            placeholder="Masukkan alamat lengkap">{{ old('alamat', $profile->alamat) }}</textarea>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-3 justify-content-end">
                                    <a href="{{ route('landingpage.profile') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Field yang ditandai <span class="required-mark">*</span> wajib diisi.
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer id="footer" class="footer">
        <div class="container copyright text-center">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">PT. Pelabuhan Indonesia (Persero)</strong></p>
        </div>
    </footer>

    {{-- Scripts --}}
    <script src="/LandingPage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/LandingPage/assets/vendor/aos/aos.js"></script>
    <script src="/LandingPage/assets/js/main.js"></script>
    <script src="/LandingPage/assets/js/vendor-profile.js"></script>
</body>

</html>