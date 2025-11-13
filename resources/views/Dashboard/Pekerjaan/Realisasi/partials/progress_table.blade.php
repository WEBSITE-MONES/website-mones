<div class="table-responsive">
    <table class="table table-sm table-bordered table-hover align-middle mb-0" id="progressTable">
        <thead class="table-light sticky-top">
            <tr>
                <th rowspan="3" class="text-center align-middle" style="min-width: 60px;">No</th>
                <th rowspan="3" class="text-center align-middle" style="min-width: 120px;">Kode WBS</th>
                <th rowspan="3" class="text-center align-middle" style="min-width: 250px;">Uraian Pekerjaan</th>
                <th rowspan="3" class="text-center align-middle" style="min-width: 100px;">Volume</th>
                <th rowspan="3" class="text-center align-middle" style="min-width: 80px;">Satuan</th>
                <th rowspan="3" class="text-center align-middle" style="min-width: 100px;">Bobot (%)</th>

                {{-- Header Bulan --}}
                @foreach($monthMap as $monthName => $monthData)
                <th colspan="{{ $monthData['colspan'] }}" class="text-center bg-primary text-white">
                    {{ $monthName }}
                </th>
                @endforeach
            </tr>

            {{-- Header Minggu --}}
            <tr>
                @foreach($monthMap as $monthData)
                @foreach($monthData['minggus'] as $minggu)
                <th colspan="2" class="text-center bg-info text-white" style="min-width: 150px;">
                    {{ $minggu->kode_minggu }}
                </th>
                @endforeach
                @endforeach
            </tr>

            {{-- Header Range Tanggal --}}
            <tr>
                @foreach($masterMinggu as $minggu)
                <th colspan="2" class="text-center bg-light">
                    <small>{{ $minggu->tanggal_awal->format('d M') }} -
                        {{ $minggu->tanggal_akhir->format('d M') }}</small>
                </th>
                @endforeach
            </tr>

            {{-- Header Rencana/Realisasi --}}
            <tr class="table-secondary">
                <th colspan="6" class="text-center">Item Pekerjaan</th>
                @foreach($masterMinggu as $minggu)
                <th class="text-center" style="width: 75px;">Rencana</th>
                <th class="text-center" style="width: 75px;">Realisasi</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
            // ✅ Fungsi render dengan collapse/expand
            function renderItems($items, $masterMinggu, $progressDetailsMap, $parentNumber = '', $parentId = '', $level
            = 0) {
            $counter = 1;

            foreach ($items as $item) {
            $isParent = $item->children->count() > 0;

            // Generate nomor hierarki
            $currentNumber = $parentNumber ? $parentNumber . '.' . $counter : $counter;

            // Generate unique ID untuk collapse
            $rowId = 'row-' . $item->id;
            $parentClass = $parentId ? 'child-of-' . $parentId : '';

            // Nama item
            $namaItem = $item->jenis_pekerjaan_utama ?:
            ($item->sub_pekerjaan ?:
            ($item->sub_sub_pekerjaan ?: 'Item'));

            // Row class
            $rowClass = $isParent ? 'fw-bold bg-light parent-row' : '';
            $rowClass .= ' ' . $parentClass;
            $rowClass .= ' level-' . $level;

            echo '<tr class="' . trim($rowClass) . '" data-id="' . $rowId . '" data-level="' . $level . '">';

                // Nomor
                echo '<td class="text-center">' . $currentNumber . '</td>';

                // Kode WBS
                echo '<td>' . $item->kode_pekerjaan . '</td>';

                // Uraian dengan toggle icon
                echo '<td>';

                    // Indentasi
                    echo str_repeat('<span class="ms-3"></span>', $level);

                    // Toggle icon untuk parent
                    if ($isParent) {
                    echo '<i class="fas fa-chevron-down toggle-icon me-2" style="cursor: pointer; color: #007bff;"
                        onclick="toggleChildren(\'' . $rowId . '\')"></i>';
                    } else {
                    echo '<span class="me-4"></span>';
                    }

                    echo $namaItem;
                    echo '</td>';

                // Volume, Satuan, Bobot
                echo '<td class="text-end">' . number_format($item->volume, 2) . '</td>';
                echo '<td class="text-center">' . $item->sat . '</td>';
                echo '<td class="text-end">' . number_format($item->bobot, 2) . '%</td>';

                // Loop minggu
                foreach ($masterMinggu as $minggu) {
                $detail = $progressDetailsMap[$item->id][$minggu->id] ?? null;
                $rencana = $detail ? number_format($detail->bobot_rencana, 2) : '0.00';
                $realisasi = $detail ? number_format($detail->bobot_realisasi, 2) : '0.00';

                // Rencana
                echo '<td class="text-center text-primary fw-semibold">' . $rencana . '%</td>';

                // Realisasi dengan badge
                $badgeClass = 'bg-secondary';
                if ($detail && $detail->bobot_realisasi > 0) {
                if ($detail->bobot_realisasi >= $detail->bobot_rencana) {
                $badgeClass = 'bg-success';
                } else {
                $badgeClass = 'bg-warning';
                }
                }

                echo '<td class="text-center">';
                    echo '<span class="badge ' . $badgeClass . ' text-white">' . $realisasi . '%</span>';
                    echo '</td>';
                }

                echo '</tr>';

            // Render children
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
        <tfoot class="table-light">
            {{-- Baris 1: RENCANA PEKERJAAN --}}
            <tr class="bg-warning">
                <th colspan="6" class="text-center fw-bold">RENCANA PEKERJAAN</th>
                @foreach($masterMinggu as $minggu)
                @php
                $totalRencana = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) {
                $totalRencana += (float) $d->bobot_rencana;
                }
                }
                @endphp
                <th colspan="2" class="text-center fw-bold">{{ number_format($totalRencana, 2) }}%</th>
                @endforeach
            </tr>

            {{-- Baris 2: KUMULATIF RENCANA PEKERJAAN --}}
            <tr>
                <th colspan="6" class="text-center fw-bold">KUMULATIF RENCANA PEKERJAAN</th>
                @php
                $kumulatifRencana = 0;
                @endphp
                @foreach($masterMinggu as $minggu)
                @php
                $totalRencana = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) {
                $totalRencana += (float) $d->bobot_rencana;
                }
                }
                $kumulatifRencana += $totalRencana;
                @endphp
                <th colspan="2" class="text-center fw-bold">{{ number_format($kumulatifRencana, 2) }}%</th>
                @endforeach
            </tr>

            {{-- Baris 3: REALISASI PEKERJAAN --}}
            <tr class="bg-warning">
                <th colspan="6" class="text-center fw-bold">REALISASI PEKERJAAN</th>
                @foreach($masterMinggu as $minggu)
                @php
                $totalRealisasi = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) {
                $totalRealisasi += (float) $d->bobot_realisasi;
                }
                }
                @endphp
                <th colspan="2" class="text-center fw-bold">{{ number_format($totalRealisasi, 2) }}%</th>
                @endforeach
            </tr>

            {{-- Baris 4: KUMULATIF REALISASI PEKERJAAN --}}
            <tr>
                <th colspan="6" class="text-center fw-bold">KUMULATIF REALISASI PEKERJAAN</th>
                @php
                $kumulatifRealisasi = 0;
                @endphp
                @foreach($masterMinggu as $minggu)
                @php
                $totalRealisasi = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) {
                $totalRealisasi += (float) $d->bobot_realisasi;
                }
                }
                $kumulatifRealisasi += $totalRealisasi;
                @endphp
                <th colspan="2" class="text-center fw-bold">{{ number_format($kumulatifRealisasi, 2) }}%</th>
                @endforeach
            </tr>

            {{-- Baris 5: DEVIASI PEKERJAAN --}}
            <tr class="bg-warning">
                <th colspan="6" class="text-center fw-bold">DEVIASI PEKERJAAN</th>
                @php
                $kumulatifRencana = 0;
                $kumulatifRealisasi = 0;
                @endphp
                @foreach($masterMinggu as $minggu)
                @php
                $totalRencana = 0;
                $totalRealisasi = 0;
                foreach($po->progresses as $p) {
                if(!$p->pekerjaan_item_id) continue;
                $d = $p->details->firstWhere('minggu_id', $minggu->id);
                if($d) {
                $totalRencana += (float) $d->bobot_rencana;
                $totalRealisasi += (float) $d->bobot_realisasi;
                }
                }
                $kumulatifRencana += $totalRencana;
                $kumulatifRealisasi += $totalRealisasi;
                $deviasi = $kumulatifRealisasi - $kumulatifRencana;
                $deviasiClass = $deviasi >= 0 ? 'text-success' : 'text-danger';
                @endphp
                <th colspan="2" class="text-center fw-bold {{ $deviasiClass }}">{{ number_format($deviasi, 2) }}%</th>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>