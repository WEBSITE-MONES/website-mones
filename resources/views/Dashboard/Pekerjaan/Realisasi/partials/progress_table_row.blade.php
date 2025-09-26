@php
$GLOBALS['rowNo'] = ($GLOBALS['rowNo'] ?? 0) + 1;
$no = $GLOBALS['rowNo'];

$progress = $po->progresses->firstWhere('pekerjaan_item_id', $item->id);

// kelas row
$rowClass = match($level) {
0 => 'table-active text-dark',
1 => 'bg-light-subtle text-muted',
default => '',
};

$indent_size = $level * 1.5;
$display_text = $item->sub_sub_pekerjaan ?: ($item->sub_pekerjaan ?: $item->jenis_pekerjaan_utama);
$text_class = ($level == 0) ? 'fw-bold' : '';

$hasChildren = $item->children && $item->children->isNotEmpty();
@endphp

<tr class="{{ $rowClass }}" data-id="{{ $item->id }}" @if($item->parent_id) data-parent-id="{{ $item->parent_id }}"
    @endif>
    <td class="text-center small">{{ $no }}</td>

    <td class="{{ $text_class }}">
        <div class="d-flex align-items-center" style="padding-left: {{ $indent_size }}rem;">
            @if ($hasChildren)
            <a href="#" class="tree-toggle text-decoration-none me-2 text-dark">
                <i class="bi bi-chevron-down"></i>
            </a>
            @else
            <span style="width: 1.2rem; display: inline-block;"></span>
            @endif
            <span>{{ $display_text }}</span>
        </div>
    </td>

    <td class="text-end">{{ number_format((float) $item->volume, 2) }}</td>
    <td class="text-center">{{ $item->sat }}</td>
    <td class="text-end fw-bold text-primary">{{ number_format($item->bobot ?? $item->bobot_total ?? 0, 2) }}%</td>

    {{-- Mingguan --}}
    @foreach ($masterMinggu as $minggu)
    @php
    $detail = $progress?->details?->firstWhere('minggu_id', $minggu->id);
    $rencana = (float) ($detail?->bobot_rencana ?? 0);
    $volumeRealisasi = (float) ($detail?->volume_realisasi ?? 0);
    $volume = (float) ($item->volume ?? 0);

    // hitung bobot realisasi
    $realisasi = 0;
    if ($volume > 0 && $volumeRealisasi > 0) {
    $realisasi = ($volumeRealisasi / $volume) * $rencana;
    }
    @endphp

    {{-- Rencana --}}
    <td class="text-end bg-warning-subtle small text-dark">
        {{ $rencana > 0 ? number_format($rencana, 2) . '%' : '-' }}
    </td>

    {{-- Volume Realisasi --}}
    <td class="text-end small">
        <input type="number" step="0.01" name="progress[{{ $item->id }}][{{ $minggu->id }}][volume_realisasi]"
            value="{{ $volumeRealisasi }}" class="form-control form-control-sm text-end">
    </td>

    {{-- Realisasi --}}
    <td class="text-end small fw-bold text-success">
        {{ $realisasi > 0 ? number_format($realisasi, 2) . '%' : '-' }}
    </td>
    @endforeach

</tr>

{{-- rekursif anak --}}
@if ($hasChildren)
@foreach ($item->children as $child)
@include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', [
'item' => $child,
'level' => $level + 1,
'po' => $po,
'masterMinggu' => $masterMinggu
])
@endforeach
@endif