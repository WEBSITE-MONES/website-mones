@extends('Dashboard.base')

@section('title', 'Edit Payment Request')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/payment.css') }}">
@endpush

@section('content')
<div class="page-inner">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Edit Payment Request</h2>
            <h5 class="fw-normal text-muted">Perbarui termin yang akan dibayar dan dokumen pendukung.</h5>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- FORM UTAMA --}}
    <form action="{{ route('realisasi.updatePayment', [$pr->id, $payment->id]) }}" method="POST"
        enctype="multipart/form-data" id="paymentForm" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="gr_id" value="{{ $gr->id }}">

        <div id="validation-alert" class="alert alert-danger d-none align-items-center" role="alert">
            <i class="fas fa-exclamation-circle me-3"></i>
            <div id="validation-message"></div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- KARTU 1: PILIH TERMIN --}}
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                            <h5 class="card-title fw-bold mb-0">
                                <i class="fas fa-layer-group text-primary me-2"></i>
                                Termin yang Akan Dibayar
                            </h5>
                            <span class="badge bg-primary-subtle text-primary-emphasis" id="selectedCount">
                                {{ $selectedTermins->count() }} Termin Dipilih
                            </span>
                        </div>
                        <div class="alert alert-info border-0 mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Tips:</strong> Anda dapat menambah atau mengurangi termin dari payment request ini.
                        </div>

                        <div class="termin-selection-container">
                            {{-- Termin yang sudah terpilih sebelumnya --}}
                            @foreach($selectedTermins as $termin)
                            <div class="form-check termin-option">
                                <input class="form-check-input termin-checkbox" type="checkbox" name="termin_ids[]"
                                    value="{{ $termin->id }}" id="termin-{{ $termin->id }}"
                                    data-nilai="{{ $termin->nilai_pembayaran }}" data-uraian="{{ $termin->uraian }}"
                                    checked>
                                <label class="form-check-label" for="termin-{{ $termin->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="termin-details">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="termin-name">{{ $termin->uraian }}</span>
                                                @if($termin->persentase)
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis ms-2">
                                                    {{ $termin->persentase }}%
                                                </span>
                                                @endif
                                            </div>
                                            @if($termin->syarat_pembayaran)
                                            <small class="text-muted d-block">
                                                <i class="fas fa-clipboard-check me-1"></i>
                                                Syarat Pembayaran: <strong>{{ $termin->syarat_pembayaran }}</strong>
                                            </small>
                                            @endif
                                        </div>
                                        <div class="termin-value">
                                            Rp {{ number_format($termin->nilai_pembayaran, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach

                            {{-- Termin yang belum dibayar (bisa ditambahkan) --}}
                            @foreach($availableTermins as $termin)
                            <div class="form-check termin-option">
                                <input class="form-check-input termin-checkbox" type="checkbox" name="termin_ids[]"
                                    value="{{ $termin->id }}" id="termin-{{ $termin->id }}"
                                    data-nilai="{{ $termin->nilai_pembayaran }}" data-uraian="{{ $termin->uraian }}">
                                <label class="form-check-label" for="termin-{{ $termin->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="termin-details">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="termin-name">{{ $termin->uraian }}</span>
                                                @if($termin->persentase)
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis ms-2">
                                                    {{ $termin->persentase }}%
                                                </span>
                                                @endif
                                            </div>
                                            @if($termin->syarat_pembayaran)
                                            <small class="text-muted d-block">
                                                <i class="fas fa-clipboard-check me-1"></i>
                                                Syarat Pembayaran: <strong>{{ $termin->syarat_pembayaran }}</strong>
                                            </small>
                                            @endif
                                        </div>
                                        <div class="termin-value">
                                            Rp {{ number_format($termin->nilai_pembayaran, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('termin_ids')
                        <div class="alert alert-danger mt-3">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- KARTU 2: INFORMASI PEMBAYARAN --}}
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Informasi Pembayaran
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_payment" class="form-label fw-semibold">Tanggal Payment <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_payment" id="tanggal_payment"
                                    class="form-control form-control-lg"
                                    value="{{ old('tanggal_payment', $payment->tanggal_payment) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_payment" class="form-label fw-semibold">Nomor Payment Request <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nomor_payment" id="nomor_payment"
                                    class="form-control form-control-lg"
                                    value="{{ old('nomor_payment', $payment->nomor_payment) }}"
                                    placeholder="Contoh: PAY/2024/001" required>
                                @error('nomor_payment')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KARTU 3: LAMPIRAN DOKUMEN --}}
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-paperclip text-primary me-2"></i>
                            Lampiran Dokumen (.pdf)
                        </h5>
                        <div class="row">
                            @php
                            function renderFileUploadEdit($id, $name, $label, $icon, $existingFile) {
                            @endphp
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold"><i
                                        class="fas {{ $icon }} me-2 text-primary"></i>{{ $label }}</label>

                                @if($existingFile)
                                <div class="alert alert-success mb-2 py-2 px-3">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    <small>File tersedia:
                                        <a href="{{ Storage::url($existingFile) }}" target="_blank"
                                            class="text-decoration-none fw-bold">
                                            {{ basename($existingFile) }}
                                        </a>
                                    </small>
                                </div>
                                @endif

                                <div class="custom-file-container">
                                    <label for="{{ $id }}" class="custom-file-upload-label">
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <p class="upload-text">
                                                {{ $existingFile ? 'Ganti file PDF' : 'Pilih file PDF' }}</p>
                                        </div>
                                    </label>
                                    <div class="file-info-container d-none">
                                        <i class="fas fa-file-pdf file-icon"></i>
                                        <span class="file-name"></span>
                                        <button type="button" class="btn-remove-file">&times;</button>
                                    </div>
                                    <input type="file" id="{{ $id }}" name="{{ $name }}" class="d-none file-input"
                                        accept=".pdf">
                                </div>
                            </div>
                            @php
                            }
                            @endphp

                            {{ renderFileUploadEdit('invoice', 'invoice', 'Invoice', 'fa-file-invoice', $payment->invoice) }}
                            {{ renderFileUploadEdit('receipt', 'receipt', 'Kwitansi', 'fa-receipt', $payment->receipt) }}
                            {{ renderFileUploadEdit('nodin_payment', 'nodin_payment', 'Nodin Permohonan Pembayaran', 'fa-file-signature', $payment->nodin_payment) }}
                            {{ renderFileUploadEdit('bill', 'bill', 'Surat Tagihan', 'fa-file-contract', $payment->bill) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="card card-round shadow-sm border-0 mb-4">
                        <div class="payment-summary-header">
                            <h6 class="text-white-50 mb-1">TOTAL NILAI PAYMENT</h6>
                            <h2 class="fw-bolder mb-0" id="grandTotalPayment">
                                Rp {{ number_format($selectedTermins->sum('nilai_pembayaran'), 0, ',', '.') }}
                            </h2>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-list-check me-2"></i>Rincian
                                Termin</h6>
                            <div id="selectedTerminsList">
                                @if($selectedTermins->isEmpty())
                                <div class="text-center text-muted py-4" id="emptyState">
                                    <i class="fas fa-hand-pointer fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0 small">Belum ada termin yang dipilih.</p>
                                </div>
                                @endif
                            </div>
                            <hr class="my-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-save me-2"></i> Update Payment Request
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i> Batal
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- INFO GR CARD --}}
                    <div class="card card-round shadow-sm border-0">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3"><i class="fas fa-archive text-primary me-2"></i>Informasi GR</h6>
                            @php
                            $totalPaid = \App\Models\Termin::where('po_id', $pr->po->id)
                            ->whereNotNull('payment_id')
                            ->where('payment_id', '!=', $payment->id)
                            ->sum('nilai_pembayaran');
                            $totalUnpaid = \App\Models\Termin::where('po_id', $pr->po->id)
                            ->whereNull('payment_id')
                            ->sum('nilai_pembayaran');
                            $currentPaymentTotal = $selectedTermins->sum('nilai_pembayaran');
                            @endphp
                            <div class="info-item">
                                <small>Total Nilai GR</small>
                                <strong class="text-primary">Rp {{ number_format($gr->nilai_gr, 0, ',', '.') }}</strong>
                            </div>
                            <div class="info-item">
                                <small>Sudah Dibayar (Lainnya)</small>
                                <strong class="text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</strong>
                            </div>
                            <div class="info-item">
                                <small>Payment Ini</small>
                                <strong class="text-info">Rp
                                    {{ number_format($currentPaymentTotal, 0, ',', '.') }}</strong>
                            </div>
                            <div class="info-item">
                                <small>Belum Dibayar</small>
                                <strong class="text-danger">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/payment.js') }}"></script>
@endpush