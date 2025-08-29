@extends('Dashboard.base')
@section('title', 'Dashboard')

@section('content')

<div class="page-inner">
    <div class="row">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <h4 class="card-title">Lokasi Investasi</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="map-container" style="position: relative; width: 100%; max-width: 1200px;">
                            <!-- Gambar peta -->
                            <img src="{{ asset('assets/img/Wilayah Pelindo.png') }}" alt="Peta Pelindo"
                                style="width: 100%;">

                            <!-- Regional IV -->
                            <!-- Pulau Kalimantan -->
                            <!-- Nunukan -->
                            <a href="#" class="marker" style="top: 25%; left: 49%;" title="Nunukan"></a>
                            <!-- Tarakan -->
                            <a href="#" class="marker" style="top: 27.5%; left: 48.4%" title="Tarakan"></a>
                            <!-- Tanjung Redep -->
                            <a href="#" class="marker" style="top: 32.2%; left: 48.7%" title="Tanjung Redep"></a>
                            <!-- Sangatta -->
                            <a href="#" class="marker" style="top: 38.7%; left: 49.3%" title="Sangatta"></a>
                            <!-- Bontang -->
                            <a href="#" class="marker" style="top: 40.5%; left: 49%" title="Bontang"></a>
                            <!-- Samarinda -->
                            <a href="#" class="marker" style="top: 45.6%; left: 48.5%" title="Samarinda"></a>
                            <!-- Balikpapan -->
                            <a href="#" class="marker" style="top: 48.3%; left: 47.8%" title="Balikpapan"></a>

                            <!-- Pulau Sulawesi -->
                            <!-- Tolitoli -->
                            <a href="#" class="marker" style="top: 37.2%; left: 55.3%" title="Tolitoli"></a>
                            <!-- Gorontalo -->
                            <a href="#" class="marker" style="top: 40.5%; left: 59.9%" title="Gorontalo"></a>
                            <!-- Manado -->
                            <a href="#" class="marker" style="top: 34.9%; left: 63.4%" title="Manado"></a>
                            <!-- Likupang -->
                            <a href="#" class="marker" style="top: 34.3%; left: 63.9%" title="Likupang"></a>
                            <!-- Bitung -->
                            <a href="#" class="marker" style="top: 34.9%; left: 64.4%" title="Bitung"></a>
                            <!-- Pantoloan -->
                            <a href="#" class="marker" style="top: 44.3%; left: 53.3%" title="Pantoloan"></a>
                            <!-- Pare Pare -->
                            <a href="#" class="marker" style="top: 61.8%; left: 53.1%" title="Pare-pare"></a>
                            <!-- Kendari -->
                            <a href="#" class="marker" style="top: 60.7%; left: 59.1%" title="Kendari"></a>

                            <!-- NTT atau NTB -->
                            <!-- Maumere -->
                            <a href="#" class="marker" style="top: 82.6%; left: 58.5%" title="Maumere"></a>
                            <!-- Ende -->
                            <a href="#" class="marker" style="top: 85.3%; left: 56.2%" title="Ende"></a>
                            <!-- Waingapu -->
                            <a href="#" class="marker" style="top: 87.1%; left: 54.6%" title="Waingapu"></a>
                            <!-- Tenau Kupang -->
                            <a href="#" class="marker" style="top: 89.4%; left: 61%" title="Tenau Kupang"></a>
                            <!-- Kalabahi -->
                            <a href="#" class="marker" style="top: 80.8%; left: 63.3%" title="Kalabahi"></a>

                            <!--  -->
                            <!-- Sorong  -->
                            <a href="#" class="marker" style="top: 45.8%; left: 76.5%" title="Sorong"></a>
                            <!-- Manokwari -->
                            <a href="#" class="marker" style="top: 45.8%; left: 82.5%" title="Manokwari"></a>
                            <!-- Biak -->
                            <a href="#" class="marker" style="top: 46.2%; left: 86.3%" title="Biak"></a>
                            <!-- Jayapura -->
                            <a href="#" class="marker" style="top: 53%; left: 95.5%" title="Jayapura"></a>
                            <!-- Merauke -->
                            <a href="#" class="marker" style="top: 83.7%; left: 95.2%" title="Merauke"></a>
                            <!-- Ternate -->
                            <a href="#" class="marker" style="top: 38%; left: 68.2%" title="Ternate"></a>
                            <a href="#" class="marker" style="top: 56%; left: 78.7%" title="Fakfak"></a>

                            <!-- Kordinat -->
                            <!-- Ambon -->
                            <a href="{{ route('dashboard.kota') }}" class="marker" style="top: 58.2%; left: 70%"
                                title="Ambon"></a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <h4 class="card-title">Statistik Investasi</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection