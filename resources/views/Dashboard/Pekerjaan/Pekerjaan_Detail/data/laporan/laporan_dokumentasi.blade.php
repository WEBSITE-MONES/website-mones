@extends('Dashboard.base')

@section('title', 'Laporan Dokumentasi')

@push('styles')
<style>
.card-body {
    padding: 1.5rem;
}

#laporanDokumentasiTable thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
}

#laporanDokumentasiTable tbody tr {
    transition: background-color 0.2s ease-in-out;
    border-bottom: 1px solid #f0f0f0;
}

#laporanDokumentasiTable tbody tr:last-child {
    border-bottom: none;
}

#laporanDokumentasiTable tbody tr:hover {
    background-color: #f1f1f1;
}

#laporanDokumentasiTable tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.keterangan-utama {
    font-weight: 500;
    color: #212529;
}

.keterangan-sub {
    font-size: 0.85rem;
    color: #6c757d;
}

.action-column {
    width: 50px;
    text-align: center;
}

.action-column .dropdown-toggle::after {
    display: none;
}

.action-column .btn {
    width: 35px;
    height: 35px;
    border-radius: 50%;
}

.thumbnail-preview {
    width: 100px;
    height: 60px;
    object-fit: cover;
    border-radius: 0.25rem;
}
</style>
@endpush

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">
                        Laporan Dokumentasi: <span class="fw-bold"></span>
                    </h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#laporanDokumentasiModal">
                        <i class="fa fa-plus"></i> Tambah Dokumentasi
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="laporanDokumentasiTable" class="display table table-hover">
                            <thead>
                                <tr>
                                    <th class="action-column">AKSI</th>
                                    <th>PREVIEW</th>
                                    <th>KETERANGAN</th>
                                    <th>TANGGAL UPLOAD</th>
                                    <th>FILE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="action-column">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow">
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="fa fa-edit text-primary me-2"></i> Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i
                                                            class="fa fa-trash me-2"></i> Hapus</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <img src="https://via.placeholder.com/100x60.png?text=Progres+Area+A"
                                            alt="Preview" class="thumbnail-preview">
                                    </td>
                                    <td>
                                        <div class="keterangan-utama">Dokumentasi Foto Progres Harian</div>
                                        <div class="keterangan-sub">Lokasi: Galian Fondasi Area A</div>
                                    </td>
                                    <td>07 Oktober 2025</td>
                                    <td>
                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-file-image"></i> Lihat Gambar
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="action-column">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow">
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="fa fa-edit text-primary me-2"></i> Edit</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i
                                                            class="fa fa-trash me-2"></i> Hapus</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center text-muted"><i class="fa fa-file-alt fa-2x"></i></div>
                                    </td>
                                    <td>
                                        <div class="keterangan-utama">Laporan Harian (Daily Report)</div>
                                        <div class="keterangan-sub">Cakupan: Seluruh area proyek</div>
                                    </td>
                                    <td>06 Oktober 2025</td>
                                    <td>
                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-file-word"></i> Lihat Dokumen
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Laporan Dokumentasi --}}
<div class="modal fade" id="laporanDokumentasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Laporan Dokumentasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pekerjaan_id" value="{{ $pekerjaan->id }}">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                            placeholder="Contoh: Progres Harian Area A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File (Gambar atau Dokumen)</label>
                        <input type="file" name="file_laporan" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#laporanDokumentasiTable').DataTable({
        responsive: true,
        "order": [
            [3, "desc"]
        ], // Mengurutkan berdasarkan tanggal upload terbaru
        "columnDefs": [{
            "targets": [0, 1], // Nonaktifkan sorting untuk kolom Aksi dan Preview
            "orderable": false
        }]
    });
});
</script>
@endpush