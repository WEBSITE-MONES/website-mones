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
                            @if(auth()->user()->wilayah_id == $id)
                            <a href="{{ route('dashboard.kota', ['id' => $id]) }}" class="marker"
                                style="top: {{ $kota['top'] }}; left: {{ $kota['left'] }};"
                                title="{{ $kota['title'] }}"></a>
                            @else
                            <span class="marker disabled" style="top: {{ $kota['top'] }}; left: {{ $kota['left'] }};"
                                title="{{ $kota['title'] }}"></span>
                            @endif
                            @else
                            <!-- superadmin dan user biasa bisa lihat semua -->
                            <a href="{{ route('dashboard.kota', ['id' => $id]) }}" class="marker"
                                style="top: {{ $kota['top'] }}; left: {{ $kota['left'] }};"
                                title="{{ $kota['title'] }}"></a>
                            @endif
                            @endforeach
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