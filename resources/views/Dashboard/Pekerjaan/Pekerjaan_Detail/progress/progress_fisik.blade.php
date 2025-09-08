@extends('Dashboard.base')

@section('title', 'Progress Bulanan')

@section('content')
<div class="page-inner">

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Grafik --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4>Progress Pekerjaan</h4>
        </div>
        <div class="card-body">
            <div class="card-sub">
                Grafik ini menampilkan <strong>Progress Fisik Pekerjaan Kumulatif</strong> per bulan,
                membandingkan <strong>Rencana Kumulatif</strong> dengan <strong>Realisasi Kumulatif</strong>.
            </div>
            <div class="chart-container" style="height:400px; width:100%;">
                <canvas id="htmlLegendsChart"></canvas>
            </div>
            <div id="myChartLegend" class="mt-3"></div>
        </div>
    </div>

    {{-- Tabel Progress --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center">
            <h4 class="card-title">Data Progress Bulanan</h4>
            <a href="{{ route('pekerjaan.progress.create', $pekerjaan->id) }}"
                class="btn btn-primary btn-round ms-auto">
                <i class="fa fa-plus"></i> Input Progress Bulanan
            </a>
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($progress as $index => $p)
                    <tr>
                        <td data-order="{{ $p->bulan }}">
                            {{ \Carbon\Carbon::parse($p->bulan.'-01')->format('M Y') }}
                        </td>
                        <td>{{ number_format($p->rencana ?? 0,2) }}</td>
                        <td>{{ number_format($p->realisasi ?? 0,2) }}</td>
                        <td>{{ number_format($p->defiasi ?? 0,2) }}</td>
                        <td>{{ number_format($rencanaKumulatif[$index] ?? 0,2) }}</td>
                        <td>{{ number_format($realisasiKumulatif[$index] ?? 0,2) }}</td>
                        <td>
                            <div class="dropdown dropend">
                                <button class="btn btn-light btn-sm" type="button" id="aksiDropdown{{ $p->id }}"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="aksiDropdown{{ $p->id }}">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('pekerjaan.progress.edit', ['id' => $pekerjaan->id, 'progress' => $p->id]) }}">
                                            <i class="fa fa-edit text-primary me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form
                                            action="{{ route('pekerjaan.progress.destroy', ['id' => $pekerjaan->id, 'progress' => $p->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus progress ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa fa-times"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada progress</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
$('#progressTable').DataTable({
    pageLength: 5,
    responsive: true,
    order: [
        [0, 'asc']
    ], // urutkan kolom pertama (bulan) ASC
    language: {
        paginate: {
            previous: "Previous",
            next: "Next"
        },
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        search: "_INPUT_",
        searchPlaceholder: "Search...",
        lengthMenu: "Tampilkan _MENU_ data"
    },
    columnDefs: [{
        orderable: false,
        targets: -1
    }]
});
</script>

{{-- Chart KaiAdmin HTML Legend --}}
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById("htmlLegendsChart").getContext("2d");

    // Gradient
    var gradientStroke = ctx.createLinearGradient(0, 0, 500, 0);
    gradientStroke.addColorStop(0, "#177dff");
    gradientStroke.addColorStop(1, "#80b6f4");

    var gradientStroke2 = ctx.createLinearGradient(0, 0, 500, 0);
    gradientStroke2.addColorStop(0, "#f3545d");
    gradientStroke2.addColorStop(1, "#ff8990");

    // Chart
    var myHtmlLegendsChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: @json($labels),
            datasets: [{
                    label: "Rencana Kumulatif",
                    borderColor: gradientStroke,
                    pointBackgroundColor: gradientStroke,
                    pointRadius: 3,
                    backgroundColor: "rgba(23,125,255,0.2)",
                    legendColor: "#177dff",
                    fill: true,
                    borderWidth: 2,
                    data: @json($rencanaKumulatif)
                },
                {
                    label: "Realisasi Kumulatif",
                    borderColor: gradientStroke2,
                    pointBackgroundColor: gradientStroke2,
                    pointRadius: 3,
                    backgroundColor: "rgba(243,84,93,0.2)",
                    legendColor: "#f3545d",
                    fill: true,
                    borderWidth: 2,
                    data: @json($realisasiKumulatif)
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
                intersect: false
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

    // Render HTML Legend
    document.getElementById("myChartLegend").innerHTML = myHtmlLegendsChart.generateLegend();
});
</script>
@endsection