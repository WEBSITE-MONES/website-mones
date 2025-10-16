@extends('Dashboard.base')

@section('title', 'Progress Fisik Pekerjaan')

@section('content')
<div class="page-inner">
    {{-- Header --}}
    <div class="page-header">
        <h4 class="page-title">Progress Fisik Pekerjaan</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('dashboard.index') }}"><i class="flaticon-home"></i></a>
            </li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item">
                <a href="{{ route('pekerjaan.index') }}">Pekerjaan</a>
            </li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item">
                <a href="{{ route('pekerjaan.show', $pekerjaan->id) }}">{{ $pekerjaan->nama_investasi }}</a>
            </li>
            <li class="separator"><i class="flaticon-right-arrow"></i></li>
            <li class="nav-item"><a href="#">Progress Fisik</a></li>
        </ul>
        <div class="ms-auto">
            <a href="{{ route('pekerjaan.show', $pekerjaan->id) }}" class="btn btn-light btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('realisasi.editProgress', $po->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit Progress
            </a>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-stats card-round border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <span
                                class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-calendar-check fa-2x"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-semibold">Rencana Kumulatif</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($rencanaPct, 2) }}%</h3>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ min(100, $rencanaPct) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stats card-round border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <span
                                class="d-flex align-items-center justify-content-center rounded-circle bg-success text-white"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-tasks fa-2x"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-semibold">Realisasi Kumulatif</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($realisasiPct, 2) }}%</h3>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ min(100, $realisasiPct) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stats card-round border-{{ $deviasiPct >= 0 ? 'info' : 'danger' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <span
                                class="d-flex align-items-center justify-content-center rounded-circle bg-{{ $deviasiPct >= 0 ? 'info' : 'danger' }} text-white"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-{{ $deviasiPct >= 0 ? 'arrow-up' : 'arrow-down' }} fa-2x"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-semibold">Deviasi</h6>
                            <h3 class="mb-0 fw-bold text-{{ $deviasiPct >= 0 ? 'info' : 'danger' }}">
                                {{ $deviasiPct > 0 ? '+' : '' }}{{ number_format($deviasiPct, 2) }}%
                            </h3>
                        </div>
                    </div>
                    <small class="text-{{ $deviasiPct >= 0 ? 'info' : 'danger' }} d-block mt-2">
                        {{ $deviasiPct >= 0 ? '✓ On Track / Ahead' : '⚠ Behind Schedule' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Kurva S --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-chart-line me-2"></i>Kurva S Progress Pekerjaan
            </h4>
        </div>
        <div class="card-body">
            <div class="card-sub mb-3">
                Grafik ini menampilkan <strong>Progress Fisik Pekerjaan Kumulatif</strong> per minggu,
                membandingkan <strong>Rencana Kumulatif</strong> dengan <strong>Realisasi Kumulatif</strong>.
            </div>
            <div class="chart-container" style="height:400px; width:100%;">
                <canvas id="htmlLegendsChart"></canvas>
            </div>
            <div id="myChartLegend" class="mt-3"></div>
        </div>
    </div>

    {{-- Tabel Progress Bulanan --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-table me-2"></i>Rekap Progress Bulanan
            </h4>
        </div>
        <div class="card-body table-responsive">
            <table id="progressTable" class="display table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Rencana (%)</th>
                        <th>Realisasi (%)</th>
                        <th>Deviasi (%)</th>
                        <th>Rencana Kumulatif (%)</th>
                        <th>Realisasi Kumulatif (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $index => $data)
                    <tr>
                        <td>{{ $data['bulan_label'] }}</td>
                        <td>{{ number_format($data['rencana'], 2) }}</td>
                        <td>{{ number_format($data['realisasi'], 2) }}</td>
                        <td class="text-{{ $data['deviasi'] >= 0 ? 'success' : 'danger' }}">
                            {{ number_format($data['deviasi'], 2) }}
                        </td>
                        <td>{{ number_format($rencanaKumulatifBulanan[$index] ?? 0, 2) }}</td>
                        <td>{{ number_format($realisasiKumulatifBulanan[$index] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#progressTable').DataTable({
        pageLength: 12,
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Tampilkan _MENU_ data"
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById("htmlLegendsChart").getContext("2d");

    var gradientStroke = ctx.createLinearGradient(0, 0, 500, 0);
    gradientStroke.addColorStop(0, "#177dff");
    gradientStroke.addColorStop(1, "#80b6f4");

    var gradientStroke2 = ctx.createLinearGradient(0, 0, 500, 0);
    gradientStroke2.addColorStop(0, "#f3545d");
    gradientStroke2.addColorStop(1, "#ff8990");

    var myHtmlLegendsChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: @json($chartLabels),
            datasets: [{
                    label: "Rencana Kumulatif",
                    borderColor: gradientStroke,
                    pointBackgroundColor: gradientStroke,
                    pointRadius: 4,
                    backgroundColor: "rgba(23,125,255,0.2)",
                    legendColor: "#177dff",
                    fill: true,
                    borderWidth: 3,
                    data: @json($chartRencana)
                },
                {
                    label: "Realisasi Kumulatif",
                    borderColor: gradientStroke2,
                    pointBackgroundColor: gradientStroke2,
                    pointRadius: 4,
                    backgroundColor: "rgba(243,84,93,0.2)",
                    legendColor: "#f3545d",
                    fill: true,
                    borderWidth: 3,
                    data: @json($chartRealisasi)
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        return data.datasets[tooltipItem.datasetIndex].label + ': ' +
                            tooltipItem.yLabel.toFixed(2) + '%';
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        max: 100,
                        callback: function(value) {
                            return value + "%";
                        }
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: "rgba(0,0,0,0.7)"
                    }
                }]
            },
            legendCallback: function(chart) {
                var text = [];
                text.push('<ul class="' + chart.id + '-legend html-legend">');
                for (var i = 0; i < chart.data.datasets.length; i++) {
                    text.push(
                        '<li><span style="background-color:' +
                        chart.data.datasets[i].legendColor +
                        '"></span>' +
                        chart.data.datasets[i].label +
                        '</li>'
                    );
                }
                text.push("</ul>");
                return text.join("");
            }
        }
    });

    document.getElementById("myChartLegend").innerHTML = myHtmlLegendsChart.generateLegend();
});
</script>
@endpush
@endsection