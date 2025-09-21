@php
$progress = $progressMap->get($item->id);
$rencanaCum = 0;
$realisasiCum = 0;
$index = $item->id;
@endphp

<tr>
    {{-- Input tersembunyi untuk PekerjaanItem --}}
    <input type="hidden" name="progress[{{ $index }}][id]" value="{{ optional($progress)->id }}">
    <input type="hidden" name="progress[{{ $index }}][pekerjaan_item_id]" value="{{ $item->id }}">

    <td class="text-center">{{ $loop->iteration }}</td>
    <td style="padding-left:{{ $level * 18 }}px;">
        {{ trim(($item->jenis_pekerjaan_utama ?? '') . ' ' . ($item->sub_pekerjaan ?? '') . ' ' . ($item->sub_sub_pekerjaan ?? '')) }}
    </td>
    <td class="text-center">{{ (float)$item->volume }}</td>
    <td class="text-center">{{ $item->sat }}</td>
    <td class="text-right">{{ number_format((float)$item->bobot_total,2) }}%</td>

    @foreach ($masterMinggu as $minggu)
    @php
    $detail = $progress ? $progress->details->firstWhere('minggu_id', $minggu->id) : null;
    $rencana = $detail->bobot_rencana ?? 0;
    $realisasi = $detail->bobot_realisasi ?? 0;

    $rencanaCum += (float)$rencana;
    $realisasiCum += (float)$realisasi;
    $deviasi = $realisasiCum - $rencanaCum;
    @endphp

    <td class="text-right">{{ number_format($rencana,2) }}%</td>
    <td class="text-right">{{ number_format($rencanaCum,2) }}%</td>
    <td class="text-right">
        {{-- Nama input disesuaikan untuk array --}}
        <input type="number" step="0.01" name="progress[{{ $index }}][details][{{ $minggu->id }}][bobot_realisasi]"
            value="{{ number_format($realisasi, 2, '.', '') }}" class="form-control form-control-sm text-end"
            style="width: 70px;">
    </td>
    <td class="text-right">{{ number_format($realisasiCum,2) }}%</td>
    <td class="text-right {{ $deviasi < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($deviasi,2) }}%</td>
    @endforeach

</tr>

{{-- Recursive call untuk anak item --}}
@if (!empty($item->children))
@foreach ($item->children as $child)
@include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', ['item' => $child, 'level' => $level + 1])
@endforeach
@endif