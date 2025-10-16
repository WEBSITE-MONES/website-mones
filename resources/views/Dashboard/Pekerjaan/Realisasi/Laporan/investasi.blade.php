@extends('Dashboard.base')

@section('title', 'Laporan Investasi')

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
    </div>

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
                                <select class="form-select" id="jenisLaporan">
                                    <option value="rekap_activa">Laporan (Rekap Activa)</option>
                                    <option value="rekap_rincian">Laporan (Rekap Rincian)</option>
                                </select>
                            </div>

                            {{-- Dropdown Tahun --}}
                            <div class="input-group" style="width: 150px;">
                                <span class="input-group-text bg-light"><i class="fa fa-filter"></i></span>
                                <select class="form-select" id="tahunFilter">
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
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
                                    <th style="width: 120px;">Action</th>
                                    <th>Periode</th>
                                    <th>Status Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fa fa-list"></i> Lihat Laporan
                                        </a>
                                    </td>
                                    <td>Laporan s.d Januari</td>
                                    <td><span class="badge bg-success">Approved</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fa fa-list"></i> Lihat Laporan
                                        </a>
                                    </td>
                                    <td>Laporan s.d Februari</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT DATATABLES --}}
@push('scripts')
<script>
$(document).ready(function() {
    const dataTableOptions = {
        responsive: true,
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
        console.log("Filter berubah:", jenis, tahun);
        // TODO: panggil AJAX/filter data di sini nanti
    });
});
</script>
@endpush
@endsection