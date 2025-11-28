<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $judul }}</title>
    <style>
    @page {
        margin: 20mm 15mm;
    }

    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 10pt;
        line-height: 1.5;
        color: #2c3e50;
    }

    /* Header Dokumen */
    .header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #2c3e50;
    }

    .header h1 {
        margin: 0 0 10px 0;
        color: #2c3e50;
        font-size: 18pt;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .header .subtitle {
        color: #7f8c8d;
        font-size: 9pt;
    }

    /* Container Foto (Card Style) */
    .photo-container {
        page-break-inside: avoid;
        margin-bottom: 30px;
        border: 1px solid #e0e0e0;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Bagian Gambar */
    .image-wrapper {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        text-align: center;
        padding: 5px;
    }

    .image-wrapper img {
        width: 100%;
        height: auto;
        display: block;
        max-height: 450px;
        object-fit: contain;
        margin: 0 auto;
    }

    /* Bagian Informasi */
    .photo-info {
        padding: 20px 25px;
    }

    .photo-title {
        font-weight: bold;
        font-size: 12pt;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    /* Tabel Metadata untuk Alignment yang Rapi */
    .meta-table {
        width: 100%;
        border-collapse: collapse;
    }

    .meta-table td {
        vertical-align: top;
        padding: 4px 0;
        font-size: 10pt;
    }

    .label-col {
        width: 100px;
        color: #7f8c8d;
        font-weight: 600;
    }

    .sep-col {
        width: 15px;
        color: #7f8c8d;
        text-align: center;
    }

    .value-col {
        color: #34495e;
    }

    /* Styling khusus untuk Deskripsi */
    .description-box {
        margin-top: 15px;
        padding: 10px;
        background-color: #fdfdfd;
        border-left: 3px solid #3498db;
        font-style: italic;
        color: #555;
    }

    /* Footer */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: right;
        font-size: 8pt;
        color: #bdc3c7;
        padding: 10px 15mm;
        background-color: white;
    }

    .page-number:after {
        content: counter(page);
    }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $judul }}</h1>
        <div class="subtitle">
            Total Foto: {{ $total_photos }} &bull; Tanggal Cetak: {{ $tanggal_export }}
        </div>
    </div>

    @foreach($photos as $index => $photo)
    <div class="photo-container">

        <div class="image-wrapper">
            @if($photo['image_data'])
            <img src="{{ $photo['image_data'] }}" alt="{{ $photo['title'] }}">
            @else
            <div style="padding: 60px; color: #999;">
                Gambar tidak tersedia
            </div>
            @endif
        </div>

        <div class="photo-info">
            <div class="photo-title">{{ $photo['title'] }}</div>

            <!-- Menggunakan Table agar titik dua (:) sejajar rapi -->
            <table class="meta-table">
                <tr>
                    <td class="label-col">Lokasi</td>
                    <td class="sep-col">:</td>
                    <td class="value-col">{{ $photo['location_name'] }}</td>
                </tr>
                <tr>
                    <td class="label-col">Proyek</td>
                    <td class="sep-col">:</td>
                    <td class="value-col">{{ $photo['project_name'] }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tanggal</td>
                    <td class="sep-col">:</td>
                    <td class="value-col">{{ $photo['date'] }}, {{ $photo['time'] }} WIB</td>
                </tr>
                <tr>
                    <td class="label-col">Pelapor</td>
                    <td class="sep-col">:</td>
                    <td class="value-col">{{ $photo['pelapor'] }}</td>
                </tr>
                <tr>
                    <td class="label-col">Cuaca</td>
                    <td class="sep-col">:</td>
                    <td class="value-col">
                        {{ $photo['weather']['temp'] }}°C - {{ $photo['weather']['desc'] }}
                        (Kelembaban: {{ $photo['weather']['humidity'] }}%)
                    </td>
                </tr>
                <tr>
                    <td class="label-col">GPS</td>
                    <td class="sep-col">:</td>
                    <td class="value-col">
                        {{ number_format($photo['gps']['lat'], 6) }},
                        {{ number_format($photo['gps']['lon'], 6) }}
                    </td>
                </tr>
            </table>

            @if($photo['description'])
            <div class="description-box">
                <strong>Catatan:</strong><br>
                {{ $photo['description'] }}
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="footer">
        Generated by Sistem P-Mones | Hal. <span class="page-number"></span>
    </div>
</body>

</html>