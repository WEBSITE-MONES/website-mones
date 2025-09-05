@extends('Dashboard.base')

@section('title', 'Progress Fisik Pekerjaan')

@section('content')
<div class="page-inner">

    {{-- Alert jika berhasil --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Grafik --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4>Grafik Progress Kumulatif</h4>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height:400px; width:100%;">
                <canvas id="multipleLineChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Tabel Data Progress --}}
    <div class="card">
        <div class="card-header">
            <h4>Data Progress Bulanan & Kumulatif</h4>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-success">
                    <tr>
                        <th>URAIAN</th>
                        @foreach($labels as $label)
                        <th>{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- Rencana Kumulatif --}}
                    <tr>
                        <td><strong>RENCANA KOMULATIF</strong></td>
                        @foreach($rencanaKumulatif as $val)
                        <td>{{ number_format($val, 2) }}%</td>
                        @endforeach
                    </tr>

                    {{-- Realisasi Kumulatif --}}
                    <tr>
                        <td><strong>REALISASI KOMULATIF</strong></td>
                        @foreach($realisasiKumulatif as $val)
                        <td>{{ number_format($val, 2) }}%</td>
                        @endforeach
                    </tr>

                    {{-- Deviasi Kumulatif --}}
                    <tr>
                        <td><strong>DEVIASI KOMULATIF</strong></td>
                        @foreach($rencanaKumulatif as $i => $r)
                        @php
                        $def = $realisasiKumulatif[$i] - $r;
                        @endphp
                        <td>{{ number_format($def, 2) }}%</td>
                        @endforeach
                    </tr>

                    {{-- Rencana Bulanan --}}
                    <tr>
                        <td><strong>RENCANA</strong></td>
                        @foreach($rencana as $val)
                        <td>{{ number_format($val, 2) }}%</td>
                        @endforeach
                    </tr>

                    {{-- Realisasi Bulanan --}}
                    <tr>
                        <td><strong>REALISASI</strong></td>
                        @foreach($realisasi as $val)
                        <td>{{ number_format($val, 2) }}%</td>
                        @endforeach
                    </tr>

                    {{-- Deviasi Bulanan --}}
                    <tr>
                        <td><strong>DEVIASI</strong></td>
                        @foreach($progress as $p)
                        <td>{{ number_format($p->defiasi, 2) }}%</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Input --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4>Input Progress Bulanan</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('pekerjaan.progress.store', $pekerjaan->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label>Bulan</label>
                        <input type="month" name="bulan" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Rencana (%)</label>
                        <input type="number" step="0.01" name="rencana" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>Realisasi (%)</label>
                        <input type="number" step="0.01" name="realisasi" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mt-4">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- Kirim data ke JavaScript (langsung kumulatif dari Controller) --}}
<script>
window.chartData = {
    labels: @json($labels),
    rencana: @json($rencanaKumulatif),
    realisasi: @json($realisasiKumulatif)
}
</script>

{{-- Load Chart.js & plugin datalabels --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

{{-- Script untuk render grafik --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    if (window.chartData) {
        const ctx = document.getElementById('multipleLineChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.chartData.labels,
                datasets: [{
                        label: 'Rencana Kumulatif',
                        data: window.chartData.rencana,
                        borderColor: '#007bff',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Realisasi Kumulatif',
                        data: window.chartData.realisasi,
                        borderColor: '#ff7f0e',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    datalabels: {
                        align: 'top',
                        anchor: 'end',
                        formatter: (val) => val.toFixed(2) + '%'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 5,
                            callback: function(value) {
                                return value.toFixed(2) + '%';
                            }
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }
});
</script>
@endsection