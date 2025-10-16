@extends('Dashboard.base')

@section('title', 'Laporan QA/QC')

@push('styles')
<style>
.card-body {
    padding: 1.5rem;
}

#laporanQaqcTable thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
}

#laporanQaqcTable tbody tr {
    transition: background-color 0.2s ease-in-out;
    border-bottom: 1px solid #f0f0f0;
}

#laporanQaqcTable tbody tr:last-child {
    border-bottom: none;
}

#laporanQaqcTable tbody tr:hover {
    background-color: #f1f1f1;
}

#laporanQaqcTable tbody td {
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
</style>
@endpush

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">
                        Laporan QA/QC: <span class="fw-bold">{{ $pekerjaan->nama_pekerjaan }}</span>
                    </h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#laporanQaqcModal">
                        <i class="fa fa-plus"></i> Tambah Laporan QA/QC
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="laporanQaqcTable" class="display table table-hover">
                            <thead>
                                <tr>
                                    <th class="action-column">AKSI</th>
                                    <th>KETERANGAN</th>
                                    <th>FILE</th>
                                    <th>TANGGAL PEMERIKSAAN</th>
                                    <th>HASIL</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- ### DATA DUMMY 1 ### --}}
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
                                        <div class="keterangan-utama">Hasil Uji Tekan Beton K-300</div>
                                        <div class="keterangan-sub">Batch: #3 | Lokasi: Kolom Lt. 2</div>
                                    </td>
                                    <td>
                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-file-excel"></i> Lihat Laporan
                                        </a>
                                    </td>
                                    <td>07 Oktober 2025</td>
                                    <td>
                                        <span class="badge bg-success">Lulus Uji</span>
                                    </td>
                                </tr>

                                {{-- ### DATA DUMMY 2 ### --}}
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
                                        <div class="keterangan-utama">Inspeksi Kualitas Pengecatan Dinding</div>
                                        <div class="keterangan-sub">Area: Dinding Eksterior Sayap Kanan</div>
                                    </td>
                                    <td>
                                        <a href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-file-alt"></i> Lihat Checklist
                                        </a>
                                    </td>
                                    <td>05 Oktober 2025</td>
                                    <td>
                                        <span class="badge bg-danger">Perlu Perbaikan</span>
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

{{-- Modal Tambah Laporan QA/QC --}}
<div class="modal fade" id="laporanQaqcModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Laporan QA/QC Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form untuk upload laporan QA/QC --}}
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pekerjaan_id" value="{{ $pekerjaan->id }}">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Nama Pemeriksaan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Uji Tekan Beton"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="sub_keterangan" class="form-label">Detail / Lokasi</label>
                        <input type="text" name="sub_keterangan" class="form-control"
                            placeholder="Contoh: Batch #3 | Kolom Lt. 2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File Laporan</label>
                        <input type="file" name="file_laporan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="hasil" class="form-label">Hasil Pemeriksaan</label>
                        <select id="hasil" name="hasil" class="form-select" required>
                            <option value="">-- Pilih Hasil --</option>
                            <option value="Lulus Uji">Lulus Uji</option>
                            <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                            <option value="Gagal Uji">Gagal Uji</option>
                        </select>
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
    $('#laporanQaqcTable').DataTable({
        responsive: true,
        "order": [
            [3, "desc"]
        ], // Mengurutkan berdasarkan tanggal
        "columnDefs": [{
            "targets": 0,
            "orderable": false
        }]
    });
});
</script>
@endpush