@foreach($reportsByItem as $itemId => $reports)
@php
$item = $reports->first()->pekerjaanItem;
$totalVolume = $reports->sum('volume_realisasi');
$bobotRealisasi = ($totalVolume / $item->volume) * $item->bobot;
@endphp

<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
            {{ $item->kode_pekerjaan }} - {{ $item->jenis_pekerjaan_utama ?? $item->sub_pekerjaan }}
        </h6>
    </div>
    <div class="card-body">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pelapor</th>
                    <th>Volume</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                <tr>
                    <td>{{ $report->tanggal->format('d M Y') }}</td>
                    <td>{{ $report->pelapor->name }}</td>
                    <td class="text-end">{{ number_format($report->volume_realisasi, 2) }}</td>
                    <td>{{ $report->satuan }}</td>
                </tr>
                @endforeach
                <tr class="fw-bold bg-light">
                    <td colspan="2">TOTAL MINGGUAN</td>
                    <td class="text-end">{{ number_format($totalVolume, 2) }}</td>
                    <td>{{ $item->sat }}</td>
                </tr>
                <tr class="fw-bold text-success">
                    <td colspan="3">BOBOT REALISASI</td>
                    <td>{{ number_format($bobotRealisasi, 2) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endforeach