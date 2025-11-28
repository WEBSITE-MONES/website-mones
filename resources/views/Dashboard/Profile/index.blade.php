@extends('Dashboard.base')

@section('title', 'Profil Saya')

@section('content')
<div class="page-inner">
    {{-- Header Halaman --}}
    <div class="page-header mb-4">
        <h4 class="page-title fw-bolder text-primary d-flex align-items-center">
            <i class="fas fa-user-circle me-2"></i> Profil Saya
        </h4>
    </div>

    <div class="row">
        <div class="col-lg-8 order-last order-md-first">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light border-0 p-3 rounded-top-4">
                    <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Informasi Detail
                    </h5>
                </div>
                <div class="card-body p-4">
                    <dl class="row g-3">
                        <dt class="col-md-3 text-muted fw-normal d-flex align-items-center">
                            <i class="fas fa-user fa-fw me-2"></i> Nama Lengkap
                        </dt>
                        <dd class="col-md-9 fw-semibold">{{ $user->name }}</dd>

                        <dt class="col-md-3 text-muted fw-normal d-flex align-items-center">
                            <i class="fas fa-briefcase fa-fw me-2"></i> Jabatan
                        </dt>
                        <dd class="col-md-9 fw-semibold">{{ $profile->jabatan ?? '-' }}</dd>

                        <dt class="col-md-3 text-muted fw-normal d-flex align-items-center">
                            <i class="fas fa-calendar-alt fa-fw me-2"></i> Tanggal Lahir
                        </dt>
                        <dd class="col-md-9 fw-semibold">
                            {{ $profile && $profile->tanggal_lahir 
                                ? \Carbon\Carbon::parse($profile->tanggal_lahir)->isoFormat('D MMMM Y') 
                                : '-' }}
                        </dd>

                        <dt class="col-md-3 text-muted fw-normal d-flex align-items-center">
                            <i class="fas fa-star-of-david fa-fw me-2"></i> Agama
                        </dt>
                        <dd class="col-md-9 fw-semibold">{{ $profile->agama ?? '-' }}</dd>

                        <dt class="col-md-3 text-muted fw-normal d-flex align-items-center">
                            <i class="fas fa-venus-mars fa-fw me-2"></i> Jenis Kelamin
                        </dt>
                        <dd class="col-md-9 fw-semibold">
                            @if($profile && $profile->jenis_kelamin === 'L')
                            <i class="fas fa-mars text-primary"></i> Laki-laki
                            @elseif($profile && $profile->jenis_kelamin === 'P')
                            <i class="fas fa-venus text-danger"></i> Perempuan
                            @else
                            -
                            @endif
                        </dd>

                        <dt class="col-md-3 text-muted fw-normal d-flex align-items-center">
                            <i class="fas fa-phone-alt fa-fw me-2"></i> No. Telepon
                        </dt>
                        <dd class="col-md-9 fw-semibold">{{ $profile->nomor_telepon ?? '-' }}</dd>

                        <dt class="col-md-3 text-muted fw-normal d-flex align-items-center">
                            <i class="fas fa-map-marker-alt fa-fw me-2"></i> Alamat
                        </dt>
                        <dd class="col-md-9 fw-semibold" style="white-space: pre-wrap;">{{ $profile->alamat ?? '-' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- KARTU PROFIL PENGGUNA (KANAN)                             --}}
        {{-- Desain ulang total untuk tampilan yang lebih menarik dan     --}}
        {{-- fungsional. Mengganti tabel dengan List Group.             --}}
        {{-- ============================================================= --}}
        <div class="col-lg-4 order-first order-md-last">
            <div class="card shadow-sm border-0 rounded-4 text-center">
                <div class="card-body">
                    <div class="my-3">
                        <img src="{{ $profile->foto ? asset('storage/' . $profile->foto) : asset('assets/img/kaiadmin/user.png') }}"
                            alt="Foto Profil"
                            class="avatar avatar-xxl rounded-circle shadow-lg border border-4 border-white">
                    </div>
                    <h4 class="card-title fw-bolder mb-0">{{ $user->name }}</h4>
                    <p class="text-muted mb-4">{{ $profile->jabatan ?? 'Jabatan belum diisi' }}</p>

                    {{-- Mengganti tabel dengan List Group untuk menu aksi --}}
                    <div class="list-group list-group-flush text-start mt-3">
                        <a href="{{ route('account.edit') }}"
                            class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-pencil-alt fa-fw me-3 text-warning"></i>
                            <span class="fw-medium">Edit Profil</span>
                            <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                        </a>
                        <a href="{{ route('account.setting') }}"
                            class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-cog fa-fw me-3 text-primary"></i>
                            <span class="fw-medium">Pengaturan Akun</span>
                            <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt fa-fw me-3 text-danger"></i>
                            <span class="fw-medium">Logout</span>
                            <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form logout tersembunyi, jika diperlukan --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection