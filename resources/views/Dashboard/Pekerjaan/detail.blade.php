@extends('Dashboard.base')

@section('title', 'Detail Pekerjaan')

@push('styles')
{{-- CSS Kustom untuk efek hover dan style ikon --}}
<style>
.stat-card {
    border-left: 5px solid var(--primary);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.action-list-item {
    transition: background-color 0.2s ease-in-out, transform 0.2s ease-in-out;
    border-bottom: 1px solid #eee;
}

.action-list-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.action-list-item .icon-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
}

.bg-primary-light {
    background-color: rgba(var(--primary-rgb), 0.1);
}
</style>
@endpush

@section('content')
<div class="page-inner">

    {{-- 1. HEADER HALAMAN YANG INFORMATIF --}}
    <div class="page-header">
        <h4 class="page-title">Detail Investasi</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="#"><i class="flaticon-home"></i></a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('pekerjaan.index') }}">Daftar Pekerjaan</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Detail</a>
            </li>
        </ul>
        <div class="ms-auto">
            <a href="{{ route('pekerjaan.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('pekerjaan.edit', $pekerjaan->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit me-1"></i> Edit Data
            </a>
        </div>
    </div>

    {{-- 2. PANEL INFORMASI UTAMA & VISUALISASI DATA --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-start mb-3">
                <div class="flex-grow-1">
                    <h3 class="fw-bolder text-primary mb-1">{{ $pekerjaan->nama_investasi }}</h3>
                    <p class="card-text text-muted">
                        <i class="fas fa-barcode me-1"></i> COA: {{ $pekerjaan->coa }} |
                        <i class="fas fa-building ms-2 me-1"></i> {{ optional($pekerjaan->wilayah)->nama }} |
                        <i class="fas fa-calendar-alt ms-2 me-1"></i> Tahun {{ $pekerjaan->tahun_usulan }}
                    </p>
                </div>
                {{-- Anda bisa menambahkan logic untuk status pekerjaan di sini --}}
                {{-- <span class="badge bg-success fs-6">Pelaksanaan</span> --}}
            </div>

            <hr>

            <div class="row mt-4 text-center">
                <div class="col-md-4 mb-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title text-muted fw-semibold">TOTAL ANGGARAN</h6>
                            <h4 class="fw-bolder">Rp {{ number_format($pekerjaan->kebutuhan_dana ?? 0,0,',','.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title text-muted fw-semibold">PENYERAPAN DANA</h6>
                            {{-- Ganti '0' dengan variabel penyerapan dana jika ada --}}
                            <h4 class="fw-bolder">Rp {{ number_format($pekerjaan->rkap ?? 0,0,',','.') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="card-title text-muted fw-semibold">PROGRES FISIK</h6>
                            {{-- Ganti '0' dengan variabel progres fisik jika ada --}}
                            @php $progresFisik = 0; @endphp
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: {{ $progresFisik }}%;" aria-valuenow="{{ $progresFisik }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    <strong class="fs-6">{{ $progresFisik }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. KARTU AKSI --}}
    <div class="row">
        {{-- Kolom Progress Investasi --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-primary">
                        <i class="fas fa-tasks me-2"></i> PROGRES INVESTASI
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('pekerjaan.progres.fisik', $pekerjaan->id) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3 action-list-item">
                        <span class="icon-circle bg-primary-light text-primary me-3"><i
                                class="fas fa-chart-bar"></i></span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">Progres Fisik Pekerjaan</h6>
                            <small class="text-muted">Pelaporan progres fisik & kurva S</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted ms-2" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('pekerjaan.pembayaran', $pekerjaan->id) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3 action-list-item">
                        <span class="icon-circle bg-primary-light text-primary me-3"><i
                                class="fas fa-money-bill-wave"></i></span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">Pembayaran</h6>
                            <small class="text-muted">Proses & realisasi pembayaran</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted ms-2" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Kolom Data Investasi --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-primary">
                        <i class="fas fa-folder-open me-2"></i> DATA & DOKUMEN
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('pekerjaan.data.kontrak', $pekerjaan->id) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3 action-list-item">
                        <span class="icon-circle bg-primary-light text-primary me-3"><i
                                class="fas fa-handshake"></i></span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">Kontrak</h6>
                            <small class="text-muted">Kontrak pekerjaan, RAB, RKS</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted ms-2" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('pekerjaan.data.gambar', $pekerjaan->id) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3 action-list-item">
                        <span class="icon-circle bg-primary-light text-primary me-3"><i class="fas fa-image"></i></span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">Gambar</h6>
                            <small class="text-muted">DED, Shop Drawing, As Built</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted ms-2" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('pekerjaan.data.laporan', $pekerjaan->id) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3 action-list-item">
                        <span class="icon-circle bg-primary-light text-primary me-3"><i
                                class="fas fa-file-alt"></i></span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">Laporan</h6>
                            <small class="text-muted">Approval, QA/QC, dokumentasi</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted ms-2" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('pekerjaan.data.korespondensi', $pekerjaan->id) }}"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3 action-list-item">
                        <span class="icon-circle bg-primary-light text-primary me-3"><i
                                class="fas fa-envelope-open-text"></i></span>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">Korespondensi</h6>
                            <small class="text-muted">Persuratan, berita acara, dsb</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted ms-2" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection