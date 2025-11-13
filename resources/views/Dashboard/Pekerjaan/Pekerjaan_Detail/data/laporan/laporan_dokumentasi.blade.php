@extends('Dashboard.base')

@section('title', 'Laporan Dokumentasi Progress Harian')
@section('content')
<div class="page-inner">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1">Laporan Dokumentasi Progress Harian</h3>
            <p class="text-muted mb-0 fs-6">
                <i class="fas fa-building me-2"></i>{{ $pekerjaan->nama_investasi }}
            </p>
        </div>
    </div>
    <div class="summary-cards">
        <div class="summary-card">
            <i class="fas fa-file-alt"></i>
            <h3>{{ $summary['total'] }}</h3>
            <p>Total Laporan</p>
        </div>
        <div class="summary-card pending">
            <i class="fas fa-clock"></i>
            <h3>{{ $summary['pending'] }}</h3>
            <p>Menunggu Approval</p>
        </div>
        <div class="summary-card approved">
            <i class="fas fa-check-circle"></i>
            <h3>{{ $summary['approved'] }}</h3>
            <p>Disetujui</p>
        </div>
        <div class="summary-card rejected">
            <i class="fas fa-times-circle"></i>
            <h3>{{ $summary['rejected'] }}</h3>
            <p>Ditolak</p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i> Daftar Laporan Dokumentasi
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="laporanDokumentasiTable" class="display table table-hover">
                            <thead>
                                <tr>
                                    <th>AKSI</th>
                                    <th>FOTO</th>
                                    <th>TANGGAL</th>
                                    <th>PELAPOR</th>
                                    <th>ITEM PEKERJAAN</th>
                                    <th>VOLUME</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($laporanDokumentasi as $report)
                                <tr>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow">
                                                <li>
                                                    <a href="{{ route('pekerjaan.laporan.dokumentasi.show', ['id' => $pekerjaan->id, 'dailyProgressId' => $report->id]) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-eye text-info"></i> Lihat Detail
                                                    </a>
                                                </li>

                                                @if($report->status_approval === 'pending')
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a href="{{ route('pekerjaan.laporan.dokumentasi.approve', ['id' => $pekerjaan->id, 'dailyProgressId' => $report->id]) }}"
                                                        class="dropdown-item text-success action-approve">
                                                        <i class="fa fa-check"></i> Setujui
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item text-warning action-reject"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $report->id }}">
                                                        <i class="fa fa-times"></i> Tolak
                                                    </button>
                                                </li>
                                                @else
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('pekerjaan.laporan.dokumentasi.revise', ['id' => $pekerjaan->id, 'dailyProgressId' => $report->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-info">
                                                            <i class="fa fa-redo"></i> Kembalikan ke Pending
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif

                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('pekerjaan.laporan.dokumentasi.destroy', ['id' => $pekerjaan->id, 'dailyProgressId' => $report->id]) }}"
                                                        method="POST" class="form-delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item text-danger action-delete">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                    <td>
                                        @if(!empty($report->foto) && count($report->foto) > 0)
                                        <img src="{{ asset($report->foto[0]['url']) }}" alt="Preview"
                                            class="thumbnail-preview"
                                            onclick="showImageModal('{{ asset($report->foto[0]['url']) }}')">
                                        @else
                                        <div class="no-image-placeholder">
                                            <i class="fa fa-image"></i>
                                        </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $report->tanggal->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $report->tanggal->format('H:i') }}</small>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $report->pelapor->name }}</div>
                                        <small
                                            class="text-muted">{{ $report->pelapor->profile->jabatan ?? '-' }}</small>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $report->pekerjaanItem->kode_pekerjaan }}</div>
                                        <small class="text-muted">{{ Str::limit($report->jenis_pekerjaan, 30) }}</small>
                                    </td>

                                    <td>
                                        @if($report->volume_realisasi)
                                        <span class="fw-bold">{{ number_format($report->volume_realisasi, 2) }}</span>
                                        {{ $report->satuan }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($report->status_approval === 'approved')
                                        <span class="badge bg-success">
                                            <i class="fa fa-check"></i> Disetujui
                                        </span>
                                        @if($report->approver)
                                        <small class="text-muted d-block mt-1">oleh
                                            {{ $report->approver->name }}</small>
                                        @endif
                                        @elseif($report->status_approval === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="fa fa-times"></i> Ditolak
                                        </span>
                                        @if($report->rejection_reason)
                                        <small class="text-muted d-block mt-1" title="{{ $report->rejection_reason }}">
                                            {{ Str::limit($report->rejection_reason, 30) }}
                                        </small>
                                        @endif
                                        @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fa fa-clock"></i> Pending
                                        </span>
                                        @endif
                                    </td>
                                </tr>

                                <div class="modal fade" id="rejectModal{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">
                                                    <i class="fa fa-exclamation-triangle"></i> Tolak Dokumentasi
                                                </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form
                                                action="{{ route('pekerjaan.laporan.dokumentasi.reject', ['id' => $pekerjaan->id, 'dailyProgressId' => $report->id]) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            Alasan Penolakan <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea name="rejection_reason" class="form-control" rows="4"
                                                            placeholder="Jelaskan alasan penolakan (min. 10 karakter)"
                                                            required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        Batal
                                                    </button>
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="fa fa-times"></i> Tolak Dokumentasi
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="fa fa-inbox"></i>
                                            <p>Belum ada laporan dokumentasi progress harian.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $laporanDokumentasi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fa fa-image me-2"></i> Preview Foto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="modalImage" src="" alt="Preview" class="img-fluid"
                    style="max-height: 80vh; border-radius: 12px;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var table = $('#laporanDokumentasiTable').DataTable({
        responsive: true,
        order: [
            [2, "desc"]
        ],
        columnDefs: [{
            targets: [0, 1],
            orderable: false,
            searchable: false
        }],

        paging: false,
        info: false,
        searching: true,
        lengthChange: false,

        language: {
            search: "Cari:",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada laporan dokumentasi"
        }
    });
    table.rows().invalidate().draw(false);


    $('#laporanDokumentasiTable').on('click', '.action-approve', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');

        Swal.fire({
            title: 'Setujui Dokumentasi?',
            html: "Dokumentasi akan di-approve dan dihitung ke progress mingguan.<br><small class='text-muted'>Tindakan ini akan mempengaruhi perhitungan progress.</small>",
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-check"></i> Ya, Setujui!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                window.location.href = url;
            }
        });
    });

    $('#laporanDokumentasiTable').on('click', '.action-reject', function(e) {});


    $('#laporanDokumentasiTable').on('click', '.action-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Dokumentasi?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });


    $('#laporanDokumentasiTable').on('submit', 'form[action*="revise"]', function(e) {
        e.preventDefault();
        const form = $(this);

        Swal.fire({
            title: 'Kembalikan ke Pending?',
            text: "Status approval akan dikembalikan menjadi Pending.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-redo"></i> Ya, Kembalikan!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.off('submit').submit();
            }
        });
    });

});

function showImageModal(imageUrl) {
    $('#modalImage').attr('src', imageUrl);

    // Gunakan Bootstrap 5 Modal API
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

$(document).ready(function() {
    $('form').on('submit', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush