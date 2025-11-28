@push('styles')
<style>
#progressTable {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.table-responsive::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

#progressTable thead th {
    vertical-align: middle;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #cbd5e1;
    border-right: 1px solid #e2e8f0;
    padding: 10px 8px;
    background-clip: padding-box;
}

/* Warna Header */
.bg-header-main {
    background-color: #1e293b !important;
    color: #ffffff;
    border-color: #334155;
}

.bg-header-sub {
    background-color: #f8fafc !important;
    color: #475569;
}

.bg-header-week {
    background-color: #f1f5f9 !important;
    color: #334155;
}

/* --- Body Styling --- */
#progressTable tbody td {
    vertical-align: middle;
    padding: 12px 10px;
    font-size: 0.85rem;
    border-bottom: 1px solid #cbd5e1;
    border-right: 1px solid #f1f5f9;
    color: #334155;
}

/* Sticky Configuration */
.sticky-top {
    z-index: 40;
}

.sticky-col {
    position: sticky;
    left: 0;
    z-index: 20;
    background-color: #ffffff;
}

.sticky-shadow-right {
    box-shadow: 4px 0 6px -2px rgba(0, 0, 0, 0.1);
    border-right: 1px solid #cbd5e1 !important;
}

/* Row States */
.parent-row {
    background-color: #f1f5f9;
    color: #0f172a;
    font-weight: 600;
}

.table-hover tbody tr:hover td {
    background-color: #e0f2fe !important;
}

.text-justify-custom {
    text-align: justify !important;
    text-justify: inter-word;
    line-height: 1.6;
    display: block;
}

.col-number {
    font-family: 'Roboto Mono', monospace;
    font-size: 0.75rem;
}

.bg-col-rencana {
    background-color: #f0f9ff !important;
}

.bg-col-vol {
    background-color: #fffbeb !important;
}

.bg-col-real {
    background-color: #f0fdf4 !important;
}

/* Indikator Status */
.status-dot {
    height: 6px;
    width: 6px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    margin-top: 6px;
    flex-shrink: 0;
}
</style>
@endpush

<div class="table-responsive shadow-sm rounded-3 border bg-white" style="max-height: 750px; overflow-y: auto;">
    <table class="table table-borderless mb-0" id="progressTable">
        <thead class="sticky-top bg-white" style="top: 0;">
            <tr>
                {{-- 1. Kode WBS (Sticky) --}}
                <th rowspan="3" class="text-center sticky-col bg-header-sub border-bottom"
                    style="width: 70px; left: 0; z-index: 50;">
                    KODE
                </th>
                {{-- 2. Uraian Pekerjaan (Sticky) --}}
                <th rowspan="3" class="text-center sticky-col sticky-shadow-right bg-header-sub border-bottom"
                    style="min-width: 350px; left: 70px; z-index: 50;">
                    URAIAN PEKERJAAN
                </th>

                {{-- Info Item --}}
                <th rowspan="3" class="text-center bg-header-sub" style="width: 80px;">VOL</th>
                <th rowspan="3" class="text-center bg-header-sub" style="width: 60px;">SAT</th>
                <th rowspan="3" class="text-center bg-header-sub" style="width: 80px;">BOBOT</th>

                {{-- Header Bulan --}}
                @foreach($monthMap as $monthName => $monthData)
                <th colspan="{{ $monthData['colspan'] }}"
                    class="text-center bg-header-main border-start border-secondary">
                    {{ $monthName }}
                </th>
                @endforeach
            </tr>

            {{-- Header Minggu --}}
            <tr>
                @foreach($monthMap as $monthData)
                @foreach($monthData['minggus'] as $minggu)
                <th colspan="3" class="text-center bg-header-week border-start small">
                    Minggu {{ $minggu->kode_minggu }}
                </th>
                @endforeach
                @endforeach
            </tr>

            {{-- Header Range Tanggal --}}
            <tr>
                @foreach($masterMinggu as $minggu)
                <th colspan="3" class="text-center bg-white border-bottom border-start text-muted">
                    <small style="font-size: 0.65rem;">
                        {{ $minggu->tanggal_awal->format('d/m') }} - {{ $minggu->tanggal_akhir->format('d/m') }}
                    </small>
                </th>
                @endforeach
            </tr>

            {{-- Sub-Header Kolom Data --}}
            <tr class="border-bottom">
                <th colspan="5"
                    class="bg-light sticky-col sticky-shadow-right text-start ps-4 py-2 text-muted fst-italic"
                    style="left:0; z-index: 45; font-size: 0.75rem;">
                    Rincian Item Pekerjaan
                </th>

                @foreach($masterMinggu as $minggu)
                <th class="text-center text-primary bg-col-rencana py-2 border-start"
                    style="width: 55px; font-size: 0.65rem;">RENCANA</th>
                <th class="text-center text-warning-emphasis bg-col-vol py-2" style="width: 55px; font-size: 0.65rem;">
                    VOLUME REALISASI</th>
                <th class="text-center text-success bg-col-real py-2" style="width: 55px; font-size: 0.65rem;">REALISASI
                </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php
            function renderItems($items, $masterMinggu, $progressDetailsMap, $parentNumber = '', $parentId = '', $level
            = 0) {
            static $counter = 1;

            foreach ($items as $item) {
            $isParent = $item->children->count() > 0;
            $currentNumber = $parentNumber ? $parentNumber . '.' . $counter : $counter;
            $rowId = 'row-' . $item->id;
            $parentClass = $parentId ? 'child-of-' . $parentId : '';

            $namaItem = $item->display_name;
            if(empty($namaItem)) {
            $namaItem = ($item->sub_pekerjaan ?: ($item->sub_sub_pekerjaan ?: 'Item'));
            }

            $rowClass = $isParent ? 'parent-row' : '';
            $rowClass .= ' ' . $parentClass;

            // Indentasi
            $paddingLeft = 15 + ($level * 20);

            echo '<tr class="' . trim($rowClass) . '" data-id="' . $rowId . '" data-level="' . $level . '">';

                // 1. KODE WBS
                echo '<td class="text-center sticky-col border-bottom" style="left: 0; background-color: inherit;">';
                    echo '<span class="badge bg-light text-secondary border border-secondary-subtle rounded-1"
                        style="font-weight: 500;">' . $item->kode_pekerjaan . '</span>';
                    echo '</td>';

                echo '<td class="sticky-col sticky-shadow-right border-bottom text-start"
                    style="left: 70px; padding-left: '.$paddingLeft.'px; background-color: inherit;">';
                    echo '<div class="d-flex align-items-start">';
                        if ($isParent) {
                        echo '<i class="fas fa-chevron-down toggle-icon me-2 text-primary mt-1"
                            style="cursor: pointer; width: 12px; font-size: 0.7rem;"
                            onclick="toggleChildren(\'' . $rowId . '\')"></i>';
                        } else {
                        echo '<div class="status-dot bg-secondary bg-opacity-25 mt-2 ms-1 me-2"></div>';
                        }

                        // WRAPPER JUSTIFY: Paksa style justify di sini
                        echo '<div class="text-justify-custom w-100 pe-2" style="text-align: justify;">';
                            echo $namaItem;
                            echo '</div>';
                        echo '</div>';
                    echo '</td>';

                // 3. VOLUME
                echo '<td class="text-end col-number">' . number_format($item->volume, 2) . '</td>';

                // 4. SATUAN
                echo '<td class="text-center small text-muted">' . $item->sat . '</td>';

                // 5. BOBOT
                echo '<td class="text-end col-number fw-bold text-dark">' . number_format($item->bobot, 2) . '%</td>';
                foreach ($masterMinggu as $minggu) {
                $detail = $progressDetailsMap[$item->id][$minggu->id] ?? null;
                $rencana = $detail ? (float) $detail['bobot_rencana'] : 0;
                $volumeRealisasi = $detail ? (float) $detail['volume_realisasi'] : 0;
                $bobotRealisasi = $detail ? (float) $detail['bobot_realisasi'] : 0;

                // Col Rencana
                echo '<td class="text-end col-number bg-col-rencana border-start">';
                    if($rencana > 0) {
                    echo '<span class="text-primary">' . number_format($rencana, 2) . '%</span>';
                    } else {
                    echo '<span class="text-muted opacity-25">-</span>';
                    }
                    echo '</td>';

                // Col Vol Realisasi
                echo '<td class="text-center col-number bg-col-vol">';
                    if ($volumeRealisasi > 0) {
                    echo '<span class="text-dark fw-semibold">' . number_format($volumeRealisasi, 2) . '</span>';
                    } else {
                    echo '<span class="text-muted opacity-25">-</span>';
                    }
                    echo '</td>';

                // Col Bobot Realisasi
                echo '<td class="text-end col-number bg-col-real">';
                    if ($bobotRealisasi > 0) {
                    $textClass = $bobotRealisasi >= $rencana ? 'text-success' : 'text-danger';
                    echo '<span class="fw-bold ' . $textClass . '">' . number_format($bobotRealisasi, 2) . '%</span>';
                    } else {
                    echo '<span class="text-muted opacity-25">-</span>';
                    }
                    echo '</td>';
                }

                echo '</tr>';

            if ($isParent) {
            renderItems($item->children, $masterMinggu, $progressDetailsMap, $currentNumber, $rowId, $level + 1);
            }

            $counter++;
            }
            }

            renderItems($items, $masterMinggu, $progressDetailsMap);
            @endphp
        </tbody>

        {{-- Footer Summary --}}
        <tfoot class="border-top border-2">
            <tr class="bg-light">
                <td colspan="5" class="text-end fw-bold text-uppercase py-3 sticky-col sticky-shadow-right text-primary"
                    style="left:0; z-index:30; font-size: 0.75rem;">
                    KOMULATIF RENCANA
                </td>
                @foreach($masterMinggu as $minggu)
                @php
                $totalRencana = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id) continue;
                if(in_array($p->pekerjaan_item_id, $parentItemIds)) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) $totalRencana += (float) $d->bobot_rencana;
                }
                @endphp
                <td colspan="3" class="text-center fw-bold col-number text-primary bg-white border-start">
                    {{ number_format($totalRencana, 2) }}%</td>
                @endforeach
            </tr>

            <!-- Summary Realisasi -->
            <tr>
                <td colspan="5" class="text-end fw-bold text-uppercase py-3 sticky-col sticky-shadow-right text-success"
                    style="left:0; z-index:30; font-size: 0.75rem;">
                    KOMULATIF REALISASI
                </td>
                @foreach($masterMinggu as $minggu)
                @php
                $totalRealisasi = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id) continue;
                if(in_array($p->pekerjaan_item_id, $parentItemIds)) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) $totalRealisasi += (float) $d->bobot_realisasi;
                }
                @endphp
                <td colspan="3" class="text-center fw-bold col-number text-success bg-white border-start">
                    {{ number_format($totalRealisasi, 2) }}%</td>
                @endforeach
            </tr>

            <!-- Summary Deviasi -->
            <tr class="bg-gray-100">
                <td colspan="5" class="text-end fw-bold text-uppercase py-3 sticky-col sticky-shadow-right text-dark"
                    style="left:0; z-index:30; font-size: 0.75rem;">
                    DEVIASI KUMULATIF
                </td>
                @php
                $kumulatifRencana = 0;
                $kumulatifRealisasi = 0;
                @endphp
                @foreach($masterMinggu as $minggu)
                @php
                $totalR = 0; $totalReal = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id || in_array($p->pekerjaan_item_id, $parentItemIds)) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) { $totalR += $d->bobot_rencana; $totalReal += $d->bobot_realisasi; }
                }
                $kumulatifRencana += $totalR;
                $kumulatifRealisasi += $totalReal;
                $deviasi = $kumulatifRealisasi - $kumulatifRencana;
                $devColor = $deviasi >= 0 ? 'text-success' : 'text-danger';
                $bgDev = $deviasi >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle';
                @endphp
                <td colspan="3" class="text-center fw-bold col-number {{ $devColor }} {{ $bgDev }} border-start">
                    {{ $deviasi > 0 ? '+' : '' }}{{ number_format($deviasi, 2) }}%
                </td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>