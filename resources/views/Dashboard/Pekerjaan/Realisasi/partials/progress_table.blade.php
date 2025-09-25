<!-- {{-- Wrapper untuk kontrol DataTables --}}
<div class="p-4">
    <div class="row">
        {{-- Placeholder untuk "Show Entries" akan muncul di sini (di-generate oleh JS) --}}
        <div class="col-sm-12 col-md-6" id="rekapTable_length_placeholder"></div>
        {{-- Placeholder untuk "Search" akan muncul di sini (di-generate oleh JS) --}}
        <div class="col-sm-12 col-md-6" id="rekapTable_filter_placeholder"></div>
    </div>
</div> -->
<div class="table-responsive">
    <table id="rekapTable" class="table table-hover table-striped table-bordered align-middle mb-0"
        style="min-width:1800px;">
        <thead class="text-center text-white sticky-top" style="background-color:#003c73; top: -1px;">
            @php
            $totalDynamicColspan = ($masterMinggu && $masterMinggu->count() > 0) ? $masterMinggu->count() * 2 : 1;
            @endphp

            {{-- Baris 1: Header Utama --}}
            <tr>
                <th rowspan="4" class="align-middle" style="width:50px;">No</th>
                <th rowspan="4" class="align-middle" style="width:200px;">Jenis Pekerjaan</th>
                <th rowspan="4" class="align-middle" style="width:80px;">Volume</th>
                <th rowspan="4" class="align-middle" style="width:70px;">Sat</th>
                <th rowspan="4" class="align-middle" style="width:120px;">Harga Satuan (Rp)</th>
                <th rowspan="4" class="align-middle" style="width:150px;">Jumlah Harga (Rp)</th>
                <th rowspan="4" class="align-middle" style="width:100px;">Bobot(%)</th>
                <th rowspan="4" class="align-middle" style="width:120px;">Volume Realisasi</th>
                <th colspan="{{ $totalDynamicColspan }}">JADWAL PELAKSANAAN PEKERJAAN</th>
            </tr>

            {{-- Baris 2: Bulan --}}
            <tr>
                @foreach ($monthMap as $monthName => $data)
                <th colspan="{{ count($data['minggus']) * 2 }}" class="bg-primary">{{ $monthName }}</th>
                @endforeach
            </tr>

            {{-- Baris 3: Minggu --}}
            <tr>
                @foreach ($monthMap as $data)
                @foreach ($data['minggus'] as $minggu)
                <th colspan="2" class="bg-info text-dark">{{ $minggu->kode_minggu }}</th>
                @endforeach
                @endforeach
            </tr>

            {{-- Baris 4: Detail per minggu --}}
            <tr>
                @foreach ($monthMap as $data)
                @foreach ($data['minggus'] as $minggu)
                <th class="bg-warning text-dark small">Rencana (%)</th>
                <th class="bg-success small">Realisasi (%)</th>
                @endforeach
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php $GLOBALS['rowNo'] = 0; @endphp
            @foreach ($items as $item)
            @include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', [
            'item' => $item,
            'level' => 0,
            'po' => $po,
            'masterMinggu' => $masterMinggu
            ])
            @endforeach
        </tbody>

        <tfoot>
            {{-- 1. Rencana per minggu --}}
            <tr class="fw-bold table-primary text-primary">
                <td colspan="8" class="text-center">RENCANA PEKERJAAN</td>
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRencana = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_rencana ?? 0);
                });
                @endphp
                <td colspan="2" class="text-end">{{ number_format($totalRencana, 2) }}%</td>
                @endforeach
            </tr>

            {{-- 2. Kumulatif rencana --}}
            <tr class="fw-bold table-primary text-primary">
                <td colspan="8" class="text-center">KUMULATIF RENCANA</td>
                @php $cumRencana = 0; @endphp
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRencana = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_rencana ?? 0);
                });
                $cumRencana += $totalRencana;
                @endphp
                <td colspan="2" class="text-end">{{ number_format($cumRencana, 2) }}%</td>
                @endforeach
            </tr>

            {{-- 3. Realisasi per minggu --}}
            <tr class="fw-bold table-success text-success">
                <td colspan="8" class="text-center">REALISASI PEKERJAAN</td>
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRealisasi = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_realisasi ?? 0);
                });
                @endphp
                <td colspan="2" class="text-end">{{ number_format($totalRealisasi, 2) }}%</td>
                @endforeach
            </tr>

            {{-- 4. Kumulatif realisasi --}}
            <tr class="fw-bold table-success text-success">
                <td colspan="8" class="text-center">KUMULATIF REALISASI</td>
                @php $cumRealisasi = 0; @endphp
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRealisasi = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_realisasi ?? 0);
                });
                $cumRealisasi += $totalRealisasi;
                @endphp
                <td colspan="2" class="text-end">{{ number_format($cumRealisasi, 2) }}%</td>
                @endforeach
            </tr>

            {{-- 5. Deviasi --}}
            <tr class="fw-bold table-warning">
                <td colspan="8" class="text-center">DEVIASI</td>
                @php $cumRencana = $cumRealisasi = 0; @endphp
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRencana = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_rencana ?? 0);
                });
                $totalRealisasi = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_realisasi ?? 0);
                });
                $cumRencana += $totalRencana;
                $cumRealisasi += $totalRealisasi;
                $deviasi = $cumRealisasi - $cumRencana;
                $deviasiClass = $deviasi >= 0 ? 'text-info' : 'text-danger';
                @endphp
                <td colspan="2" class="text-end {{ $deviasiClass }}">{{ number_format($deviasi, 2) }}%</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>


<!-- {{-- Wrapper untuk informasi dan paginasi DataTables --}}
<div class="p-4">
    <div class="row">
        {{-- Placeholder untuk "Showing X of Y" akan muncul di sini (di-generate oleh JS) --}}
        <div class="col-sm-12 col-md-5" id="rekapTable_info_placeholder"></div>
        {{-- Placeholder untuk Paginasi "Previous/Next" akan muncul di sini (di-generate oleh JS) --}}
        <div class="col-sm-12 col-md-7" id="rekapTable_paginate_placeholder"></div>
    </div>
</div> -->