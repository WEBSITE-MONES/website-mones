@php
// nomor baris global supaya increment across recursive include
$GLOBALS['rowNo'] = ($GLOBALS['rowNo'] ?? 0) + 1;
$no = $GLOBALS['rowNo'];

$progress = $po->progresses->firstWhere('pekerjaan_item_id', $item->id);

// Kelas baris untuk hirarki visual
$rowClass = match($level) {
0 => 'table-active text-dark',
1 => 'bg-light-subtle text-muted',
default => '',
};

// Logika untuk indentasi dan teks yang ditampilkan
$indent_size = $level * 1.5;
$style = "padding-left: {$indent_size}rem;";
$display_text = $item->sub_sub_pekerjaan ?: ($item->sub_pekerjaan ?: $item->jenis_pekerjaan_utama);
$text_class = ($level == 0) ? 'fw-bold' : '';

// Cek apakah item ini memiliki anak
$hasChildren = $item->children && $item->children->isNotEmpty();

// Siapkan atribut data untuk JavaScript
$dataAttributes = 'data-id="' . $item->id . '"';
if ($item->parent_id) {
// Tambahkan atribut data-parent-id jika ini adalah item anak
$dataAttributes .= ' data-parent-id="' . $item->parent_id . '"';
}
@endphp

<tr class="{{ $rowClass }}" {!! $dataAttributes !!}>
    {{-- 1) No --}}
    <td class="text-center small">{{ $no }}</td>

    {{-- 2) Jenis Pekerjaan (KOLOM GABUNGAN YANG DISEMPURNAKAN) --}}
    <td class="{{ $text_class }}">
        <div class="d-flex align-items-center" style="padding-left: {{ $level * 1.5 }}rem;">
            @if ($hasChildren)
            <a href="#" class="tree-toggle text-decoration-none me-2 text-dark">
                {{-- Ganti ikon ini jika menggunakan FontAwesome atau library lain --}}
                <i class="bi bi-chevron-down"></i>
            </a>
            @else
            <span style="width: 1.2rem; display: inline-block;"></span>
            @endif

            {{-- Teks Pekerjaan --}}
            <span>{{ $display_text }}</span>
        </div>
    </td>

    {{-- 3) Volume --}}
    <td class="text-end fw-semibold">{{ number_format((float) $item->volume, 2) }}</td>

    <td class="text-center">{{ $item->sat }}</td>
    <td class="text-end">{{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
    <td class="text-end fw-bold">{{ number_format($item->jumlah_harga ?? 0, 0, ',', '.') }}</td>
    <td class="text-end fw-bold text-primary">{{ number_format($item->bobot ?? $item->bobot_total ?? 0, 2) }}</td>
    <td class="text-end fw-bold text-primary">{{ number_format($item->bobot ?? $item->bobot_total ?? 0, 2) }}%</td>

    {{-- Kolom dinamis per minggu --}}

    @foreach ($masterMinggu as $minggu)
    @php
    $detail = $progress?->details?->firstWhere('minggu_id', $minggu->id);
    $hasDetailThisWeek = (bool) $detail && ((float) $detail->bobot_rencana !== 0 || (float) $detail->bobot_realisasi !==
    0);
    $rencana = $hasDetailThisWeek ? (float) $detail->bobot_rencana : 0;
    $realisasi = $hasDetailThisWeek ? (float) $detail->bobot_realisasi : 0;
    $rencanaData = $rencana > 0 ? number_format($rencana, 2) . '%' : '-';
    //$isInputEditable = $hasDetailThisWeek;
    @endphp

    {{-- Rencana --}}
    <td class="text-end bg-warning-subtle small text-dark" data-minggu-id="{{ $minggu->id }}"
        data-item-id="{{ $item->id }}">
        {{ $rencanaData }}
    </td>

    {{-- Realisasi (Tampilan Saja) --}}
    <td class="text-end small text-success fw-bold" data-minggu-id="{{ $minggu->id }}" data-item-id="{{ $item->id }}">
        @if (!empty($realisasi))
        {{ number_format($realisasi, 2) }}
        @else
        {{-- biarin kosong --}}
        @endif
    </td>


    @endforeach
</tr>

{{-- Rekursif untuk item anak --}}
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