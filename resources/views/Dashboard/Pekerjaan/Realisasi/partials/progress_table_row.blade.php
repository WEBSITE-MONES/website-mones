@php
// Styling row berdasarkan level hierarki
$rowClass = match($level) {
0 => 'table-secondary text-dark fw-bold',
1 => 'table-light',
default => '',
};

$indent_size = $level * 20;
$display_text = $item->sub_sub_pekerjaan ?: ($item->sub_pekerjaan ?: $item->jenis_pekerjaan_utama);
$text_class = ($level <= 1) ? 'fw-semibold' : '' ; $hasChildren=$item->children && $item->children->isNotEmpty();
    @endphp

    <tr class="{{ $rowClass }}" data-id="{{ $item->id }}" data-kode="{{ $item->kode_pekerjaan }}" @if($item->parent_id)
        data-parent-id="{{ $item->parent_id }}" @endif>

        {{-- Nomor --}}
        <td class="text-center">{{ $no }}</td>

        {{-- Uraian Pekerjaan --}}
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

        {{-- Volume --}}
        <td class="text-end">{{ number_format((float) $item->volume, 2) }}</td>

        {{-- Satuan --}}
        <td class="text-center">{{ $item->sat }}</td>

        {{-- Bobot --}}
        <td class="text-end fw-bold text-primary">{{ number_format($item->bobot ?? 0, 2) }}%</td>

        {{-- Data per Minggu --}}
        @foreach ($masterMinggu as $minggu)
        @php
        // ============================================
        // AMBIL DATA DENGAN FALLBACK SYSTEM
        // ============================================
        $detail = null;

        // PRIORITAS 1: Dari mapping controller
        if (isset($progressDetailsMap[$item->id][$minggu->id])) {
        $detail = $progressDetailsMap[$item->id][$minggu->id];
        }
        // PRIORITAS 2: Query langsung (fallback)
        else {
        // Cari progress record untuk item ini
        $progress = \App\Models\Progress::where('po_id', $po->id)
        ->where('pekerjaan_item_id', $item->id)
        ->first();

        if ($progress) {
        // Cari detail untuk minggu ini
        $detailModel = \App\Models\ProgressDetail::where('progress_id', $progress->id)
        ->where('minggu_id', $minggu->id)
        ->first();

        if ($detailModel) {
        $detail = [
        'bobot_rencana' => (float) $detailModel->bobot_rencana,
        'volume_realisasi' => (float) $detailModel->volume_realisasi,
        'bobot_realisasi' => (float) $detailModel->bobot_realisasi,
        'keterangan' => $detailModel->keterangan
        ];

        // Debug log (hanya di development)
        if (config('app.debug')) {
        \Log::debug('📌 Fallback Query Used', [
        'item_kode' => $item->kode_pekerjaan,
        'minggu' => $minggu->kode_minggu,
        'volume' => $detail['volume_realisasi'],
        'bobot' => $detail['bobot_realisasi']
        ]);
        }
        }
        }
        }

        // Extract nilai atau default 0
        $rencana = 0;
        $volumeRealisasi = 0;
        $bobotRealisasi = 0;

        if ($detail) {
        // ✅ PERBAIKAN: Konversi ke persentase untuk chart
        $bobotRencana = (float) $detail->bobot_rencana;
        $bobotRealisasi = (float) $detail->bobot_realisasi;

        // 🔧 Jika nilai dalam desimal (< 1), kalikan 100 // Asumsi: jika bobot < 1, berarti masih dalam decimal
            (0.0003) // Jika bobot>= 1, berarti sudah dalam % (0.41)
            if ($bobotRencana < 1 && $bobotRencana> 0) {
                $bobotRencana = $bobotRencana * 100;
                }
                if ($bobotRealisasi < 1 && $bobotRealisasi> 0) {
                    $bobotRealisasi = $bobotRealisasi * 100;
                    }

                    $rencanaWeek += $bobotRencana;
                    $realisasiWeek += $bobotRealisasi;

                    // ✅ LOG DETAIL untuk debugging
                    Log::info('✅ Found data for chart', [
                    'item_id' => $progress->pekerjaan_item_id,
                    'minggu' => $minggu->kode_minggu,
                    'bobot_rencana_raw' => $detail->bobot_rencana,
                    'bobot_rencana_converted' => $bobotRencana,
                    'bobot_realisasi_raw' => $detail->bobot_realisasi,
                    'bobot_realisasi_converted' => $bobotRealisasi,
                    'volume_realisasi' => $detail->volume_realisasi ?? 0
                    ]);
                    }
                    @endphp

                    {{-- Kolom RENCANA --}}
                    <td class="text-end small fw-semibold" style="background-color: #e3f2fd;">
                        @if($rencana > 0)
                        <span class="text-primary">{{ number_format($rencana, 2) }}%</span>
                        @else
                        <span class="text-muted" style="opacity: 0.3;">-</span>
                        @endif
                    </td>

                    {{-- Kolom VOLUME REALISASI --}}
                    <td class="text-center small fw-semibold" style="background-color: #fff8dc;">
                        @if($volumeRealisasi > 0)
                        <span class="badge bg-info text-white">{{ number_format($volumeRealisasi, 2) }}</span>
                        @else
                        <span class="text-muted" style="opacity: 0.3;">-</span>
                        @endif
                    </td>

                    {{-- Kolom BOBOT REALISASI --}}
                    <td class="text-end small fw-bold" style="background-color: #f1f8e9;">
                        @if($bobotRealisasi > 0)
                        @php
                        $badgeClass = $bobotRealisasi >= $rencana ? 'bg-success' : 'bg-warning text-dark';
                        $percentage = number_format($bobotRealisasi, 2);
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $percentage }}%</span>
                        @else
                        <span class="text-muted" style="opacity: 0.3;">-</span>
                        @endif
                    </td>
                    @endforeach
    </tr>

    {{-- Render Children Recursively --}}
    @if ($hasChildren)
    @php
    $childNo = 1; // Counter untuk nomor child
    @endphp
    @foreach ($item->children as $child)
    @include('Dashboard.Pekerjaan.Realisasi.partials.progress_table_row', [
    'item' => $child,
    'level' => $level + 1,
    'po' => $po,
    'masterMinggu' => $masterMinggu,
    'progressDetailsMap' => $progressDetailsMap,
    'no' => ''
    ])
    @php $childNo++; @endphp
    @endforeach
    @endif