@extends('Dashboard.base')

@section('title', 'Laporan Approval')

@push('styles')
<style>
/* Gaya CSS Anda tetap sama, tidak perlu diubah */
.card-body {
    padding: 1.5rem;
}

#laporanApprovalTable thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
}

#laporanApprovalTable tbody tr {
    transition: background-color 0.2s ease-in-out;
    border-bottom: 1px solid #f0f0f0;
}

#laporanApprovalTable tbody tr:last-child {
    border-bottom: none;
}

#laporanApprovalTable tbody tr:hover {
    background-color: #f1f1f1;
}

#laporanApprovalTable tbody td {
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
                        Laporan Approval: <span class="fw-bold">{{ $pekerjaan->nama_pekerjaan }}</span>
                    </h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#laporanModal">
                        <i class="fa fa-plus"></i> Tambah Laporan Approval
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="laporanApprovalTable" class="display table table-hover">
                            <thead>
                                <tr>
                                    <th class="action-column">AKSI</th>
                                    <th>KETERANGAN</th>
                                    <th>FILE</th>
                                    <th>TANGGAL UPLOAD</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($laporans as $laporan)
                                <tr>
                                    <td class="action-column">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow">
                                                @if ($laporan->status == 'Menunggu')
                                                <li>
                                                    {{-- MODIFIED: Menghapus onclick dan menambahkan class --}}
                                                    <a href="{{ route('pekerjaan.laporan.approval.approve', ['id' => $pekerjaan->id, 'laporan' => $laporan->id]) }}"
                                                        class="dropdown-item text-success action-approve">
                                                        <i class="fa fa-check me-2"></i> Setujui
                                                    </a>
                                                </li>
                                                <li>
                                                    {{-- MODIFIED: Menghapus onclick dan menambahkan class --}}
                                                    <a href="{{ route('pekerjaan.laporan.approval.reject', ['id' => $pekerjaan->id, 'laporan' => $laporan->id]) }}"
                                                        class="dropdown-item text-warning action-reject">
                                                        <i class="fa fa-times me-2"></i> Tolak
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                @endif
                                                <li>
                                                    <form
                                                        action="{{ route('pekerjaan.laporan.approval.destroy', ['id' => $pekerjaan->id, 'laporan' => $laporan->id]) }}"
                                                        method="POST" class="form-delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        {{-- MODIFIED: Menghapus onclick dan menambahkan class --}}
                                                        <button type="submit"
                                                            class="dropdown-item text-danger action-delete">
                                                            <i class="fa fa-trash me-2"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="keterangan-utama">{{ $laporan->keterangan }}</div>
                                        <div class="keterangan-sub">Oleh: {{ $laporan->user->name ?? 'Admin' }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ asset('uploads/laporan/' . $laporan->file_laporan) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-file"></i> Lihat File
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($laporan->tanggal_upload)->translatedFormat('d F Y') }}
                                    </td>
                                    <td>
                                        @if($laporan->status == 'Disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                        @elseif($laporan->status == 'Ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                        @else
                                        <span class="badge bg-warning">Menunggu</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada laporan approval.</td>
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

{{-- Modal Tambah Laporan (Tidak ada perubahan) --}}
<div class="modal fade" id="laporanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Laporan Approval Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pekerjaan.laporan.approval.store', $pekerjaan->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                            placeholder="Contoh: Laporan persetujuan material" required>
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

@push('scripts')
{{-- NEW: Tambahkan library SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#laporanApprovalTable').DataTable({
        responsive: true,
    });

    // NEW: SweetAlert for Approve Action
    $('#laporanApprovalTable').on('click', '.action-approve', function(e) {
        e.preventDefault(); // Mencegah link langsung dieksekusi
        const url = $(this).attr('href');

        Swal.fire({
            title: 'Anda Yakin?',
            text: "Laporan ini akan disetujui.",
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url; // Arahkan ke URL jika dikonfirmasi
            }
        });
    });

    // NEW: SweetAlert for Reject Action
    $('#laporanApprovalTable').on('click', '.action-reject', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            title: 'Anda Yakin?',
            text: "Laporan ini akan ditolak.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // NEW: SweetAlert for Delete Action
    $('#laporanApprovalTable').on('click', '.action-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form'); // Dapatkan form terdekat

        Swal.fire({
            title: 'Anda Yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit form jika dikonfirmasi
            }
        });
    });
});
</script>
@endpush