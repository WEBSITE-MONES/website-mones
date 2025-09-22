@php
// nomor baris global supaya increment across recursive include
$GLOBALS['rowNo'] = ($GLOBALS['rowNo'] ?? 0) + 1;
$no = $GLOBALS['rowNo'];

// Ambil progress terkait item ini
$progress = $po->progresses->firstWhere('pekerjaan_item_id', $item->id);

// Variabel kumulatif untuk item ini
$rencanaCum = 0;
$realisasiCum = 0;
$hasAnyDetailSoFar = false; // untuk menentukan kapan kumulatif mulai tampil
@endphp

<tr>
    {{-- 1) No (sesuai header) --}}
    <td class="text-center">{{ $no }}</td>

    {{-- 2) Jenis Pekerjaan --}}
    <td>{{ $item->jenis_pekerjaan_utama }}</td>

    {{-- 3) Sub Pekerjaan --}}
    <td>{{ $item->sub_pekerjaan }}</td>

    {{-- 4) Sub-Sub Pekerjaan --}}
    <td>{{ $item->sub_sub_pekerjaan }}</td>

    {{-- 5) Volume --}}
    <td class="text-end">{{ number_format((float) $item->volume, 2) }}</td>

    {{-- 6) Satuan --}}
    <td>{{ $item->sat }}</td>

    {{-- 7) Harga Satuan --}}
    <td class="text-end">{{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>

    {{-- 8) Jumlah Harga --}}
    <td class="text-end">{{ number_format($item->jumlah_harga ?? 0, 0, ',', '.') }}</td>

    {{-- 9) Bobot Total (angka) --}}
    <td class="text-end">{{ number_format($item->bobot ?? $item->bobot_total ?? 0, 2) }}</td>

    {{-- 10) Bobot (%) (tampilkan sama seperti angka, header punya kolom ini juga) --}}
    <td class="text-end">{{ number_format($item->bobot ?? $item->bobot_total ?? 0, 2) }}%</td>

    {{-- Dinamis: 5 kolom per minggu (Rencana, Rencana Cum, Realisasi, Realisasi Cum, Deviasi) --}}
    @foreach ($masterMinggu as $minggu)
    @php
    $detail = $progress?->details?->firstWhere('minggu_id', $minggu->id);
    $hasDetailThisWeek = (bool) $detail && ((float) $detail->bobot_rencana !== 0 || (float) $detail->bobot_realisasi !==
    0);

    // Ambil nilai hanya kalau ada detail
    $rencana = $hasDetailThisWeek ? (float) $detail->bobot_rencana : 0;
    $realisasi = $hasDetailThisWeek ? (float) $detail->bobot_realisasi : 0;

    // kalau ada detail minggu ini, tambahkan ke kumulatif
    if ($hasDetailThisWeek) {
    $rencanaCum += $rencana;
    $realisasiCum += $realisasi;
    $hasAnyDetailSoFar = true;
    }

    $deviasi = $realisasiCum - $rencanaCum;
    @endphp

    {{-- Rencana (per minggu) --}}
    <td class="text-end">
        {{ $hasDetailThisWeek ? number_format($rencana, 2) . '%' : '-' }}
    </td>

    {{-- Rencana Kumulatif --}}
    <td class="text-end">
        {{ $hasAnyDetailSoFar ? number_format($rencanaCum, 2) . '%' : '-' }}
    </td>

    {{-- Realisasi (per minggu) --}}
    <td class="text-end">
        @if ($progress)
        <input type="number" name="progress[{{ $progress->id }}][detail][{{ $minggu->id }}][bobot_realisasi]"
            class="form-control form-control-sm text-end" style="min-width:70px;" step="0.01"
            value="{{ $realisasi > 0 ? $realisasi : '' }}">
        @else
        -
        @endif
    </td>


    {{-- Realisasi Kumulatif --}}
    <td class="text-end">
        {{ $hasAnyDetailSoFar ? number_format($realisasiCum, 2) . '%' : '-' }}
    </td>

    {{-- Deviasi (kumulatif) --}}
    <td class="text-end {{ $deviasi < 0 ? 'text-danger' : 'text-success' }}">
        {{ $hasAnyDetailSoFar ? number_format($deviasi, 2) . '%' : '-' }}
    </td>
    @endforeach
</tr>

{{-- Rekursif untuk children --}}
@if ($item->children && $item->children->isNotEmpty())
@foreach ($item->children as $child)
@include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', [
'item' => $child,
'level' => $level + 1,
'po' => $po,
'masterMinggu' => $masterMinggu
])
@endforeach
@endif