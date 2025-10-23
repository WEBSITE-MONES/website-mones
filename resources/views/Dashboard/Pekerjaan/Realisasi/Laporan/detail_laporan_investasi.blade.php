@extends('Dashboard.base')

@section('title', 'Detail Laporan Investasi')

@section('content')
<div class="page-inner">
    {{-- HEADER --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <h3 class="fw-bold mb-1">{{ strtoupper($laporan->periode_label) }}</h3>
                <p class="text-muted mb-0">Periode {{ $laporan->nama_bulan }} Tahun {{ $laporan->tahun }}</p>
            </div>
            <div class="ms-auto d-flex gap-2">
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
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- STATUS CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
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

    {{-- TABEL LAPORAN --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" style="font-size: 13px;">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th rowspan="2" style="vertical-align: middle;">NO. COA</th>
                            <th rowspan="2" style="vertical-align: middle;">Uraian Pekerjaan</th>
                            <th rowspan="2" style="vertical-align: middle;">Volume<br>(Satuan)</th>
                            <th colspan="2">RKAP {{ $laporan->tahun }}</th>
                            <th colspan="3">Kontrak</th>
                            <th colspan="2">Realisasi s.d {{ $laporan->nama_bulan }}</th>
                        </tr>
                        <tr class="text-center">
                            <th>1 Tahun (Rp)</th>
                            <th>Target S.D Bulan Laporan</th>
                            <th>Nomor Kontrak/PO/SP2</th>
                            <th>Tanggal Kontrak/PO/SP2</th>
                            <th>Pelaksana Kontrak/PO/SP2</th>
                            <th>Fisik (%)</th>
                            <th>Pembayaran (Rp)</th>
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
                        <tr class="table-info">
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
                        <tr class="table-warning fw-bold">
                            <td colspan="2" class="text-end">Jumlah : {{ $coa }}</td>
                            <td class="text-end">{{ number_format($group['subtotal_volume'] ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($group['subtotal_rkap'] ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($group['subtotal_target'] ?? 0, 0) }}</td>
                            <td colspan="4"></td>
                            <td class="text-end">{{ number_format($group['subtotal_pembayaran'] ?? 0, 0) }}</td>
                        </tr>

                        @php
                        $totalVolume += $group['subtotal_volume'] ?? 0;
                        $totalRkap += $group['subtotal_rkap'] ?? 0;
                        $totalTarget += $group['subtotal_target'] ?? 0;
                        $totalPembayaran += $group['subtotal_pembayaran'] ?? 0;
                        @endphp
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted p-5">
                                <i class="fa fa-folder-open fa-3x mb-3"></i>
                                <p class="mb-0">Data tidak ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse

                        {{-- GRAND TOTAL--}}
                        @if(count($groupedData) > 0)
                        <tr class="table-success fw-bold" style="font-size: 14px;">
                            <td colspan="2" class="text-center">JUMLAH BIAYA INVESTASI</td>
                            <td class="text-end">{{ number_format($totalVolume, 2) }}</td>
                            <td class="text-end">{{ number_format($totalRkap, 0) }}</td>
                            <td class="text-end">{{ number_format($totalTarget, 0) }}</td>
                            <td colspan="4"></td>
                            <td class="text-end">{{ number_format($totalPembayaran, 0) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- APPROVAL SECTION --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-12 text-end mb-3">
                    <small class="text-muted">Dibuat pada:
                        {{ $laporan->tanggal_dibuat->translatedFormat('l, d F Y H:i') }}</small>
                </div>
            </div>

            <div class="row text-center">
                @foreach($laporan->approvals as $approval)
                <div class="col-md-6 approval-column">
                    <div class="p-3">
                        <p class="fw-bold mb-1 text-dark">
                            {{ $approval->role_approval == 'manager_teknik' ? 'Manager Teknik' : 'Assisten Manager' }}
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

@push('styles')
<style>
@media (min-width: 768px) {
    .border-md-end {
        border-right: 1px solid #dee2e6;
    }

    .approval-column:not(:last-child) {
        border-right: 1px solid #dee2e6;
    }
}

/* Kustomisasi Tampilan Tabel */
.table {
    font-size: 0.875rem;
    /* 14px */
}

.table thead th {
    padding: 0.75rem 1rem;
    /* 12px 16px */
    white-space: nowrap;
    vertical-align: middle;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.table tbody td {
    padding: 0.75rem 1rem;
    /* 12px 16px */
    vertical-align: middle;
}

.table thead {
    border-bottom: 2px solid #dee2e6;
}

.table tbody tr:last-child {
    border-bottom: 2px solid #dee2e6;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.03);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.table .table-warning td,
.table .table-success td {
    font-weight: 600;
    color: #333;
}

.table .table-info td {
    background-color: #e0f2fe !important;
    /* light-blue */
    color: #0c5460;
    font-weight: 600;
}

/* Rounded modal */
.modal-content {
    border-radius: 0.75rem;
    /* 12px */
}
</style>
@endpush
@endsection