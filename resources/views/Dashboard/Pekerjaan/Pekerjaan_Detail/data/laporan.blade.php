@extends('Dashboard.base')

@section('title', 'Laporan')

@push('styles')
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-link {
    text-decoration: none;
    display: block;
    opacity: 0;
    animation: fadeInUp 0.5s ease-out forwards;
}

.col-md-4:nth-child(1) .card-link {
    animation-delay: 0.1s;
}

.col-md-4:nth-child(2) .card-link {
    animation-delay: 0.2s;
}

.col--md-4:nth-child(3) .card-link {
    animation-delay: 0.3s;
}

.card-link .card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    /* Transisi lebih halus & 'bouncy' */
}

.card-link .card:hover {
    transform: translateY(-10px);
    /* Terangkat lebih tinggi */
}

/* Efek Glow berwarna saat hover */
.card-link:hover .card-approval {
    box-shadow: 0 10px 30px -10px rgba(25, 135, 84, 0.6);
}

.card-link:hover .card-qaqc {
    box-shadow: 0 10px 30px -10px rgba(13, 110, 253, 0.6);
}

.card-link:hover .card-dokumentasi {
    box-shadow: 0 10px 30px -10px rgba(255, 193, 7, 0.6);
}

.card-link .card i {
    transition: transform 0.3s ease-in-out;
}

.card-link:hover .card i {
    transform: scale(1.15);
}

.view-details {
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease-out;
    font-weight: 600;
    font-size: 0.9rem;
    color: #6c757d;
}

.card-link:hover .view-details {
    opacity: 1;
    transform: translateY(0);
}
</style>
@endpush

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Kategori Laporan</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#laporanModal">
                        <i class="fa fa-plus"></i> Tambah Laporan
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        {{-- Folder Approval --}}
                        <div class="col-md-4 mb-4">
                            <a href="{{ route('pekerjaan.laporan.approval', ['id' => $pekerjaan->id]) }}"
                                class="card-link">
                                <div class="card card-round text-center card-approval">
                                    <div class="card-body">
                                        <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                                        <h5 class="card-title">Approval</h5>
                                        <p class="card-text text-muted">Laporan persetujuan</p>
                                        <span class="view-details text-success">Lihat Detail →</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        {{-- Folder QA/QC --}}
                        <div class="col-md-4 mb-4">
                            <a href="{{ route('pekerjaan.laporan.qa', ['id' => $pekerjaan->id]) }}" class="card-link">
                                <div class="card card-round text-center card-qaqc">
                                    <div class="card-body">
                                        <i class="fa fa-clipboard-list fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title">QA/QC</h5>
                                        <p class="card-text text-muted">Laporan kualitas</p>
                                        <span class="view-details text-primary">Lihat Detail →</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        {{-- Folder Dokumentasi --}}
                        <div class="col-md-4 mb-4">
                            <a href="{{ route('pekerjaan.laporan.dokumentasi', ['id' => $pekerjaan->id]) }}"
                                class="card-link">
                                <div class="card card-round text-center card-dokumentasi">
                                    <div class="card-body">
                                        <i class="fa fa-folder-open fa-3x text-warning mb-3"></i>
                                        <h5 class="card-title">Dokumentasi</h5>
                                        <p class="card-text text-muted">Laporan Proyek</p>
                                        <span class="view-details text-warning">Lihat Detail →</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="laporanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <select id="keterangan" name="keterangan" class="form-select" required>
                            <option value="">-- Pilih Keterangan --</option>
                            <option value="Approval">Approval</option>
                            <option value="QA/QC">QA/QC</option>
                            <option value="Dokumentasi">Dokumentasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <input type="file" name="file_laporan" class="form-control" accept=".pdf,.doc,.docx,image/*"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection