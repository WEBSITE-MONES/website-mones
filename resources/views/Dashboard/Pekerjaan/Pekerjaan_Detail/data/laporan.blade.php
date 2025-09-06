@extends('Dashboard.base')

@section('title', 'Laporan')

@section('content')
<div class="page-inner">
    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Daftar Laporan</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#laporanModal">
                        <i class="fa fa-plus"></i> Tambah Laporan
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="laporanTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Keterangan</th>
                                    <th>File</th>
                                    <th>Tanggal Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dummy data --}}
                                <tr>
                                    <td>Approval</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file"></i> Lihat</a>
                                    </td>
                                    <td>06-09-2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>QA/QC</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file"></i> Lihat</a>
                                    </td>
                                    <td>05-09-2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Dokumentasi</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file"></i> Lihat</a>
                                    </td>
                                    <td>04-09-2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
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

{{-- Modal Tambah Laporan --}}
<div class="modal fade" id="laporanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <select id="keterangan" class="form-select" required>
                            <option value="">-- Pilih Keterangan --</option>
                            <option value="Approval">Approval</option>
                            <option value="QA/QC">QA/QC</option>
                            <option value="Dokumentasi">Dokumentasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <input type="file" class="form-control" accept=".pdf,.doc,.docx,image/*">
                    </div>
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#laporanTable').DataTable({
    pageLength: 5,
    responsive: true,
    language: {
        paginate: {
            previous: "Previous",
            next: "Next"
        },
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        search: "_INPUT_",
        searchPlaceholder: "Search...",
        lengthMenu: "Tampilkan _MENU_ data"
    }
});
</script>
@endpush
@endsection