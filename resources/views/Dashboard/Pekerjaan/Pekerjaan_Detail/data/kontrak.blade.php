@extends('Dashboard.base')

@section('title', 'Kontrak')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Daftar Kontrak</h4>
                    @if(auth()->user()->role === 'superadmin')
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#modalKontrak">
                        <i class="fa fa-plus"></i> Tambah Kontrak
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="kontrakTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kontrak</th>
                                    <th>Tanggal</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Contoh data dummy --}}
                                <tr>
                                    <td>1</td>
                                    <td>Kontrak Proyek A</td>
                                    <td>01-09-2024</td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-file"></i> Lihat
                                        </a>
                                    </td>
                                    <td>
                                        <div class="dropdown dropend">
                                            <button class="btn btn-light btn-sm" type="button" id="aksiDropdown1"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="aksiDropdown1">
                                                <li>
                                                    <a href="#" class="dropdown-item">
                                                        <i class="fa fa-edit text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="#" method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-times"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Kontrak Proyek B</td>
                                    <td>15-09-2024</td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-file"></i> Lihat
                                        </a>
                                    </td>
                                    <td>
                                        <div class="dropdown dropend">
                                            <button class="btn btn-light btn-sm" type="button" id="aksiDropdown2"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="aksiDropdown2">
                                                <li>
                                                    <a href="#" class="dropdown-item">
                                                        <i class="fa fa-edit text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="#" method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-times"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> {{-- card-body --}}
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Kontrak --}}
<div class="modal fade" id="modalKontrak" tabindex="-1" aria-labelledby="modalKontrakLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKontrakLabel">Tambah Kontrak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="nama_kontrak" class="form-label">Nama Kontrak</label>
                        <input type="text" id="nama_kontrak" class="form-control" placeholder="Masukkan nama kontrak">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kontrak" class="form-label">Tanggal Kontrak</label>
                        <input type="date" id="tanggal_kontrak" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="file_kontrak" class="form-label">Upload File Kontrak</label>
                        <input type="file" id="file_kontrak" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Format: PDF, Word, JPG, PNG</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">
                    <i class="fa fa-upload me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#kontrakTable').DataTable({
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