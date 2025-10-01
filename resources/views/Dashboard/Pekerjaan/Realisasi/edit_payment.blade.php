@extends('Dashboard.base')

@section('title', 'Edit Payment Request')

@push('styles')
<style>
.custom-file-upload {
    border: 2px dashed #0d6efd;
    border-radius: .5rem;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    background-color: #f8f9fa;
    transition: all .3s ease;
}

.custom-file-upload:hover {
    background-color: #e9ecef;
    border-color: #0b5ed7;
}

.custom-file-upload .icon {
    font-size: 2.5rem;
    color: #0d6efd;
    margin-bottom: .5rem;
}

.custom-file-upload .file-text {
    display: block;
    font-weight: 600;
    color: #495057;
}

.custom-file-upload .file-name {
    display: block;
    font-size: .875rem;
    color: #0b5ed7;
    font-weight: 500;
    margin-top: .5rem;
}
</style>
@endpush

@section('content')
<div class="page-inner">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">Edit Payment Request</h2>
            <h5 class="fw-normal text-muted">Perbarui detail pembayaran dan lampirkan dokumen bila ada perubahan.</h5>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm d-none d-md-block">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('realisasi.updatePayment', ['pr' => $pr->id, 'payment' => $payment->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                {{-- KARTU 1: INFORMASI UTAMA & LAMPIRAN --}}
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>Informasi Utama
                        </h5>
                        {{-- Tanggal & Nomor --}}
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_payment" class="form-label">Tanggal Payment Request</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="tanggal_payment" class="form-control"
                                        value="{{ old('tanggal_payment', $payment->tanggal_payment) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_payment" class="form-label">Nomor Payment Request</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" name="nomor_payment" class="form-control"
                                        value="{{ old('nomor_payment', $payment->nomor_payment) }}" required>
                                </div>
                            </div>

                            {{-- GR --}}
                            <div class="col-md-12 mb-3">
                                <label for="gr_id" class="form-label">Pilih GR</label>
                                <select name="gr_id" id="gr_id" class="form-select" required>
                                    <option value="{{ $gr->id }}" selected>
                                        Rp {{ number_format($gr->nilai_gr, 0, ',', '.') }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Total Nilai Payment</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="payment[0][nilai]" class="form-control nilai-input"
                                        value="{{ $gr->nilai_gr }}" readonly>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-paperclip text-primary me-2"></i>Lampiran Dokumen (.pdf)
                        </h5>

                        <div class="row">
                            @foreach ([
                            'invoice' => 'Invoice',
                            'receipt' => 'Kwitansi',
                            'nodin_payment' => 'Nodin Permohonan Pembayaran',
                            'bill' => 'Surat Tagihan'
                            ] as $field => $label)
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ $label }}</label>
                                @if($payment->$field)
                                <p>
                                    <a href="{{ asset('storage/' . $payment->$field) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        Lihat {{ $label }}
                                    </a>
                                </p>
                                @endif
                                <div class="custom-file-upload">
                                    <input type="file" id="{{ $field }}" name="{{ $field }}" class="d-none file-input"
                                        accept=".pdf">
                                    <label for="{{ $field }}" class="w-100">
                                        <i class="fas fa-cloud-upload-alt icon"></i>
                                        <span class="file-text">Pilih file...</span>
                                        <span class="file-name text-success"></span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL PEMBAYARAN --}}
            <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4 sticky-lg-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-dollar-sign text-primary me-2"></i> Detail Pembayaran
                        </h5>

                        <div id="paymentTable">
                            <div class="mb-3">
                                <label class="form-label">Total Nilai Payment</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="payment[0][nilai]" class="form-control nilai-input"
                                        value="{{ $gr->nilai_gr }}" readonly>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-center bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">TOTAL NILAI PAYMENT</h6>
                            <input type="hidden" name="nilai_payment" id="nilai_payment" value="{{ $gr->nilai_gr }}">
                            <h3 class="fw-bold text-success mb-0" id="grandTotalPayment">
                                Rp {{ number_format($gr->nilai_gr, 0, ',', '.') }}
                            </h3>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Update
                                Payment</button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-danger"><i
                                    class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(angka);
}

function parseNumber(value) {
    if (!value) return 0;

    // Kalau sudah angka mentah (1000000), langsung return
    if (/^\d+$/.test(value)) {
        return Number(value);
    }

    // Kalau ada titik/koma → hapus lalu parse
    return Number(String(value).replace(/[^\d]/g, '')) || 0;
}


function updateGrandTotalPayment() {
    let total = 0;
    document.querySelectorAll("#paymentTable .nilai-input").forEach(input => {
        total += parseNumber(input.value);
    });
    const grandEl = document.getElementById("grandTotalPayment");
    if (grandEl) grandEl.textContent = formatRupiah(total);
    const nilaiInput = document.getElementById("nilai_payment");
    if (nilaiInput) nilaiInput.value = total;
}
document.addEventListener("input", function(e) {
    if (e.target && e.target.matches("#paymentTable .nilai-input")) {
        updateGrandTotalPayment();
    }
});
document.addEventListener("DOMContentLoaded", function() {
    updateGrandTotalPayment();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.file-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
            const nextSibling = e.target.nextElementSibling.querySelector('.file-name');
            if (nextSibling) {
                nextSibling.textContent = fileName;
            }
        });
    });
});
</script>
@endpush