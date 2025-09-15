@extends('Dashboard.base')

@section('title', 'Detail Pekerjaan')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Detail Pekerjaan</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container mt-3">
                        <div class="row">
                            {{-- Progress Investasi --}}
                            <div class="col-md-4">
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-primary text-white text-center">
                                        <h5 class="mb-0">PROGRES INVESTASI</h5>
                                    </div>
                                    <div class="card-body p-2">
                                        <a href="{{ route('pekerjaan.progres.fisik', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-percentage fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>PROGRES FISIK PEKERJAAN</strong><br>
                                                        <small>Pelaporan progres fisik</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.rkap', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-chart-bar fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>PENYERAPAN RKAP</strong><br>
                                                        <small>Pelaporan penyerapan RKAP</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.pembayaran', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-money-bill-wave fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>PEMBAYARAN</strong><br>
                                                        <small>Proses & realisasi pembayaran</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Investasi --}}
                            <div class="col-md-4">
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-primary text-white text-center">
                                        <h5 class="mb-0">DATA INVESTASI</h5>
                                    </div>
                                    <div class="card-body p-2">
                                        <a href="{{ route('pekerjaan.data.kontrak', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-handshake fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>KONTRAK</strong><br>
                                                        <small>Kontrak pekerjaan, RAB, RKS</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.data.gambar', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-image fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>GAMBAR</strong><br>
                                                        <small>DED, Shop Drawing, As Built</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.data.laporan', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-alt fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>LAPORAN</strong><br>
                                                        <small>Approval, QA/QC, dokumentasi</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.data.korespondensi', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-envelope-open-text fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>KORESPONDENSI</strong><br>
                                                        <small>Persuratan, berita acara, dsb</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Investasi --}}
                            <div class="col-md-4">
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-primary text-white text-center">
                                        <h5 class="mb-0">STATUS INVESTASI</h5>
                                    </div>
                                    <div class="card-body p-2">
                                        <a href="{{ route('pekerjaan.status.perencanaan', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-cogs fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>PERENCANAAN</strong><br>
                                                        <small>Investasi dalam tahap perencanaan</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.status.pelelangan', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-gavel fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>PELELANGAN</strong><br>
                                                        <small>Investasi dalam tahap pelelangan</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.status.pelaksanaan', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 mb-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-play-circle fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>PELAKSANAAN</strong><br>
                                                        <small>Investasi dalam tahap pelaksanaan</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                        <a href="{{ route('pekerjaan.status.selesai', $pekerjaan->id) }}">
                                            <div
                                                class="d-flex align-items-center justify-content-between border p-2 rounded">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-check-circle fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>SELESAI / DIBATALKAN</strong><br>
                                                        <small>Investasi telah selesai / dibatalkan</small>
                                                    </div>
                                                </div>
                                                <span>
                                                    <i class="fas fa-angle-right text-primary text-xl font-bold"></i>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom style --}}
<style>
.card-body .border strong,
.card-body .border small {
    color: #000 !important;
}

.card-body i {
    color: #007bff;
}

.card-body .fa-angle-right {
    color: #007bff !important;
}

.col-md-4 {
    padding-left: 10px;
    padding-right: 10px;
}

.row {
    margin-left: -10px;
    margin-right: -10px;
}

.card.mb-3 {
    margin-bottom: 0.75rem !important;
}
</style>
@endsection