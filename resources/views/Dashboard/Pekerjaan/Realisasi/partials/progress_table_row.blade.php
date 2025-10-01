@php
$GLOBALS['rowNo'] = ($GLOBALS['rowNo'] ?? 0) + 1;
$no = $GLOBALS['rowNo'];

$progress = $po->progresses->firstWhere('pekerjaan_item_id', $item->id);

$rowClass = match($level) {
0 => 'table-secondary text-dark fw-bold', // Level 0 (Utama)
1 => 'table-light', // Level 1 (Sub)
default => '', // Level 2+ (Sub-sub dst)
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
                <a href="#" class="tree-toggle text-decoration-none me-2 text-dark" style="cursor: pointer;">
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
        <td class="text-end fw-bold text-primary">{{ number_format($item->bobot ?? $item->bobot_total ?? 0, 2) }}%</td>

        {{-- Kolom Mingguan --}}
        @foreach ($masterMinggu as $minggu)
        @php
        $detail = $progress?->details?->firstWhere('minggu_id', $minggu->id);
        $rencana = (float) ($detail?->bobot_rencana ?? 0);
        $volumeRealisasi = (float) ($detail?->volume_realisasi ?? 0);
        $volume = (float) ($item->volume ?? 0);
        $realisasi = 0;
        if ($volume > 0 && $volumeRealisasi > 0) {
        $realisasi = ($volumeRealisasi / $volume) * ($item->bobot ?? 0);
        }
        @endphp

        <td class="text-end small" style="background-color: #f3f8ff;">
            {{ $rencana > 0 ? number_format($rencana, 2) . '%' : '-' }}
        </td>

        <td class="p-1 align-middle">
            <input type="number" step="any" name="progress[{{ $item->id }}][{{ $minggu->id }}][volume_realisasi]"
                value="{{ $volumeRealisasi > 0 ? $volumeRealisasi : '' }}"
                class="form-control form-control-sm text-end border-0 bg-light" @if($hasChildren) readonly disabled
                title="Input hanya pada item pekerjaan." @endif>
        </td>

        <td class="text-end small fw-bold text-success" style="background-color: #f0fbf4;">
            {{ $realisasi > 0 ? number_format($realisasi, 2) . '%' : '-' }}
        </td>
        @endforeach
    </tr>

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