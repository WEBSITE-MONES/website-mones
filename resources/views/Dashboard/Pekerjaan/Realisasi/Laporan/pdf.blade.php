<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Investasi - {{ $laporan->kode_laporan }}</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    @page {
        margin: 10mm;
        size: A4 landscape;
    }

    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 7px;
        line-height: 1.2;
        color: #000;
    }

    .header {
        text-align: center;
        margin-bottom: 10px;
        padding: 6px;
        background: #4A90E2;
        color: white;
        border-radius: 3px;
    }

    .header h2 {
        margin: 3px 0;
        font-size: 11px;
        font-weight: bold;
    }

    .header p {
        margin: 2px 0;
        font-size: 9px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    table th {
        background: #2E86C1;
        color: white;
        padding: 4px 2px;
        border: 1px solid #000;
        font-size: 7px;
        text-align: center;
        font-weight: bold;
        vertical-align: middle;
    }

    table td {
        padding: 3px 2px;
        border: 1px solid #000;
        font-size: 7px;
        vertical-align: middle;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .bg-info {
        background: #D6EAF8;
        font-weight: bold;
    }

    .bg-warning {
        background: #FCF3CF;
        font-weight: bold;
    }

    .bg-success {
        background: #D5F4E6;
        font-weight: bold;
        font-size: 8px;
    }

    .approval-section {
        margin-top: 20px;
        page-break-inside: avoid;
    }

    .date-info {
        text-align: right;
        margin-bottom: 6px;
        font-size: 8px;
    }

    .approval-container {
        display: table;
        width: 100%;
        margin-top: 12px;
    }

    .approval-box {
        display: table-cell;
        width: 50%;
        text-align: center;
        padding: 6px;
        vertical-align: top;
    }

    .approval-title {
        font-weight: bold;
        font-size: 8px;
        margin-bottom: 4px;
    }

    .signature-space {
        height: 40px;
        margin: 6px 0;
    }

    .status-badge {
        display: inline-block;
        padding: 3px 6px;
        border-radius: 2px;
        font-size: 7px;
        margin: 6px 0;
    }

    .status-approved {
        background: #D5F4E6;
        color: #155724;
        border: 1px solid #28a745;
    }

    .status-rejected {
        background: #FADBD8;
        color: #721c24;
        border: 1px solid #dc3545;
    }

    .status-pending {
        background: #FCF3CF;
        color: #856404;
        border: 1px solid #ffc107;
    }

    .approver-name {
        font-weight: bold;
        text-decoration: underline;
        font-size: 8px;
        margin-top: 4px;
    }

    .footer {
        margin-top: 12px;
        text-align: center;
        font-size: 6px;
        color: #666;
        border-top: 1px solid #ccc;
        padding-top: 4px;
    }

    tr {
        page-break-inside: avoid;
    }

    .no-wrap {
        white-space: nowrap;
    }
    </style>
</head>

<body>
    {{-- HEADER --}}
    <div class="header">
        <h2>LAPORAN BULANAN PROGRAM PEMELIHARAAN</h2>
        <p>PERIODE {{ strtoupper($laporan->nama_bulan ?? 'N/A') }} TAHUN {{ $laporan->tahun }}</p>
        <p style="font-size: 7px;">Kode: {{ $laporan->kode_laporan }}</p>
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th rowspan="3" style="width: 3%;">No</th>
                <th rowspan="3" style="width: 5%;">COA</th>
                <th rowspan="3" style="width: 15%;">Uraian Pekerjaan</th>
                <th rowspan="3" style="width: 4%;">Volume</th>
                <th colspan="2" rowspan="2">RKAP {{ $laporan->tahun }}</th>
                <th colspan="9">Kontrak/SPK/SPB</th>
                <th colspan="2" rowspan="2">Realisasi s.d {{ $laporan->nama_bulan ?? 'N/A' }}</th>
            </tr>
            <tr>
                <th rowspan="2">Nomor Kontrak/PO/SP2</th>
                <th rowspan="2">Tanggal Kontrak/PO/SP2</th>
                <th rowspan="2">Pelaksana Kontrak/PO/SP2</th>
                <th colspan="3">Nilai Kontrak (Non PPn)(Rp)<br>(Multiyears)</th>
                <th rowspan="2">Total Kontrak<br>(Rp)</th>
                <th rowspan="2">Mulai</th>
                <th rowspan="2">Selesai</th>
            </tr>
            <tr>
                <th style="width: 6%;">1 Tahun<br>(Rp)</th>
                <th style="width: 6%;">Target S.D<br>Bulan</th>
                <th style="width: 4%;">2024</th>
                <th style="width: 4%;">2025</th>
                <th style="width: 4%;">2026</th>
                <th style="width: 5%;">Realisasi Fisik<br>(%)</th>
                <th style="width: 7%;">Pembayaran<br>(Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalVolume = 0;
            $totalRkap = 0;
            $totalTarget = 0;
            $totalPembayaran = 0;
            $no = 1;
            @endphp

            @foreach($groupedData as $coa => $group)
            {{-- HEADER COA --}}
            <tr class="bg-info">
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center"><strong>{{ $coa }}</strong></td>
                <td colspan="15">
                    <strong>{{ $group['items'][0]->nama_investasi ?? 'BEBAN INVESTASI' }}</strong>
                </td>
            </tr>

            {{-- DETAIL ITEMS --}}
            @foreach($group['items'] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ $item->nomor_prodef_sap ?? '-' }}</td>
                <td class="text-left">{{ $item->uraian_pekerjaan ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->total_volume ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->nilai_rkap ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->target_sd_bulan ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->nomor_po ?? '-' }}</td>
                <td class="text-center no-wrap">
                    <!-- Ditambahkan no-wrap -->
                    @if(isset($item->tanggal_po))
                    {{ is_string($item->tanggal_po) ? \Carbon\Carbon::parse($item->tanggal_po)->format('d M Y') : $item->tanggal_po->format('d M Y') }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-left">{{ $item->pelaksana ?? '-' }}</td>
                {{-- Kolom Nilai Kontrak Multiyears (2024, 2025, 2026) --}}
                <td class="text-right">0</td>
                <td class="text-right">{{ number_format($item->nilai_rkap ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">0</td>
                {{-- Total Kontrak --}}
                <td class="text-right">{{ number_format($item->nilai_rkap ?? 0, 0, ',', '.') }}</td>
                {{-- Mulai & Selesai Kontrak --}}
                <td class="text-center no-wrap">{{ $item->mulai_kontrak ?? '-' }}</td> <!-- Ditambahkan no-wrap -->
                <td class="text-center no-wrap">{{ $item->selesai_kontrak ?? '-' }}</td> <!-- Ditambahkan no-wrap -->
                {{-- Realisasi --}}
                <td class="text-center">{{ number_format($item->realisasi_fisik ?? 0, 2, ',', '.') }}%</td>
                <td class="text-right">{{ number_format($item->realisasi_pembayaran ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            {{-- SUBTOTAL COA --}}
            <tr class="bg-warning">
                <td colspan="3" class="text-right"><strong>Jumlah :</strong></td>
                <td class="text-right"><strong>{{ number_format($group['subtotal_volume'] ?? 0, 2, ',', '.') }}</strong>
                </td>
                <td class="text-right"><strong>{{ number_format($group['subtotal_rkap'] ?? 0, 0, ',', '.') }}</strong>
                </td>
                <td class="text-right"><strong>{{ number_format($group['subtotal_target'] ?? 0, 0, ',', '.') }}</strong>
                </td>
                <td colspan="6"></td>
                <td class="text-right"><strong>{{ number_format($group['subtotal_rkap'] ?? 0, 0, ',', '.') }}</strong>
                </td>
                <td colspan="2"></td>
                <td></td>
                <td class="text-right">
                    <strong>{{ number_format($group['subtotal_pembayaran'] ?? 0, 0, ',', '.') }}</strong>
                </td>
            </tr>

            @php
            $totalVolume += $group['subtotal_volume'] ?? 0;
            $totalRkap += $group['subtotal_rkap'] ?? 0;
            $totalTarget += $group['subtotal_target'] ?? 0;
            $totalPembayaran += $group['subtotal_pembayaran'] ?? 0;
            @endphp
            @endforeach

            {{-- GRAND TOTAL --}}
            <tr class="bg-success">
                <td colspan="3" class="text-center"><strong>JUMLAH BIAYA INVESTASI</strong></td>
                <td class="text-right"><strong>{{ number_format($totalVolume, 2, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalRkap, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalTarget, 0, ',', '.') }}</strong></td>
                <td colspan="6"></td>
                <td class="text-right"><strong>{{ number_format($totalRkap, 0, ',', '.') }}</strong></td>
                <td colspan="3"></td> {{-- ✅ Kolom ini sudah benar (Mulai, Selesai, Fisik) --}}
                <td class="text-right"><strong>{{ number_format($totalPembayaran, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    {{-- APPROVAL SECTION --}}
    <div class="approval-section">
        <div class="date-info">
            @if(isset($laporan->tanggal_dibuat))
            {{ $laporan->tanggal_dibuat->translatedFormat('l, d F Y') }}
            @else
            {{ now()->translatedFormat('l, d F Y') }}
            @endif
        </div>

        <div class="approval-container">
            @foreach($laporan->approvals as $approval)
            <div class="approval-box">
                <div class="approval-title">
                    {{ $approval->role_approval == 'manager_teknik' ? 'Manager Teknik' : 'Assisten Manager' }}
                </div>

                <div class="signature-space">
                    @if($approval->status == 'approved')
                    <div class="status-badge status-approved">
                        ✓ Approved<br>
                        <small
                            style="font-weight: normal;">{{ $approval->tanggal_approval ? $approval->tanggal_approval->format('d M Y H:i') : '-' }}</small>
                    </div>
                    @elseif($approval->status == 'rejected')
                    <div class="status-badge status-rejected">
                        ✗ Rejected<br>
                        <small
                            style="font-weight: normal;">{{ $approval->tanggal_approval ? $approval->tanggal_approval->format('d M Y H:i') : '-' }}</small>
                    </div>
                    @else
                    <div class="status-badge status-pending">
                        ⏳ Pending Approval
                    </div>
                    @endif
                </div>

                <div class="approver-name">
                    {{ strtoupper($approval->nama_approver) }}
                </div>

                @if($approval->komentar)
                <div style="font-size: 6px; margin-top: 4px; font-style: italic;">
                    "{{ $approval->komentar }}"
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Generated on {{ now()->format('d F Y H:i:s') }} | {{ $laporan->kode_laporan }} |
        Status: {{ strtoupper($laporan->status_approval) }}
    </div>
</body>

</html>