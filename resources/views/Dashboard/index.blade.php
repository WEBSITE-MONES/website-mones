@extends('Dashboard.base')
@section('title', 'Dashboard')

@section('content')
<div class="page-inner">

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-chart-line me-2 text-primary"></i> Dashboard
        </h4>
        {{-- Anda bisa menambahkan tombol lain di sini jika diperlukan --}}
    </div>

    ---

    <div class="row">
        {{-- Contoh Info-Card: Anda bisa mengisi data dari backend di sini --}}
        <div class="col-md-3">
            <div class="card card-stats card-round shadow-sm">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="col-stats">
                            <div class="numbers">
                                <p class="card-category text-muted mb-1">Total Nilai Investasi</p>
                                <h4 class="card-title fw-bold">Rp {{ number_format(123456789000, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round shadow-sm">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                        </div>
                        <div class="col-stats">
                            <div class="numbers">
                                <p class="card-category text-muted mb-1">Jumlah Proyek</p>
                                <h4 class="card-title fw-bold">25 Proyek</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round shadow-sm">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                        </div>
                        <div class="col-stats">
                            <div class="numbers">
                                <p class="card-category text-muted mb-1">Realisasi Anggaran</p>
                                <h4 class="card-title fw-bold">75%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round shadow-sm">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col-stats">
                            <div class="numbers">
                                <p class="card-category text-muted mb-1">Proyek Selesai</p>
                                <h4 class="card-title fw-bold">15 Proyek</h4>
                            </div>
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
                <div class="card-header bg-primary text-white p-3 rounded-top-3">
                    <div class="card-head-row">
                        <h4 class="card-title fw-bold">
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

</div>

<style>
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
    font-size: 24px;
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

.marker i.fas.fa-map-marker-alt,
.marker i.fas.fa-map-pin {
    filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.4));
}
</style>
@endsection