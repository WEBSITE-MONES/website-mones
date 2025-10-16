@extends('Dashboard.base')

@section('title', 'Gambar')

@push('styles')
<style>
.card-body {
    padding: 1.5rem;
}

#gambarTable thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
}

#gambarTable tbody tr {
    transition: background-color 0.2s ease-in-out;
    border-bottom: 1px solid #f0f0f0;
}

#gambarTable tbody tr:last-child {
    border-bottom: none;
}

#gambarTable tbody tr:hover {
    background-color: #f1f1f1;
}

#gambarTable tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
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
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Daftar Gambar — {{ $pekerjaan->nama_pekerjaan ?? 'Pekerjaan' }}</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#uploadModal">
                        <i class="fa fa-plus"></i> Tambah Gambar
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="gambarTable" class="display table table-hover">
                            <thead>
                                <tr>
                                    <th class="action-column">Aksi</th>
                                    <th>Keterangan</th>
                                    <th>File</th>
                                    <th>Tanggal Upload</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pekerjaan->gambars as $gambar)
                                <tr>
                                    <td class="action-column">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow">
                                                @if($gambar->status == 'Pending')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('pekerjaan.data.gambar.approve', [$pekerjaan->id, $gambar->id]) }}">
                                                        <i class="fa fa-check text-success me-2"></i> Approve
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('pekerjaan.data.gambar.reject', [$pekerjaan->id, $gambar->id]) }}">
                                                        <i class="fa fa-times text-warning me-2"></i> Reject
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                @endif
                                                <li>
                                                    <form
                                                        action="{{ route('pekerjaan.data.gambar.destroy', [$pekerjaan->id, $gambar->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus gambar ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash me-2"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>{{ $gambar->keterangan }}</td>
                                    <td>
                                        <a href="{{ asset('uploads/gambar/'.$gambar->file) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="fa fa-file-image"></i> Lihat
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($gambar->tanggal_upload)->format('d-m-Y') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($gambar->status == 'Approved') bg-success 
                                            @elseif($gambar->status == 'Rejected') bg-danger 
                                            @else bg-warning 
                                            @endif">
                                            {{ $gambar->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada gambar diunggah.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pekerjaan.data.gambar.store', $pekerjaan->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <select id="keterangan" name="keterangan" class="form-select" required>
                            <option value="">-- Pilih Keterangan --</option>
                            <option value="DED">DED</option>
                            <option value="Shop Drawing">Shop Drawing</option>
                            <option value="As Built">As Built</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Gambar</label>
                        <input type="file" name="file" class="form-control" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Upload</label>
                        <input type="date" name="tanggal_upload" class="form-control">
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
    $('#gambarTable').DataTable({
        responsive: true,
    });
});
</script>
@endpush