@extends('Dashboard.base')

@section('title', 'Edit User')

@section('content')
<div class="page-inner">
    {{-- Header Halaman Ditingkatkan --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title fw-bold">
            <i class="fas fa-user-edit me-2 text-primary"></i> Edit Pengguna: <span
                class="text-secondary">{{ $user->username }}</span>
        </h4>
        <a href="{{ route('dashboard.user') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar User
        </a>
    </div>

    ---

    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Kartu Utama Form --}}
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white p-3 rounded-top-3">
                    <h4 class="card-title mb-0 text-center fw-bolder">
                        FORM EDIT DATA PENGGUNA
                    </h4>
                </div>

                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4">

                        {{-- Alert Validasi Error (Ditingkatkan) --}}
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <h5 class="alert-heading fs-6 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>
                                Kesalahan Input!</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        {{-- Info Wajib Isi --}}
                        <div class="alert alert-info border-info shadow-sm p-2 mb-4">
                            <small class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                Bidang yang ditanda bintang merah (<span class="text-danger">*</span>) **wajib diisi**.
                            </small>
                        </div>

                        {{-- Grup Input Dasar User --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-id-card me-1"></i> Informasi Akun
                            </legend>

                            <div class="row g-3">
                                {{-- Username --}}
                                <div class="col-md-6">
                                    <label for="username" class="form-label fw-semibold">Username <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username"
                                        class="form-control form-control-sm @error('username') is-invalid @enderror"
                                        value="{{ old('username', $user->username) }}"
                                        placeholder="Masukkan username unik" required>
                                    @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Nama Lengkap --}}
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Nama Lengkap <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control form-control-sm @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap"
                                        required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email"
                                        class="form-control form-control-sm @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        {{-- Grup Input Role & Wilayah --}}
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-lock me-1"></i> Hak Akses & Password
                            </legend>
                            <div class="row g-3">
                                {{-- Role --}}
                                <div class="col-md-6">
                                    <label for="roleSelect" class="form-label fw-semibold">Role <span
                                            class="text-danger">*</span></label>
                                    <select name="role"
                                        class="form-select form-select-sm @error('role') is-invalid @enderror"
                                        id="roleSelect" required>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="superadmin"
                                            {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin
                                        </option>
                                        <option value="admin"
                                            {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                            User</option>
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Wilayah (hanya untuk role admin) --}}
                                <div class="col-md-6" id="wilayahField" style="display: none;">
                                    <label for="wilayah_id" class="form-label fw-semibold">Wilayah <span
                                            class="text-danger">*</span></label>
                                    <select name="wilayah_id" id="wilayah_id"
                                        class="form-select form-select-sm @error('wilayah_id') is-invalid @enderror">
                                        <option value="">-- Pilih Wilayah --</option>
                                        @foreach($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}"
                                            {{ old('wilayah_id', $user->wilayah_id) == $wilayah->id ? 'selected' : '' }}>
                                            {{ $wilayah->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('wilayah_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password (opsional) --}}
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-semibold">Password <small
                                            class="text-muted fst-italic">(kosongkan jika tidak diubah)</small></label>
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-sm @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password baru">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Card Footer Aksi --}}
                    <div class="card-footer d-flex justify-content-end p-3 bg-light border-top">
                        <a href="{{ route('dashboard.user') }}" class="btn btn-outline-danger me-2 px-4 shadow-sm">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleWilayahField() {
    let role = document.getElementById('roleSelect').value;
    // Menggunakan 'block' agar elemen col-md-6 tetap berfungsi sebagai blok kolom di grid.
    document.getElementById('wilayahField').style.display = (role === 'admin') ? 'block' : 'none';
}
document.getElementById('roleSelect').addEventListener('change', toggleWilayahField);
window.onload = toggleWilayahField;
</script>
@endpush

@endsection