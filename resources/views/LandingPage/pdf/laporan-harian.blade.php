<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - {{ $report->tanggal->format('d-m-Y') }}</title>
    <style>
    @page {
        size: legal;
        /* F4 / Legal: 8.5 x 13 inch */
        margin: 10mm 15mm;
    }

    body {
        font-family: 'Arial', sans-serif;
        font-size: 10pt;
        color: #333;
        line-height: 1.4;
    }

    /* Helper Utilities */
    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .font-bold {
        font-weight: bold;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .mb-1 {
        margin-bottom: 5px;
    }

    .mb-2 {
        margin-bottom: 10px;
    }

    .mt-2 {
        margin-top: 10px;
    }

    /* HEADER DESIGN (Mirip Referensi) */
    .header-container {
        text-align: center;
        margin-bottom: 25px;
    }

    .logo-placeholder {
        height: 60px;
        margin-bottom: 10px;
    }

    .main-title {
        font-size: 14pt;
        font-weight: bold;
        color: #000;
        margin-bottom: 2px;
    }

    .sub-title {
        font-size: 12pt;
        font-weight: bold;
        color: #000;
    }

    /* INFO SECTION */
    .meta-info {
        width: 100%;
        margin-bottom: 20px;
        font-size: 10pt;
    }

    .meta-info td {
        padding: 2px 0;
        vertical-align: top;
    }

    .meta-label {
        width: 140px;
        font-weight: bold;
    }

    .meta-separator {
        width: 15px;
    }

    /* TABLE DESIGN */
    table.custom-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 9pt;
    }

    table.custom-table th {
        border: 1px solid #ccc;
        padding: 8px 5px;
        background-color: #fff;
        font-weight: bold;
        color: #000;
        text-align: center;
        vertical-align: middle;
    }

    table.custom-table td {
        border: 1px solid #ccc;
        padding: 6px 8px;
        vertical-align: top;
        color: #444;
    }

    .section-header {
        font-size: 10pt;
        font-weight: bold;
        margin-bottom: 5px;
        margin-top: 15px;
        text-transform: uppercase;
        color: #000;
    }

    /* Two Column Layout */
    .row-grid {
        display: table;
        width: 100%;
        table-layout: fixed;
        border-spacing: 0 0;
    }

    .col-half {
        display: table-cell;
        width: 50%;
        vertical-align: top;
    }

    .pr-2 {
        padding-right: 10px;
    }

    .pl-2 {
        padding-left: 10px;
    }

    /* Signature - FIXED PAGE BREAK */
    .signature-table {
        width: 100%;
        margin-top: 20px;
        border: 1px solid #ccc;
        page-break-inside: avoid !important;
        page-break-before: auto;
    }

    .signature-table th {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
        font-size: 9pt;
    }

    .signature-table td {
        border: 1px solid #ccc;
        padding: 60px 10px 10px;
        text-align: center;
        vertical-align: bottom;
    }

    /* Photo Grid */
    .photo-container {
        width: 100%;
        margin-top: 10px;
    }

    .photo-item {
        display: inline-block;
        width: 48%;
        margin-bottom: 10px;
        vertical-align: top;
        border: 1px solid #ddd;
        padding: 5px;
    }

    .photo-img {
        width: 100%;
        height: 200px;
        object-fit: contain;
        border: 1px solid #eee;
    }

    .photo-caption {
        font-size: 8pt;
        margin-top: 5px;
        color: #666;
        font-style: italic;
    }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/2/26/Pelindo_2021.png" alt="Logo Pelindo"
            class="logo-placeholder">

        <div class="main-title">LAPORAN HARIAN PEKERJAAN</div>
        <div class="sub-title">{{ strtoupper($namaProyek) }}</div>
    </div>

    <!-- Info Section -->
    <table class="meta-info">
        <tr>
            <td class="meta-label">TANGGAL</td>
            <td class="meta-separator">:</td>
            <td>{{ $report->tanggal->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">PEKERJAAN</td>
            <td class="meta-separator">:</td>
            <td>{{ $report->jenis_pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">LOKASI</td>
            <td class="meta-separator">:</td>
            <td>{{ $report->lokasi_nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">PELAKSANA</td>
            <td class="meta-separator">:</td>
            <td>{{ $report->po->pelaksana ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">NO. KONTRAK</td>
            <td class="meta-separator">:</td>
            <td>{{ $report->po->nomor_po ?? '-' }}</td>
        </tr>
    </table>

    <!-- A. PEKERJAAN -->
    <div class="section-header">A. URAIAN KEGIATAN</div>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 20%;">JENIS PEKERJAAN</th>
                <th style="width: 20%;">LOKASI</th>
                <th style="width: 10%;">VOL</th>
                <th style="width: 10%;">SAT</th>
                <th style="width: 35%;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>{{ $report->jenis_pekerjaan ?? '-' }}</td>
                <td>{{ $report->lokasi_nama ?? '-' }}</td>
                <td class="text-center">{{ number_format($report->volume_realisasi, 2) }}</td>
                <td class="text-center">{{ $report->satuan ?? '-' }}</td>
                <td>{{ $report->deskripsi ?? '-' }}</td>
            </tr>
            @for($i=0; $i<2; $i++) <tr>
                <td style="height: 20px;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tr>
                @endfor
        </tbody>
    </table>

    <!-- Layout 2 Kolom untuk Material & Alat -->
    <div class="row-grid">
        <div class="col-half pr-2">
            <div class="section-header">B. BAHAN / MATERIAL</div>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">NO</th>
                        <th style="width: 85%;">URAIAN MATERIAL</th>
                    </tr>
                </thead>
                <tbody>
                    @if($report->material)
                    @php
                    $materials = explode(',', $report->material);
                    $no = 1;
                    @endphp
                    @foreach(array_slice($materials, 0, 5) as $material)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ trim($material) }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="2" class="text-center">- Tidak ada material -</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="col-half pl-2">
            <div class="section-header">C. PERALATAN KERJA</div>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">NO</th>
                        <th style="width: 85%;">JENIS PERALATAN</th>
                    </tr>
                </thead>
                <tbody>
                    @if($report->alat_berat)
                    @php
                    $alats = explode(',', $report->alat_berat);
                    $no = 1;
                    @endphp
                    @foreach(array_slice($alats, 0, 5) as $alat)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ trim($alat) }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="2" class="text-center">- Tidak ada peralatan -</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Layout 2 Kolom untuk Tenaga Kerja & Cuaca -->
    <div class="row-grid mt-2">
        <div class="col-half pr-2">
            <div class="section-header">D. TENAGA KERJA</div>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">JABATAN</th>
                        <th style="width: 30%;">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pekerja Lapangan</td>
                        <td class="text-center">{{ $report->jumlah_pekerja ?? 0 }} Orang</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-half pl-2">
            <div class="section-header">E. KONDISI CUACA</div>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>KONDISI</th>
                        <th>WAKTU</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $report->cuaca_deskripsi ?? '-' }}</td>
                        <td class="text-center">{{ $report->jam_kerja ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Catatan -->
    @if($report->kendala || $report->solusi || $report->rencana_besok)
    <div class="section-header">F. CATATAN / INSTRUKSI</div>
    <table class="custom-table">
        <tr>
            <td style="width: 20%; font-weight:bold; background-color: #f9f9f9;">KENDALA</td>
            <td>{{ $report->kendala ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 20%; font-weight:bold; background-color: #f9f9f9;">SOLUSI</td>
            <td>{{ $report->solusi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 20%; font-weight:bold; background-color: #f9f9f9;">RENCANA BESOK</td>
            <td>{{ $report->rencana_besok ?? '-' }}</td>
        </tr>
    </table>
    @endif

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <thead>
            <tr>
                <th>KONTRAKTOR</th>
                <th>OWNER / PENGAWAS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="font-bold underline">{{ $report->pelapor->name ?? '......................' }}</div>
                    <div>Pelaksana</div>
                </td>
                <td>
                    <div class="font-bold underline">{{ $report->approver->name ?? '......................' }}</div>
                    <div>Pengawas</div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="text-right" style="font-size: 8pt; margin-top: 10px; color: #888;">
        <i>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</i>
    </div>

    <!-- Halaman Foto (Jika Ada) -->
    @if(!empty($photos) && count($photos) > 0)
    <div style="page-break-before: always;"></div>

    <div class="header-container">
        <div class="main-title">DOKUMENTASI PEKERJAAN</div>
        <div class="sub-title">{{ $report->tanggal->format('d F Y') }}</div>
    </div>

    <div class="photo-container">
        @foreach($photos as $photo)
        <div class="photo-item">
            <img src="{{ $photo['data'] }}" class="photo-img">
            <div class="photo-caption">
                <b>{{ $photo['location'] }}</b><br>
                {{ number_format($photo['gps']['lat'], 6) }}, {{ number_format($photo['gps']['lon'], 4) }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

</body>

</html>