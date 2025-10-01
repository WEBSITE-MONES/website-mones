@extends('Dashboard.base')
@section('title', 'Dashboard')

@section('content')
<div class="page-inner">

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-chart-line me-2 text-primary"></i> Dashboard
        </h4>
    </div>

    ---

    {{-- Stats Cards Section: Cleaner, more defined cards --}}
    <div class="row g-4 mb-3">
        {{-- Total Nilai Investasi Card (Primary Color Focus) --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-primary-light text-primary me-3">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Total Nilai Investasi</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">
                                Rp {{ number_format($totalNilaiInvestasi, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Jumlah Proyek Card --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-success-light text-success me-3">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Jumlah Proyek</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">{{ $jumlahProyek }} Proyek</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Realisasi Anggaran Card --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-warning-light text-warning me-3">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Realisasi Anggaran</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">75%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Proyek Selesai Card --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-info-light text-info me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Proyek Selesai</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">15 Proyek</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    ---

    <div class="row">
        <div class="col-md-12">
            <div class="card card-round shadow-lg">
                <div class="card-header  text-white p-3 rounded-top-3">

                    <div class="card-header bg-light-gray p-4 border-bottom-0">
                        <h4 class="card-title fw-bolder text-dark mb-0">
                            <i class="fas fa-map-marked-alt me-2"></i> Lokasi Investasi
                        </h4>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="map-container position-relative overflow-hidden" style="width: 100%;">
                        <img src="{{ asset('assets/img/Wilayah Pelindo.png') }}" alt="Peta Pelindo" class="img-fluid"
                            style="width: 100%;">

                        {{-- Logika PHP dan koordinat dipertahankan --}}
                        @php
                        $kotas = [
                        1 => ['title' => 'Ambon', 'top' => '58.2%', 'left' => '70%'],
                        2 => ['title' => 'Makassar', 'top' => '65.3%', 'left' => '52.6%'],
                        3 => ['title' => 'Nunukan', 'top' => '25%', 'left' => '49%'],
                        4 => ['title' => 'Tarakan', 'top' => '27.5%', 'left' => '48.4%'],
                        5 => ['title' => 'Tanjung Redep', 'top' => '32.2%', 'left' => '48.7%'],
                        6 => ['title' => 'Sangatta', 'top' => '38.7%', 'left' => '49.3%'],
                        7 => ['title' => 'Bontang', 'top' => '40.5%', 'left' => '49%'],
                        8 => ['title' => 'Samarinda', 'top' => '45.6%', 'left' => '48.5%'],
                        9 => ['title' => 'Balikpapan', 'top' => '48.3%', 'left' => '47.8%'],
                        10 => ['title' => 'Tolitoli', 'top' => '37.2%', 'left' => '55.3%'],
                        11 => ['title' => 'Gorontalo', 'top' => '40.5%', 'left' => '59.9%'],
                        12 => ['title' => 'Manado', 'top' => '34.9%', 'left' => '63.4%'],
                        13 => ['title' => 'Likupang', 'top' => '34.3%', 'left' => '63.9%'],
                        14 => ['title' => 'Bitung', 'top' => '34.9%', 'left' => '64.4%'],
                        15 => ['title' => 'Pantoloan', 'top' => '44.3%', 'left' => '53.3%'],
                        16 => ['title' => 'Pare-pare', 'top' => '61.8%', 'left' => '53.1%'],
                        17 => ['title' => 'Kendari', 'top' => '60.7%', 'left' => '59.1%'],
                        18 => ['title' => 'Maumere', 'top' => '82.6%', 'left' => '58.5%'],
                        19 => ['title' => 'Ende', 'top' => '85.3%', 'left' => '56.2%'],
                        20 => ['title' => 'Waingapu', 'top' => '87.1%', 'left' => '54.6%'],
                        21 => ['title' => 'Tenau Kupang', 'top' => '89.4%', 'left' => '61%'],
                        22 => ['title' => 'Kalabahi', 'top' => '80.8%', 'left' => '63.3%'],
                        23 => ['title' => 'Sorong', 'top' => '45.8%', 'left' => '76.5%'],
                        24 => ['title' => 'Manokwari', 'top' => '45.8%', 'left' => '82.5%'],
                        25 => ['title' => 'Biak', 'top' => '46.2%', 'left' => '86.3%'],
                        26 => ['title' => 'Jayapura', 'top' => '53%', 'left' => '95.5%'],
                        27 => ['title' => 'Merauke', 'top' => '83.7%', 'left' => '95.2%'],
                        28 => ['title' => 'Ternate', 'top' => '38%', 'left' => '68.2%'],
                        29 => ['title' => 'Fakfak', 'top' => '56%', 'left' => '78.7%'],
                        ];
                        @endphp

                        @foreach($kotas as $id => $kota)
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('dashboard.kota', ['id' => $id]) }}"
                            class="marker {{ auth()->user()->wilayah_id == $id ? '' : 'disabled' }}"
                            style="top: {{ $kota['top'] }}; left: {{ $kota['left'] }};" title="{{ $kota['title'] }}">
                            @if(auth()->user()->wilayah_id == $id)
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            @else
                            <i class="fas fa-map-pin text-secondary"></i>
                            @endif
                        </a>
                        @else
                        <a href="{{ route('dashboard.kota', ['id' => $id]) }}" class="marker"
                            style="top: {{ $kota['top'] }}; left: {{ $kota['left'] }};" title="{{ $kota['title'] }}">
                            <i></i>
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    ---

    {{-- Data Table Section: Simple card for the table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-light-gray p-4 border-bottom-0">
                    <h4 class="card-title fw-bolder text-dark mb-0">
                        <i class="fas fa-database me-2"></i> Database Pekerjaan
                    </h4>
                </div>
                <div class="card-body p-4">
                    {{-- Table content goes here --}}
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tabelDatabase" class="display table table-striped table-hover">

                                        <thead>
                                            <tr>
                                                <th>Nama Investasi</th>
                                                <th>Tahun Usulan</th>
                                                <th>COA</th>
                                                <th>Program Investasi</th>
                                                <th>Tipe Investasi</th>
                                                <th>Kategori Investasi</th>
                                                <th>Manfaat Investasi</th>
                                                <th>Jenis Investasi</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Nama Investasi</th>
                                                <th>Tahun Usulan</th>
                                                <th>COA</th>
                                                <th>Program Investasi</th>
                                                <th>Tipe Investasi</th>
                                                <th>Kategori Investasi</th>
                                                <th>Manfaat Investasi</th>
                                                <th>Jenis Investasi</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            @foreach($pekerjaans as $pk)
                                            <tr>
                                                <td>{{ $pk->nama_investasi }}</td>
                                                <td>{{ $pk->tahun_usulan }}</td>
                                                <td>{{ $pk->coa }}</td>
                                                <td>{{ $pk->program_investasi }}</td>
                                                <td>{{ $pk->tipe_investasi }}</td>
                                                <td>{{ $pk->masterInvestasi->kategori ?? '-' }}</td>
                                                <td>{{ $pk->masterInvestasi->manfaat ?? '-' }}</td>
                                                <td>{{ $pk->masterInvestasi->jenis ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Pengaturan ini tetap sama, karena sudah bagus
    const dataTableOptions = {
        pageLength: 10,
        responsive: false,
        // scrollX: true,
        language: {
            paginate: {
                previous: "Previous",
                next: "Next"
            },
            search: "_INPUT_",
            searchPlaceholder: "Pencarian...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data tersedia",
            infoFiltered: "(disaring dari _MAX_ total data)"
        }
    };

    $('#tabelDatabase').DataTable(dataTableOptions);

});
</script>
@endpush
<style>
/* Custom Styles for Modern Look */

/* Primary color variation (assuming Bootstrap utility classes are available) */
.text-primary-light {
    color: #5B9AFF !important;
}

.bg-primary-light {
    background-color: #E6F0FF !important;
}

.bg-success-light {
    background-color: #E6FFEB !important;
}

.bg-warning-light {
    background-color: #FFF8E6 !important;
}

.bg-info-light {
    background-color: #E6FAFF !important;
}

.bg-light-gray {
    background-color: #f8f9fa !important;
}

/* Icon Container for Stats Cards */
.icon-stat-modern {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    /* Soft rounded corners */
    font-size: 1.5rem;
}

.map-container {
    padding-bottom: 56.25%;
    /* 16:9 Aspect Ratio */
    position: relative;
    height: 0;
}

.map-container img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.marker {
    position: absolute;
    transform: translate(-50%, -50%);
    transition: transform 0.2s ease-in-out, opacity 0.2s ease-in-out;
    font-size: 100px;
    /* Ukuran ikon */
    text-decoration: none;
    cursor: pointer;
}

.marker:hover {
    transform: translate(-50%, -50%) scale(1.3);
}

.marker.disabled {
    pointer-events: none;
    cursor: default;
    opacity: 0.4;
}
</style>
@endsection