@extends('Dashboard.base')

@section('title', 'Edit Profile')

@section('content')
<div class="page-inner">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h2 class="page-title">Edit Profile</h2>
            <h5 class="fw-normal text-muted">Perbarui informasi personal dan kontak Anda.</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('account.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- KARTU 1: INFORMASI PRIBADI --}}
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3"><i
                                class="fas fa-user-edit text-primary me-2"></i>Informasi Pribadi</h5>

                        {{-- Notifikasi Error & Sukses dipindahkan ke sini agar lebih kontekstual --}}
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Gagal!</strong> Terdapat kesalahan pada input Anda.
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap Anda"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', $profile->tanggal_lahir) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="agama" class="form-label">Agama</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-mosque"></i></span>
                                    <select name="agama" id="agama" class="form-select">
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach(['Islam','Kristen Katolik','Kristen
                                        Protestan','Hindu','Budha','Konghucu','Lainnya'] as $agama)
                                        <option value="{{ $agama }}"
                                            {{ old('agama', $profile->agama) == $agama ? 'selected' : '' }}>
                                            {{ $agama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Jenis Kelamin <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki-laki"
                                    value="L"
                                    {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'L' ? 'checked' : '' }}
                                    required>
                                <label class="form-check-label" for="laki-laki">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan"
                                    value="P"
                                    {{ old('jenis_kelamin', $profile->jenis_kelamin) == 'P' ? 'checked' : '' }}
                                    required>
                                <label class="form-check-label" for="perempuan">Perempuan</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KARTU 2: INFORMASI KONTAK & PEKERJAAN --}}
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3"><i
                                class="fas fa-briefcase text-primary me-2"></i>Informasi Kontak & Pekerjaan</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    <input type="text" class="form-control" id="jabatan" name="jabatan"
                                        value="{{ old('jabatan', optional($profile)->jabatan) }}"
                                        placeholder="Masukkan jabatan">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_telepon" class="form-label">No. Telepon</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon"
                                        value="{{ old('nomor_telepon', $profile->nomor_telepon) }}"
                                        placeholder="Contoh: 08123456789" maxlength="15">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3"
                                    placeholder="Masukkan alamat lengkap">{{ old('alamat', $profile->alamat) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

        </div>

        <div class="col-lg-4">
            {{-- KARTU 3: FOTO PROFIL & AKSI --}}
            <div class="card card-round shadow-sm border-0 mb-4 sticky-lg-top" style="top: 20px;">
                <div class="card-body p-4 text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0d6efd&color=fff&size=128"
                        class="img-fluid rounded-circle mb-3" alt="Avatar">
                    <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <hr>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-check me-2"></i>Simpan
                            Perubahan</button>
                        <button type="reset" class="btn btn-outline-danger"><i class="fas fa-undo me-2"></i>Reset
                            Form</button>
                    </div>
                    <small class="form-text text-muted mt-3 d-block">
                        Input yang ditandai (<span class="text-danger">*</span>) wajib diisi.
                    </small>
                </div>
            </div>
        </div>
        </form> {{-- Tag form ditutup di sini setelah mencakup kedua kolom --}}
    </div>
</div>
@endsection