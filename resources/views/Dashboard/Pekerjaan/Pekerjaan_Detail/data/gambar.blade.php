@extends('Dashboard.base')

@section('title', 'Gambar')

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
                    <h4 class="card-title">Daftar Gambar</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#uploadModal">
                        <i class="fa fa-plus"></i> Tambah Gambar
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="gambarTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Keterangan</th>
                                    <th>File</th>
                                    <th>Tanggal Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DED</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file-image"></i>
                                            Lihat</a></td>
                                    <td>06-09-2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shop Drawing</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file-image"></i>
                                            Lihat</a></td>
                                    <td>05-09-2025</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>As Built</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file-image"></i>
                                            Lihat</a></td>
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

{{-- Modal Upload --}}
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload / Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- form dummy --}}
                <form>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <select id="keterangan" class="form-select" required>
                            <option value="">-- Pilih Keterangan --</option>
                            <option value="DED">DED</option>
                            <option value="Shop Drawing">Shop Drawing</option>
                            <option value="As Built">As Built</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Gambar</label>
                        <input type="file" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Atau Ambil Foto</label>
                        <input type="file" class="form-control" accept="image/*" capture="camera">
                        {{-- capture="camera" akan aktifkan kamera kalau dibuka di HP --}}
                    </div>
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#gambarTable').DataTable({
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