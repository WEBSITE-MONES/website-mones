@extends('Dashboard.base')

@section('title', 'Korespondensi')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Korespondensi</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#uploadKorespondensiModal">
                        <i class="fa fa-plus"></i> Tambah Korespondensi
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="korespondensiTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Persuratan</td>
                                    <td>Surat Permintaan Material</td>
                                    <td>02-09-2025</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fa fa-download"></i> Download
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
                                                        onsubmit="return confirm('Yakin ingin hapus?');">
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
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload --}}
<div class="modal fade" id="uploadKorespondensiModal" tabindex="-1" aria-labelledby="uploadKorespondensiModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadKorespondensiModalLabel">Upload Korespondensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Jenis Korespondensi</label>
                        <select class="form-select">
                            <option value="Persuratan">Persuratan</option>
                            <option value="Berita Acara">Berita Acara</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" placeholder="Masukkan judul dokumen">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <input type="file" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Upload
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#korespondensiTable').DataTable({
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