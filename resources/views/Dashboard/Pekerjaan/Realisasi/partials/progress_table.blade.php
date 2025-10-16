<div class="table-responsive bg-white rounded-3 shadow-sm p-3">
    <table id="rekapTable" class="table table-bordered table-hover align-middle mb-0" style="min-width: 1800px;">
        <thead class="text-center align-middle sticky-top" style="top: -1px;">

            @php
            $totalDynamicColspan = ($masterMinggu && $masterMinggu->count() > 0) ? $masterMinggu->count() * 3 : 1;
            @endphp


            {{-- Baris 1: Header Utama --}}
            <tr class="table-blue">
                <th rowspan="4" style="width:50px;">No</th>
                <th rowspan="4" style="width:250px;">Jenis Pekerjaan</th>
                <th rowspan="4" style="width:80px;">Volume</th>
                <th rowspan="4" style="width:70px;">Sat</th>
                <th rowspan="4" style="width:100px;">Bobot (%)</th>
                <th colspan="{{ $totalDynamicColspan }}">JADWAL PELAKSANAAN PEKERJAAN</th>
            </tr>

            {{-- Baris 2: Bulan --}}
            <tr class="table-primary">
                @foreach ($monthMap as $bulan => $data)
                @php
                $jumlahMinggu = count($data['minggus']);
                $colspan = $jumlahMinggu * 3;
                @endphp
                <th colspan="{{ $colspan }}" class="fw-medium">
                    {{ strtoupper($bulan) }}
                </th>
                @endforeach
            </tr>

            {{-- Baris 3: Minggu --}}
            <tr class="table-light">
                @foreach ($monthMap as $data)
                @foreach ($data['minggus'] as $minggu)
                <th colspan="3" class="fw-normal">{{ $minggu->kode_minggu }}</th>
                @endforeach
                @endforeach
            </tr>

            {{-- Baris 4: Detail per minggu --}}
            <tr class="table-light">
                @foreach ($monthMap as $data)
                @foreach ($data['minggus'] as $minggu)
                <th class="fw-semibold small text-primary-emphasis" style="background-color: #cfe2ff;">Rencana (%)</th>
                <th class="fw-semibold small text-dark-emphasis" style="background-color: #e2e3e5;">Volume Realisasi
                </th>
                <th class="fw-semibold small text-success-emphasis" style="background-color: #d1e7dd;">Realisasi (%)
                </th>
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

        <tfoot class="fw-bold text-center">
            {{-- 1. Rencana per minggu --}}
            <tr class="table-primary-subtle">
                <td colspan="5">RENCANA MINGGUAN (%)</td>
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRencana = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_rencana ?? 0);
                });
                @endphp
                <td colspan="3" class="text-end">{{ number_format($totalRencana, 2) }}</td>
                @endforeach
            </tr>

            {{-- 2. Kumulatif rencana --}}
            <tr class="table-primary-subtle">
                <td colspan="5">KUMULATIF RENCANA (%)</td>
                @php $cumRencana = 0; @endphp
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRencana = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_rencana ?? 0);
                });
                $cumRencana += $totalRencana;
                @endphp
                <td colspan="3" class="text-end">{{ number_format($cumRencana, 2) }}</td>
                @endforeach
            </tr>

            {{-- 3. Realisasi per minggu --}}
            <tr class="table-success-subtle">
                <td colspan="5">REALISASI MINGGUAN (%)</td>
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRealisasi = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_realisasi ?? 0);
                });
                @endphp
                <td colspan="3" class="text-end">{{ number_format($totalRealisasi, 2) }}</td>
                @endforeach
            </tr>

            {{-- 4. Kumulatif realisasi --}}
            <tr class="table-success-subtle">
                <td colspan="5">KUMULATIF REALISASI (%)</td>
                @php $cumRealisasi = 0; @endphp
                @foreach ($masterMinggu as $minggu)
                @php
                $totalRealisasi = $po->progresses->sum(function ($p) use ($minggu) {
                $detail = $p->details?->firstWhere('minggu_id', $minggu->id);
                return (float) ($detail?->bobot_realisasi ?? 0);
                });
                $cumRealisasi += $totalRealisasi;
                @endphp
                <td colspan="3" class="text-end">{{ number_format($cumRealisasi, 2) }}</td>
                @endforeach
            </tr>

            {{-- 5. Deviasi --}}
            <tr class="table-warning-subtle">
                <td colspan="5">DEVIASI (%)</td>
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
                $deviasiClass = $deviasi >= 0 ? 'text-primary' : 'text-danger';
                @endphp
                <td colspan="3" class="text-end {{ $deviasiClass }}">{{ number_format($deviasi, 2) }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>