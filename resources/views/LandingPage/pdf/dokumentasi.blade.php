<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $judul }}</title>
    <style>
    @page {
        margin: 12mm 10mm;
        size: A4 portrait;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 7pt;
        line-height: 1.2;
        color: #2c3e50;
    }

    /* Header Dokumen */
    .header {
        text-align: center;
        margin-bottom: 8px;
        padding-bottom: 6px;
        border-bottom: 2px solid #2c3e50;
    }

    .header h1 {
        margin: 0 0 3px 0;
        color: #2c3e50;
        font-size: 12pt;
        text-transform: uppercase;
    }

    .header .subtitle {
        color: #7f8c8d;
        font-size: 7pt;
    }

    /* Page Container */
    .page-container {
        page-break-after: always;
    }

    .page-container:last-child {
        page-break-after: avoid;
    }

    /* Grid Table Layout - 2 kolom untuk setiap baris */
    .photo-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5px;
    }

    .photo-table td {
        width: 50%;
        padding: 3px;
        vertical-align: top;
    }

    /* Container Foto - Fixed Height */
    .photo-container {
        border: 1px solid #ddd;
        background: #fff;
        height: 120mm;
        /* Fixed height untuk konsistensi */
        display: flex;
        flex-direction: column;
    }

    /* Bagian Gambar - Fixed */
    .image-wrapper {
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
        text-align: center;
        height: 70mm;
        /* 70mm untuk gambar */
        overflow: hidden;
        flex-shrink: 0;
    }

    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Info Section - Fixed */
    .photo-info {
        padding: 4px 6px;
        flex-grow: 1;
        overflow: hidden;
    }

    .photo-title {
        font-weight: bold;
        font-size: 7.5pt;
        color: #2c3e50;
        margin-bottom: 3px;
        padding-bottom: 2px;
        border-bottom: 1px solid #eee;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Metadata Table */
    .meta-table {
        width: 100%;
        font-size: 6pt;
        line-height: 1.3;
    }

    .meta-table td {
        padding: 1px 0;
        vertical-align: top;
    }

    .label-col {
        width: 32px;
        color: #666;
        font-weight: 600;
    }

    .sep-col {
        width: 5px;
        color: #666;
    }

    .value-col {
        color: #333;
    }

    .description-box {
        margin-top: 3px;
        padding: 3px;
        background: #f9f9f9;
        border-left: 2px solid #3498db;
        font-size: 5.5pt;
        font-style: italic;
        color: #555;
        max-height: 22px;
        overflow: hidden;
        line-height: 1.2;
    }

    /* Footer */
    .footer {
        position: fixed;
        bottom: 5mm;
        right: 10mm;
        font-size: 6pt;
        color: #999;
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

    @php
    // Chunk into pages of 4 photos
    $photoPages = array_chunk($photos, 4);
    @endphp

    @foreach($photoPages as $pagePhotos)
    <div class="page-container">
        @php
        // Split into 2 rows of 2 photos
        $rows = array_chunk($pagePhotos, 2);
        @endphp

        @foreach($rows as $rowPhotos)
        <table class="photo-table">
            <tr>
                @foreach($rowPhotos as $photo)
                <td>
                    <div class="photo-container">
                        <div class="image-wrapper">
                            @if(!empty($photo['image_data']))
                            <img src="{{ $photo['image_data'] }}" alt="{{ $photo['title'] }}">
                            @else
                            <div style="padding: 25mm 0; color: #999; font-size: 7pt;">
                                Gambar tidak tersedia
                            </div>
                            @endif
                        </div>

                        <div class="photo-info">
                            <div class="photo-title">{{ \Illuminate\Support\Str::limit($photo['title'], 35) }}</div>

                            <table class="meta-table">
                                <tr>
                                    <td class="label-col">Lokasi</td>
                                    <td class="sep-col">:</td>
                                    <td class="value-col">
                                        {{ \Illuminate\Support\Str::limit($photo['location_name'], 22) }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Proyek</td>
                                    <td class="sep-col">:</td>
                                    <td class="value-col">
                                        {{ \Illuminate\Support\Str::limit($photo['project_name'], 22) }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Tanggal</td>
                                    <td class="sep-col">:</td>
                                    <td class="value-col">{{ $photo['date'] }}, {{ $photo['time'] }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Pelapor</td>
                                    <td class="sep-col">:</td>
                                    <td class="value-col">{{ $photo['pelapor'] }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Cuaca</td>
                                    <td class="sep-col">:</td>
                                    <td class="value-col">{{ $photo['weather']['temp'] }}°C -
                                        {{ $photo['weather']['desc'] }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">GPS</td>
                                    <td class="sep-col">:</td>
                                    <td class="value-col">{{ number_format($photo['gps']['lat'], 4) }},
                                        {{ number_format($photo['gps']['lon'], 4) }}</td>
                                </tr>
                            </table>

                            @if(!empty($photo['description']))
                            <div class="description-box">
                                {{ \Illuminate\Support\Str::limit($photo['description'], 55) }}
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
                @endforeach

                {{-- Empty cell if only 1 photo in row --}}
                @if(count($rowPhotos) == 1)
                <td></td>
                @endif
            </tr>
        </table>
        @endforeach
    </div>
    @endforeach

    <div class="footer">
        P-Mones | Hal. <span class="page-number"></span>
    </div>
</body>

</html>