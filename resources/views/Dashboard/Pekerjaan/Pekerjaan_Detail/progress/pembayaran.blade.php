@extends('Dashboard.base')

@section('title', 'Kesimpulan Pembayaran')

@section('content')
<div class="page-inner">
    {{-- Header --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0 text-dark">Pembayaran</h4>
                            <small>{{ $pekerjaan->nama_pekerjaan }}</small>
                        </div>
                        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                    // Hitung total dari SEMUA sub pekerjaan (PRs)
                    $allPrs = $pekerjaan->prs;
                    $totalKontrakKeseluruhan = 0;
                    $totalDibayarKeseluruhan = 0;

                    foreach($allPrs as $pr) {
                    if($pr->po) {
                    $totalKontrakKeseluruhan += $pr->po->grand_total;
                    if($pr->payments) {
                    $totalDibayarKeseluruhan += $pr->payments->sum('nilai_payment');
                    }
                    }
                    }

                    $sisaBelumBayar = $totalKontrakKeseluruhan - $totalDibayarKeseluruhan;
                    $progressPersen = $totalKontrakKeseluruhan > 0 ? ($totalDibayarKeseluruhan /
                    $totalKontrakKeseluruhan * 100) : 0;
                    @endphp

                    {{-- Overview Cards TOTAL KESELURUHAN --}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body p-3">
                                    <h6 class="mb-1">Total Kontrak Keseluruhan</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalKontrakKeseluruhan, 0, ',', '.') }}</h4>
                                    <small>{{ $allPrs->count() }} Sub Pekerjaan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body p-3">
                                    <h6 class="mb-1">Total Sudah Dibayar</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalDibayarKeseluruhan, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body p-3">
                                    <h6 class="mb-1">Total Sisa Belum Bayar</h6>
                                    <h4 class="mb-0">Rp {{ number_format($sisaBelumBayar, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body p-3">
                                    <h6 class="mb-1">Progress Keseluruhan</h6>
                                    <h4 class="mb-0">{{ number_format($progressPersen, 1) }}%</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar Keseluruhan --}}
                    <div class="mt-3">
                        <label><strong>Progress Pembayaran Keseluruhan</strong></label>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: {{ $progressPersen }}%;"
                                aria-valuenow="{{ $progressPersen }}" aria-valuemin="0" aria-valuemax="100">
                                {{ number_format($progressPersen, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL PER SUB PEKERJAAN --}}
    @foreach($allPrs as $indexPr => $pr)
    @php
    $po = $pr->po;
    if(!$po) continue;

    $allTermins = $po->termins;
    $totalKontrak = $po->grand_total;
    $payments = $pr->payments;
    $totalDibayar = $payments->sum('nilai_payment');
    $sisaBelumBayar = $totalKontrak - $totalDibayar;
    $progressPersen = $totalKontrak > 0 ? ($totalDibayar / $totalKontrak * 100) : 0;
    @endphp

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                {{ optional($pekerjaan->subPekerjaan->get($indexPr))->nama_sub ?? '-' }}

                            </h5>
                            <small class="text-muted">PO: {{ $po->nomor_po ?? '-' }}</small>
                        </div>
                        <div>
                            <span
                                class="badge badge-{{ $progressPersen >= 100 ? 'success' : ($progressPersen > 0 ? 'warning' : 'secondary') }}">
                                {{ number_format($progressPersen, 1) }}% Terbayar
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <small class="text-muted">Nilai Kontrak</small>
                                <h5 class="mb-0">Rp {{ number_format($totalKontrak, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <small class="text-muted">Sudah Dibayar</small>
                                <h5 class="mb-0 text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 bg-light">
                                <small class="text-muted">Sisa Belum Bayar</small>
                                <h5 class="mb-0 text-warning">Rp {{ number_format($sisaBelumBayar, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar Sub --}}
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPersen }}%;">
                            {{ number_format($progressPersen, 1) }}%
                        </div>
                    </div>

                    {{-- Timeline Payments untuk Sub ini --}}
                    @if($payments->count() > 0)
                    <h6 class="mt-4 mb-3"><i class="fas fa-history"></i> Riwayat Pembayaran</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Nomor Payment</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="15%">Termin</th>
                                    <th width="20%">Nilai</th>
                                    <th width="25%">Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments->sortBy('tanggal_payment') as $index => $payment)
                                @php
                                $terminsPaid = $payment->termins;
                                $terminNumbers = $terminsPaid->pluck('nomor_termin')->join(', ');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $payment->nomor_payment }}</strong></td>
                                    <td>
                                        <i class="far fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($payment->tanggal_payment)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $terminNumbers }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            Rp {{ number_format($payment->nilai_payment, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($payment->invoice)
                                        <a href="{{ Storage::url($payment->invoice) }}" target="_blank"
                                            class="btn btn-xs btn-info" title="Invoice">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @endif
                                        @if($payment->receipt)
                                        <a href="{{ Storage::url($payment->receipt) }}" target="_blank"
                                            class="btn btn-xs btn-info" title="Receipt">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        @endif
                                        @if($payment->bill)
                                        <a href="{{ Storage::url($payment->bill) }}" target="_blank"
                                            class="btn btn-xs btn-info" title="Bill">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        @endif
                                        @if($payment->nodin_payment)
                                        <a href="{{ Storage::url($payment->nodin_payment) }}" target="_blank"
                                            class="btn btn-xs btn-info" title="Nodin">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th colspan="4" class="text-right">Total Terbayar:</th>
                                    <th colspan="2" class="text-success">
                                        Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Detail Termin --}}
                    <h6 class="mt-4 mb-3"><i class="fas fa-list"></i> Detail Status Termin</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Nomor Termin</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="20%">Nilai</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">No. Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allTermins as $termin)
                                <tr class="{{ $termin->payment_id ? 'table-success' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $termin->nomor_termin }}</strong></td>
                                    <td>{{ $termin->deskripsi ?? '-' }}</td>
                                    <td>Rp {{ number_format($termin->nilai_pembayaran, 0, ',', '.') }}</td>
                                    <td>
                                        @if($termin->payment_id)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Lunas
                                        </span>
                                        @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Belum
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($termin->payment)
                                        {{ $termin->payment->nomor_payment }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-info-circle"></i> Belum ada pembayaran untuk sub pekerjaan ini.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Summary Akhir --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round bg-light">
                <div class="card-body text-center">
                    <h5>📊 Ringkasan Akhir</h5>
                    <p class="mb-0">
                        Total <strong>{{ $allPrs->count() }}</strong> Sub Pekerjaan |
                        Nilai Kontrak: <strong class="text-primary">Rp
                            {{ number_format($totalKontrakKeseluruhan, 0, ',', '.') }}</strong> |
                        Sudah Dibayar: <strong class="text-success">Rp
                            {{ number_format($totalDibayarKeseluruhan, 0, ',', '.') }}</strong> |
                        Sisa: <strong class="text-warning">Rp {{ number_format($sisaBelumBayar, 0, ',', '.') }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-success {
    background-color: #d4edda !important;
}

.btn-xs {
    padding: 2px 6px;
    font-size: 10px;
}
</style>
@endsection