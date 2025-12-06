@extends('Dashboard.base')
@section('title', 'Dashboard')

@section('content')
<div class="page-inner">

    {{-- Header --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-chart-line me-2 text-primary"></i> Dashboard Investasi Tahun {{ $tahun }}
        </h4>
        <div class="ms-auto">
            <div class="dropdown">
                <button class="btn btn-light border dropdown-toggle" type="button" id="tahunFilter"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-2"></i> Tahun {{ $tahun }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="tahunFilter">
                    @for ($i = now()->year; $i >= 2020; $i--)
                    <li>
                        <a class="dropdown-item tahun-filter {{ $tahun == $i ? 'active' : '' }}" href="#"
                            data-tahun="{{ $i }}">
                            {{ $i }}
                        </a>
                    </li>
                    @endfor
                </ul>
            </div>
        </div>

    </div>

    ---

    {{-- Stats Cards Section --}}
    <div class="row g-4 mb-3">
        {{-- Total Nilai Investasi --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-primary-light text-primary me-3">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Total Nilai Investasi {{ $tahun }}</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">
                                Rp {{ number_format($totalNilaiInvestasi, 0, ',', '.') }}
                            </h4>
                            @if($growthNilaiInvestasi != 0)
                            @if(abs($growthNilaiInvestasi) > 100)
                            <small class="fw-bold {{ $growthNilaiInvestasi >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $growthNilaiInvestasi >= 0 ? 'up' : 'down' }}"></i>
                                {{ $growthNilaiInvestasi >= 0 ? 'Naik' : 'Turun' }}
                                Rp {{ number_format(abs($selisihNilaiInvestasi), 0, ',', '.') }}
                                <br>
                                <span class="text-muted" style="font-size: 0.75rem;">vs {{ $tahunLalu }}</span>
                            </small>
                            @else
                            <small class="fw-bold {{ $growthNilaiInvestasi >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $growthNilaiInvestasi >= 0 ? 'up' : 'down' }}"></i>
                                {{ $growthNilaiInvestasi >= 0 ? '+' : '' }}{{ number_format($growthNilaiInvestasi, 1) }}%
                                vs {{ $tahunLalu }}
                            </small>
                            @endif
                            @else
                            <small class="text-muted">Tidak ada data {{ $tahunLalu }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Jumlah Proyek Card --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-success-light text-success me-3">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Total Proyek {{ $tahun }}</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">{{ $jumlahProyek }} Proyek</h4>
                            @if($selisihProyek != 0)
                            <small class="fw-bold {{ $selisihProyek > 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-{{ $selisihProyek > 0 ? 'plus' : 'minus' }}"></i>
                                {{ abs($selisihProyek) }} {{ $selisihProyek > 0 ? 'proyek baru' : 'berkurang' }}
                            </small>
                            @else
                            <small class="text-muted">Sama dengan tahun {{ $tahunLalu }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Realisasi Anggaran Card --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-warning-light text-warning me-3">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Proyek Berjalan {{ $tahun }}</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">25 Proyek</h4>
                            <small class="text-muted">75% dari total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Proyek Selesai Card --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="icon-stat-modern bg-info-light text-info me-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="card-category text-muted mb-1 small">Proyek Selesai {{ $tahun }}</p>
                            <h4 class="card-title fw-bolder text-dark mb-0">15 Proyek</h4>
                            <small class="text-danger fw-bold"><i class="fas fa-arrow-down"></i> 10%</small>
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

    <div class="row">
        {{-- Kartu Ringkasan Realisasi Anggaran --}}
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card border-0 shadow-sm h-100 card-round">
                <div class="card-header bg-transparent p-3 border-bottom-0">
                    <h5 class="card-title fw-bolder text-dark mb-0">
                        <i class="fas fa-stream me-2 text-primary"></i>
                        Progres Realisasi Anggaran {{ $tahun }}
                    </h5>
                </div>
                <div class="card-body pt-0">
                    {{-- Item PR --}}
                    <div class="realisasi-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark">Perencanaan (PR)</span>
                            <span class="fw-bold text-primary">{{ number_format($persentasePR, 2) }}%</span>
                        </div>
                        <div class="progress rounded-pill mb-1" style="height: 10px;">
                            <div class="progress-bar rounded-pill bg-primary" role="progressbar"
                                style="width: {{ $persentasePR }}%;" aria-valuenow="{{ $persentasePR }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Rp. {{ number_format($totalNilaiPR, 0, ',', '.') }}
                            </small>
                            <a href="#" id="openModalPR" title="Lihat Detail Data PR"
                                class="text-primary text-decoration-none fw-bold">&rarr;</a>
                        </div>
                    </div>

                    {{-- Item PO --}}
                    <div class="realisasi-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark">Anggaran (PO)</span>
                            <span class="fw-bold text-warning">{{ number_format($persentasePO, 2) }}%</span>
                        </div>
                        <div class="progress rounded-pill mb-1" style="height: 10px;">
                            <div class="progress-bar rounded-pill bg-warning" role="progressbar"
                                style="width: {{ $persentasePO }}%;" aria-valuenow="{{ $persentasePO }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Rp. {{ number_format($totalNilaiPO, 0, ',', '.') }}
                            </small>
                            <a href="#" id="openModalPO" title="Lihat Detail Data PO"
                                class="text-warning text-decoration-none fw-bold">&rarr;</a>
                        </div>
                    </div>

                    {{-- Item GR --}}
                    <div class="realisasi-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark">Realisasi (GR)</span>
                            <span class="fw-bold text-info">{{ number_format($persentaseGR, 2) }}%</span>
                        </div>
                        <div class="progress rounded-pill mb-1" style="height: 10px;">
                            <div class="progress-bar rounded-pill bg-info" role="progressbar"
                                style="width: {{ $persentaseGR }}%;" aria-valuenow="{{ $persentaseGR }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Rp. {{ number_format($totalNilaiGR, 0, ',', '.') }}</small>
                            <a href="#" id="openModalGR" title="Lihat Detail Data GR"
                                class="text-info text-decoration-none fw-bold">&rarr;</a>
                        </div>
                    </div>

                    {{-- Item Payment --}}
                    <div class="realisasi-item border-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark">Pembayaran</span>
                            <span class="fw-bold text-success">{{ number_format($persentasePayment, 2) }}%</span>
                        </div>
                        <div class="progress rounded-pill mb-1" style="height: 10px;">
                            <div class="progress-bar rounded-pill bg-success" role="progressbar"
                                style="width: {{ $persentasePayment }}%;" aria-valuenow="{{ $persentasePayment }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Rp. {{ number_format($totalNilaiPayment, 0, ',', '.') }}</small>
                            <a href="#" id="openModalPayment" title="Lihat Detail Data Payment"
                                class="text-success text-decoration-none fw-bold">&rarr;</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Kartu Statistik --}}
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card border-0 shadow-sm h-100 card-round">
                <div
                    class="card-header bg-transparent p-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bolder text-dark mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Statistik Investasi {{ $tahun }}
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-light" title="Export Data">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <button type="button" class="btn btn-light" title="Cetak Grafik">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-4">
                    <div class="chart-container w-100" style="min-height: 350px;">
                        <canvas id="statisticsChart"></canvas>
                    </div>
                    <div id="myChartLegend"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const dataTableOptions = {
        pageLength: 10,
        responsive: false,
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

    // filter tahun
    $(document).on('click', '.tahun-filter', function(e) {
        e.preventDefault();
        let tahun = $(this).data('tahun');

        $.ajax({
            url: "{{ route('dashboard') }}",
            type: "GET",
            data: {
                tahun: tahun
            },
            success: function(response) {
                // ambil bagian konten dashboard (page-inner)
                let newContent = $(response).find('.page-inner').html();
                $('.page-inner').html(newContent);
            },
            error: function() {
                alert("Gagal memuat data tahun " + tahun);
            }
        });
    });
});
</script>
@endpush
<style>
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