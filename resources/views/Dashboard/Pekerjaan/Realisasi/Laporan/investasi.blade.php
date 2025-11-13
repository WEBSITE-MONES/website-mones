@extends('Dashboard.base')

@section('title', 'Laporan Investasi')

@push('styles')
<style>
.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
}

.action-buttons .btn {
    white-space: nowrap;
}
</style>
@endpush

@section('content')
<div class="page-inner">
    {{-- HEADER HALAMAN --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Laporan Investasi</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('laporan.index') }}">Laporan Investasi</a>
                </li>
            </ul>
        </div>
        <div class="d-flex gap-2">
            @can('role', ['admin', 'superadmin'])
            <a href="{{ route('laporan.approval-settings.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fa fa-cog"></i> Pengaturan Approval
            </a>
            @endcan
            <a href="{{ route('laporan.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fa fa-plus"></i> Buat Laporan Baru
            </a>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- KONTEN UTAMA --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    {{-- HEADER FILTER --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-2 mb-md-0">Daftar Laporan Investasi</h5>

                        <div class="d-flex gap-2">
                            {{-- Dropdown Jenis Laporan --}}
                            <div class="input-group" style="width: 240px;">
                                <span class="input-group-text bg-light"><i class="fa fa-file-alt"></i></span>
                                <select class="form-select" id="jenisLaporan" name="jenis">
                                    <option value="rekap_activa" {{ $jenis == 'rekap_activa' ? 'selected' : '' }}>
                                        Laporan (Rekap Activa)</option>
                                    <option value="rekap_rincian" {{ $jenis == 'rekap_rincian' ? 'selected' : '' }}>
                                        Laporan (Rekap Rincian)</option>
                                </select>
                            </div>

                            {{-- Dropdown Tahun --}}
                            <div class="input-group" style="width: 150px;">
                                <span class="input-group-text bg-light"><i class="fa fa-filter"></i></span>
                                <select class="form-select" id="tahunFilter" name="tahun">
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL --}}
                    <div class="table-responsive">
                        <table id="tabelLaporan" class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th style="width: 250px;">Action</th>
                                    <th>Kode Laporan</th>
                                    <th>Periode</th>
                                    <th>Jenis Laporan</th>
                                    <th>Status Approval</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporan as $index => $item)
                                <tr>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('laporan.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                data-bs-toggle="tooltip" title="Lihat Detail">
                                                <i class="fa fa-eye"></i> Lihat
                                            </a>

                                            @if($item->status_approval !== 'approved')
                                            <form action="{{ route('laporan.destroy', $item->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                    data-bs-toggle="tooltip" title="Hapus Laporan">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                            @else
                                            <span class="badge bg-success" data-bs-toggle="tooltip"
                                                title="Laporan yang sudah approved tidak dapat dihapus">
                                                <i class="fa fa-lock"></i> Locked
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td><strong>{{ $item->kode_laporan }}</strong></td>
                                    <td>{{ $item->periode_label }}</td>
                                    <td>
                                        <span class="badge bg-info text-white">
                                            {{ $item->jenis_laporan == 'rekap_activa' ? 'Rekap Activa' : 'Rekap Rincian' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{!! $item->status_badge !!}</td>
                                    <td class="text-center">{{ $item->tanggal_dibuat->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa fa-inbox fa-2x mb-2"></i>
                                        <p>Belum ada laporan untuk filter yang dipilih</p>
                                    </td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // DataTable
    const dataTableOptions = {
        responsive: true,
        ordering: true,
        order: [
            [5, 'desc']
        ],
        language: {
            paginate: {
                previous: "<i>Previous</i>",
                next: "<i>Next</i>"
            },
            search: "_INPUT_",
            searchPlaceholder: "Cari data...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari _MAX_ total data)"
        }
    };
    $('#tabelLaporan').DataTable(dataTableOptions);

    // Event filter
    $('#jenisLaporan, #tahunFilter').on('change', function() {
        const jenis = $('#jenisLaporan').val();
        const tahun = $('#tahunFilter').val();
        window.location.href = `{{ route('laporan.index') }}?jenis=${jenis}&tahun=${tahun}`;
    });

    // Confirm delete
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `
                <div class="text-start">
                    <p class="mb-2">Anda yakin ingin menghapus laporan ini?</p>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Data laporan akan dihapus permanen</li>
                            <li>Semua detail dan approval akan ikut terhapus</li>
                            <li>Aksi ini tidak dapat dibatalkan</li>
                        </ul>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
            cancelButtonText: '<i class="fa fa-times"></i> Batal',
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection