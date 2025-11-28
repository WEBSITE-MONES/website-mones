@php
$rowClass = match($level) {
0 => 'table-secondary text-dark fw-bold',
1 => 'table-light',
default => '',
};

$indent_size = $level * 20;
$display_text = $item->sub_sub_pekerjaan ?: ($item->sub_pekerjaan ?: $item->jenis_pekerjaan_utama);
$text_class = ($level <= 1) ? 'fw-semibold' : '' ; $hasChildren=$item->children && $item->children->isNotEmpty();
    @endphp

    <tr class="{{ $rowClass }}" data-id="{{ $item->id }}" @if($item->parent_id) data-parent-id="{{ $item->parent_id }}"
        @endif>

        <td class="text-center">{{ $no }}</td>
        <td class="{{ $text_class }}">
            <div class="d-flex align-items-center" style="padding-left: {{ $indent_size }}px;">
                @if ($hasChildren)
                <a href="#" class="tree-toggle text-decoration-none me-2 text-dark" onclick="return false;">
                    <i class="bi bi-chevron-down"></i>
                </a>
                @else
                <span style="width: 24px; display: inline-block;"></span>
                @endif
                <span>{{ $display_text }}</span>
            </div>
        </td>
        <td class="text-end">{{ number_format((float) $item->volume, 2) }}</td>
        <td class="text-center">{{ $item->sat }}</td>
        <td class="text-end fw-bold text-primary">{{ number_format($item->bobot ?? 0, 2) }}%</td>

        @foreach ($masterMinggu as $minggu)
        @php
        $detail = $progressDetailsMap[$item->id][$minggu->id] ?? null;

        $rencana = isset($detail['bobot_rencana']) ? (float) $detail['bobot_rencana'] : 0;
        $volumeRealisasi = isset($detail['volume_realisasi']) ? (float) $detail['volume_realisasi'] : 0;
        $bobotRealisasi = isset($detail['bobot_realisasi']) ? (float) $detail['bobot_realisasi'] : 0;
        @endphp

        <td class="text-end small fw-semibold" style="background-color: #e3f2fd;">
            {{ $rencana > 0 ? number_format($rencana, 2) . '%' : '-' }}
        </td>

        <td class="text-center small fw-semibold" style="background-color: #fff8dc;">
            @if($volumeRealisasi > 0)
            <span class="badge bg-info text-white">{{ number_format($volumeRealisasi, 2) }}</span>
            @else
            <span class="text-muted">-</span>
            @endif
        </td>

        <td class="text-end small fw-bold" style="background-color: #f1f8e9;">
            @if($bobotRealisasi > 0)
            @php
            $badgeClass = $bobotRealisasi >= $rencana ? 'bg-success' : 'bg-warning text-dark';
            @endphp
            <span class="badge {{ $badgeClass }}">{{ number_format($bobotRealisasi, 2) }}%</span>
            @else
            <span class="text-muted">-</span>
            @endif
        </td>
        @endforeach
    </tr>

    {{-- Children recursion --}}
    @if ($hasChildren)
    @foreach ($item->children as $child)
    @include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', [
    'item' => $child,
    'level' => $level + 1,
    'po' => $po,
    'masterMinggu' => $masterMinggu,
    'progressDetailsMap' => $progressDetailsMap
    ])
    @endforeach
    @endif