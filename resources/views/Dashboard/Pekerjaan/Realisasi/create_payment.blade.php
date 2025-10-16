@extends('Dashboard.base')

@section('title', 'Input Payment Request')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/payment.css') }}">
@endpush

@section('content')
<div class="page-inner">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Input Payment Request
            </h2>
            <h5 class="fw-normal text-muted">Pilih termin yang akan dibayar dan lengkapi dokumen pendukung.</h5>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm d-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    @if($termins->isEmpty())
    <div class="alert alert-warning border-0 shadow-sm">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-3x me-4 text-warning"></i>
            <div>
                <h5 class="mb-1 fw-bold">Tidak Ada Termin Tersedia</h5>
                <p class="mb-0">Semua termin sudah lunas atau belum ada termin yang dibuat untuk Purchase Order ini.</p>
            </div>
        </div>
    </div>
    @else

    {{-- FORM UTAMA --}}
    <form action="{{ route('realisasi.storePayment', $pr->id) }}" method="POST" enctype="multipart/form-data"
        id="paymentForm" novalidate>
        @csrf
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
                            <span class="badge bg-primary-subtle text-primary-emphasis" id="selectedCount">0 Termin
                                Dipilih</span>
                        </div>
                        <div class="alert alert-info border-0 mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Tips:</strong> Anda dapat memilih lebih dari satu termin untuk digabungkan dalam
                            satu payment request.
                        </div>

                        <div class="termin-selection-container">
                            @foreach($termins as $termin)
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
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary-emphasis ms-2">{{ $termin->persentase }}%</span>
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
                                    value="{{ old('tanggal_payment', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_payment" class="form-label fw-semibold">Nomor Payment Request <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nomor_payment" id="nomor_payment"
                                    class="form-control form-control-lg" value="{{ old('nomor_payment') }}"
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
                            function renderFileUpload($id, $name, $label, $icon) {
                            @endphp
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold"><i
                                        class="fas {{ $icon }} me-2 text-primary"></i>{{ $label }}</label>
                                <div class="custom-file-container">
                                    <label for="{{ $id }}" class="custom-file-upload-label">
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <p class="upload-text">Pilih file PDF</p>
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

                            {{ renderFileUpload('invoice', 'invoice', 'Invoice', 'fa-file-invoice') }}
                            {{ renderFileUpload('receipt', 'receipt', 'Kwitansi', 'fa-receipt') }}
                            {{ renderFileUpload('nodin_payment', 'nodin_payment', 'Nodin Permohonan Pembayaran', 'fa-file-signature') }}
                            {{ renderFileUpload('bill', 'bill', 'Surat Tagihan', 'fa-file-contract') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="card card-round shadow-sm border-0 mb-4">
                        <div class="payment-summary-header">
                            <h6 class="text-white-50 mb-1">TOTAL NILAI PAYMENT</h6>
                            <h2 class="fw-bolder mb-0" id="grandTotalPayment">Rp 0</h2>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-list-check me-2"></i>Rincian
                                Termin</h6>
                            <div id="selectedTerminsList">
                                <div class="text-center text-muted py-4" id="emptyState">
                                    <i class="fas fa-hand-pointer fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0 small">Belum ada termin yang dipilih.</p>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-save me-2"></i> Buat Payment Request
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
                            $totalPaid = \App\Models\Termin::where('po_id',
                            $pr->id)->whereNotNull('payment_id')->sum('nilai_pembayaran');
                            $totalUnpaid = \App\Models\Termin::where('po_id',
                            $pr->id)->whereNull('payment_id')->sum('nilai_pembayaran');
                            @endphp
                            <div class="info-item">
                                <small>Total Nilai GR</small>
                                <strong class="text-primary">Rp {{ number_format($gr->nilai_gr, 0, ',', '.') }}</strong>
                            </div>
                            <div class="info-item">
                                <small>Sudah Dibayar</small>
                                <strong class="text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</strong>
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
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/payment.js') }}"></script>
@endpush