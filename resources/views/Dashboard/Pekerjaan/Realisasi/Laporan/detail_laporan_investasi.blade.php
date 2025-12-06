@extends('Dashboard.base')

@section('title', 'Detail Laporan Investasi')

@section('content')

<style>
/* GENERAL STYLES */
@media (min-width: 768px) {
    .border-md-end {
        border-right: 1px solid #dee2e6;
    }

    .approval-column:not(:last-child) {
        border-right: 1px solid #dee2e6;
    }
}

/* REPORT HEADER - STYLE PELINDO */
.report-header {
    page-break-inside: avoid;
    border-bottom: 2px solid #333;
}

/* TABEL PELINDO STYLE - BORDER HITAM POLOS */
.table-pelindo {
    width: 100%;
    border-collapse: collapse !important;
    font-size: 9px;
    margin: 0;
    border: 1.5px solid #000;
}

.table-pelindo thead {
    background-color: transparent;
}

.table-pelindo th {
    padding: 6px 4px;
    font-weight: 700;
    border: 1px solid #000 !important;
    vertical-align: middle;
    text-align: center;
    font-size: 8px;
    line-height: 1.3;
    text-transform: uppercase;
    background-color: transparent;
}

.table-pelindo tbody td {
    padding: 5px 4px;
    border: 1px solid #000 !important;
    vertical-align: middle;
    font-size: 8px;
    line-height: 1.2;
}

.table-pelindo tr {
    border: 1px solid #000;
}

/* HEADER COA ROW */
.table-pelindo .header-coa td {
    background-color: transparent !important;
    font-weight: 700;
    border: 1px solid #000 !important;
}

/* SUBTOTAL ROW */
.table-pelindo .subtotal-row td {
    background-color: transparent !important;
    font-weight: 700;
    border: 1px solid #000 !important;
}

/* GRAND TOTAL ROW */
.table-pelindo .grand-total-row td {
    background-color: transparent !important;
    font-weight: 700;
    font-size: 9px;
    border: 1px solid #000 !important;
}

.table-pelindo tbody tr:hover {
    background-color: #fafafa;
}

/* APPROVAL SECTION PRINT */
.approval-print {
    margin-top: 40px;
    page-break-inside: avoid;
}

.approval-print table {
    width: 100%;
    border-collapse: collapse;
}

.approval-print td {
    padding: 10px;
    text-align: center;
    vertical-align: top;
    border: 1px solid #000;
    font-size: 10px;
}

.modal-content {
    border-radius: 0.75rem;
}

/* PRINT STYLING */
@media print {

    /* Sembunyikan elemen dashboard */
    body * {
        visibility: hidden;
    }

    .sidebar,
    .main-header,
    .main-footer,
    .no-print,
    nav,
    aside,
    .page-header,
    .alert,
    button,
    .btn {
        display: none !important;
        visibility: hidden !important;
    }

    /* Tampilkan area laporan */
    #printable-report,
    #printable-report * {
        visibility: visible !important;
    }

    #printable-report {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .print-area {
        box-shadow: none !important;
        border: none !important;
    }

    .card,
    .card-body {
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
    }

    .card-body {
        padding: 10px !important;
    }

    /* Tabel */
    .table-pelindo {
        page-break-inside: auto;
        width: 100%;
        font-size: 8px;
    }

    .table-pelindo tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    .table-pelindo thead {
        display: table-header-group;
    }

    .table-pelindo th,
    .table-pelindo td {
        border: 1px solid #000 !important;
        padding: 4px 3px !important;
    }

    /* Approval section */
    .approval-print {
        visibility: visible !important;
        display: block !important;
        page-break-before: auto;
    }

    body {
        background: white !important;
        margin: 0;
        padding: 0;
    }

    /* Page settings LANDSCAPE */
    @page {
        size: A4 landscape;
        margin: 1cm 0.8cm;
    }
}
</style>
<div class="page-inner">
    {{-- HEADER --}}
    <div class="page-header mb-4 no-print">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <h3 class="fw-bold mb-1">{{ strtoupper($laporan->periode_label) }}</h3>
                <p class="text-muted mb-0">Periode {{ $laporan->nama_bulan }} Tahun {{ $laporan->tahun }}</p>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-2 px-3">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn btn-info rounded-pill px-3 shadow-sm">
                    <i class="fa fa-print"></i> Print
                </button>
                <a href="{{ route('laporan.export.pdf', $laporan->id) }}"
                    class="btn btn-danger rounded-pill px-3 shadow-sm">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('laporan.export.excel', $laporan->id) }}"
                    class="btn btn-success rounded-pill px-3 shadow-sm">
                    <i class="fa fa-file-excel"></i> Excel
                </a>
            </div>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 no-print">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 no-print">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- STATUS CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-3 mb-3 mb-md-0 border-md-end">
                    <small class="text-muted">Kode Laporan</small>
                    <h5 class="fw-bold mb-0">{{ $laporan->kode_laporan }}</h5>
                </div>
                <div class="col-md-3 mb-3 mb-md-0 border-md-end">
                    <small class="text-muted">Status Approval</small>
                    <div>{!! $laporan->status_badge !!}</div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0 border-md-end">
                    <small class="text-muted">Dibuat Oleh</small>
                    <p class="mb-0 fw-medium">{{ $laporan->pembuatLaporan->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3 text-md-end">
                    @if($laporan->status_approval == 'draft')
                    <form action="{{ route('laporan.submit', $laporan->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"
                            onclick="return confirm('Submit laporan untuk approval?')">
                            <i class="fa fa-paper-plane"></i> Send Report
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- LAPORAN AREA - DENGAN HEADER PELINDO --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 print-area" id="printable-report">
        <div class="card-body p-4">
            {{-- HEADER LAPORAN PELINDO --}}
            <div class="report-header text-center mb-3 pb-2">
                {{-- Logo Pelindo --}}
                <div class="mb-2">
                    <img src="{{ asset('assets/img/logo-pelindo.png') }}" alt="Logo Pelindo" style="height: 50px;">
                </div>

                {{-- Judul Laporan --}}
                <h5 class="fw-bold text-uppercase mb-1" style="font-size: 14px; letter-spacing: 0.5px;">
                    LAPORAN BULANAN {{ strtoupper($laporan->periode_label) }}
                </h5>
                <h6 class="text-uppercase mb-1" style="font-size: 13px; font-weight: 600;">
                    BULAN {{ strtoupper($laporan->nama_bulan) }} {{ $laporan->tahun }}
                </h6>
                <p class="text-muted mb-0" style="font-size: 11px;">
                    PT. Pelabuhan Indonesia (Persero) Regional 4
                </p>
            </div>

            {{-- TABEL LAPORAN --}}
            <div class="table-responsive">
                <table class="table-pelindo">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2" style="vertical-align: middle; width: 70px;">NO. COA</th>
                            <th rowspan="2" style="vertical-align: middle; min-width: 150px;">URAIAN PEKERJAAN</th>
                            <th rowspan="2" style="vertical-align: middle; width: 60px;">VOLUME<br>(SATUAN)</th>
                            <th colspan="2">RKAP {{ $laporan->tahun }}</th>
                            <th colspan="3">KONTRAK</th>
                            <th colspan="2">REALISASI S.D {{ strtoupper($laporan->nama_bulan) }}</th>
                        </tr>
                        <tr class="text-center">
                            <th style="min-width: 90px;">1 TAHUN (RP)</th>
                            <th style="min-width: 90px;">TARGET S.D BULAN LAPORAN</th>
                            <th style="min-width: 100px;">NOMOR KONTRAK/PO/SP2</th>
                            <th style="min-width: 80px;">TANGGAL KONTRAK/PO/SP2</th>
                            <th style="min-width: 100px;">PELAKSANA KONTRAK/PO/SP2</th>
                            <th style="width: 50px;">FISIK (%)</th>
                            <th style="min-width: 90px;">PEMBAYARAN (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $totalVolume = 0;
                        $totalRkap = 0;
                        $totalTarget = 0;
                        $totalPembayaran = 0;
                        @endphp

                        @forelse($groupedData as $coa => $group)
                        {{-- HEADER COA --}}
                        <tr class="header-coa">
                            <td><strong>{{ $coa }}</strong></td>
                            <td colspan="9">
                                <strong>{{ $group['items'][0]->nama_investasi ?? 'BEBAN INVESTASI' }}</strong>
                            </td>
                        </tr>

                        {{-- DETAIL ITEMS --}}
                        @foreach($group['items'] as $item)
                        <tr>
                            <td class="text-center">{{ $item->nomor_prodef_sap ?? '-' }}</td>
                            <td>{{ $item->uraian_pekerjaan ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item->total_volume ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($item->nilai_rkap ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($item->target_sd_bulan ?? 0, 0) }}</td>
                            <td class="text-center">{{ $item->nomor_po ?? '-' }}</td>
                            <td class="text-center">
                                @if(isset($item->tanggal_po) && $item->tanggal_po)
                                {{ is_string($item->tanggal_po) ? \Carbon\Carbon::parse($item->tanggal_po)->format('d M Y') : $item->tanggal_po->format('d M Y') }}
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ $item->pelaksana ?? '-' }}</td>
                            <td class="text-center">{{ number_format($item->realisasi_fisik ?? 0, 2) }}%</td>
                            <td class="text-end">{{ number_format($item->realisasi_pembayaran ?? 0, 0) }}</td>
                        </tr>
                        @endforeach

                        {{-- SUBTOTAL COA --}}
                        <tr class="subtotal-row">
                            <td colspan="2" class="text-end"><strong>Jumlah : {{ $coa }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($group['subtotal_volume'] ?? 0, 2) }}</strong>
                            </td>
                            <td class="text-end"><strong>{{ number_format($group['subtotal_rkap'] ?? 0, 0) }}</strong>
                            </td>
                            <td class="text-end"><strong>{{ number_format($group['subtotal_target'] ?? 0, 0) }}</strong>
                            </td>
                            <td colspan="4"></td>
                            <td class="text-end">
                                <strong>{{ number_format($group['subtotal_pembayaran'] ?? 0, 0) }}</strong>
                            </td>
                        </tr>

                        @php
                        $totalVolume += $group['subtotal_volume'] ?? 0;
                        $totalRkap += $group['subtotal_rkap'] ?? 0;
                        $totalTarget += $group['subtotal_target'] ?? 0;
                        $totalPembayaran += $group['subtotal_pembayaran'] ?? 0;
                        @endphp
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted p-4">
                                Data tidak ditemukan.
                            </td>
                        </tr>
                        @endforelse

                        {{-- GRAND TOTAL--}}
                        @if(count($groupedData) > 0)
                        <tr class="grand-total-row">
                            <td colspan="2" class="text-center"><strong>JUMLAH BIAYA INVESTASI</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalVolume, 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalRkap, 0) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalTarget, 0) }}</strong></td>
                            <td colspan="4"></td>
                            <td class="text-end"><strong>{{ number_format($totalPembayaran, 0) }}</strong></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- INFO FOOTER --}}
            <div class="mt-3 pt-2" style="border-top: 1px solid #ddd;">
                <div class="row">
                    <div class="col-6">
                        <small style="font-size: 10px;">Kode Laporan:
                            <strong>{{ $laporan->kode_laporan }}</strong></small>
                    </div>
                    <div class="col-6 text-end">
                        <small style="font-size: 10px;">Dibuat pada:
                            <strong>{{ $laporan->tanggal_dibuat->format('d F Y') }}</strong></small>
                    </div>
                </div>
            </div>

            {{-- APPROVAL SECTION FOR PRINT --}}
            <div class="approval-print mt-4">
                <table>
                    <tr>
                        @foreach($laporan->approvals as $approval)
                        <td>
                            <div>
                                <strong style="display: block; margin-bottom: 5px;">{{ $approval->role_label }}</strong>
                                <div style="min-height: 40px; margin: 10px 0;">
                                    @if($approval->status == 'approved')
                                    <span style="font-size: 9px;">✓ Approved</span>
                                    @elseif($approval->status == 'rejected')
                                    <span style="font-size: 9px;">✗ Rejected</span>
                                    @else
                                    <span style="font-size: 9px;">( Pending )</span>
                                    @endif
                                </div>
                                <strong style="display: block;">{{ strtoupper($approval->nama_approver) }}</strong>
                                @if($approval->tanggal_approval)
                                <small style="font-size: 8px; display: block; margin-top: 3px;">
                                    {{ $approval->tanggal_approval->format('d M Y') }}
                                </small>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- APPROVAL SECTION (SCREEN ONLY) --}}
    <div class="card border-0 shadow-sm rounded-4 no-print">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-12 text-end mb-3">
                    <small class="text-muted">Dibuat pada:
                        {{ $laporan->tanggal_dibuat->translatedFormat('l, d F Y H:i') }}</small>
                </div>
            </div>

            <div class="row text-center">
                @foreach($laporan->approvals as $approval)
                @php
                $colWidth = 12 / max($laporan->approvals->count(), 1);
                $colWidth = min($colWidth, 6);
                @endphp
                <div class="col-md-{{ $colWidth }} approval-column mb-3">
                    <div class="p-3">
                        <p class="fw-bold mb-1 text-dark">
                            {{ $approval->role_label }}
                        </p>

                        <div class="my-3" style="min-height: 60px;">
                            @if($approval->status == 'pending')
                            {!! $approval->status_badge !!}

                            @if(Auth::id() == $approval->user_id && $laporan->status_approval == 'pending')
                            <div class="mt-2 d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3"
                                    data-bs-toggle="modal" data-bs-target="#approveModal{{ $approval->id }}">
                                    <i class="fa fa-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $approval->id }}">
                                    <i class="fa fa-times"></i> Reject
                                </button>
                            </div>
                            @endif
                            @else
                            {!! $approval->status_badge !!}
                            <p class="text-muted small mb-0 mt-1">
                                {{ $approval->tanggal_approval ? $approval->tanggal_approval->format('d M Y H:i') : '-' }}
                            </p>
                            @endif
                        </div>

                        <p class="fw-bold mb-0 text-dark">{{ strtoupper($approval->nama_approver) }}</p>

                        @if($approval->komentar)
                        <div class="mt-2">
                            <small class="text-muted fst-italic">"{{ $approval->komentar }}"</small>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- MODAL APPROVE --}}
                <div class="modal fade" id="approveModal{{ $approval->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3">
                            <form action="{{ route('laporan.approve', $laporan->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="approval_id" value="{{ $approval->id }}">
                                <div class="modal-header">
                                    <h5 class="modal-title">Approve Laporan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label">Komentar (Opsional)</label>
                                        <textarea name="komentar" class="form-control" rows="3"
                                            placeholder="Tambahkan komentar..."></textarea>
                                    </div>
                                    <p class="mb-0">Apakah Anda yakin ingin menyetujui laporan ini?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary rounded-pill"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-pill">
                                        <i class="fa fa-check"></i> Ya, Approve
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- MODAL REJECT --}}
                <div class="modal fade" id="rejectModal{{ $approval->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3">
                            <form action="{{ route('laporan.reject', $laporan->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="approval_id" value="{{ $approval->id }}">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Reject Laporan</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label">Alasan Reject <span
                                                class="text-danger">*</span></label>
                                        <textarea name="komentar" class="form-control" rows="4"
                                            placeholder="Jelaskan alasan reject..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary rounded-pill"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger rounded-pill">
                                        <i class="fa fa-times"></i> Ya, Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection