@extends('Dashboard.base')

@section('title', 'Input Payment Request')

@section('content')
<div class="page-inner">
    <div class="card card-round">
        <div class="card-header">
            <h4 class="card-title">Input Payment Request</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('realisasi.storePayment', $pr->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_payment" class="form-label">Tanggal Payment Request</label>
                        <input type="date" name="tanggal_payment" class="form-control" required
                            value="{{ old('tanggal_payment', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="nomor_payment" class="form-label">Nomor Payment Request</label>
                        <input type="text" name="nomor_payment" class="form-control" required
                            value="{{ old('nomor_payment') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="nilai_payment" class="form-label">Nilai Payment Request</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nilai_payment" id="nilai_payment" class="form-control"
                                value="{{ old('nilai_payment', $gr->nilai_gr ?? 0) }}" readonly>
                        </div>
                    </div>
                </div>

                {{-- Attachment --}}
                <h5 class="mt-4">Attachment</h5>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Kwitansi</th>
                            <th>Nodin Permohonan Pembayaran</th>
                            <th>Surat Tagihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="file" name="invoice" class="form-control" accept=".pdf"></td>
                            <td><input type="file" name="receipt" class="form-control" accept=".pdf"></td>
                            <td><input type="file" name="nodin_payment" class="form-control" accept=".pdf"></td>
                            <td><input type="file" name="bill" class="form-control" accept=".pdf"></td>
                        </tr>
                    </tbody>
                </table>

                {{-- Item Payment mirip Biaya GR --}}
                <h5 class="mt-4">Detail Payment</h5>
                <table class="table table-bordered" id="paymentTable">
                    <thead class="table-light">
                        <tr>
                            <th>Deskripsi</th>
                            <th style="width:220px">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="payment[0][deskripsi]" class="form-control"
                                    value="Pembayaran GR" readonly>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="payment[0][nilai]" class="form-control nilai-input"
                                        value="{{ $gr->nilai_gr ?? 0 }}" min="0" step="1">
                                    <span class="input-group-text">Rp</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-end">Grand Total</th>
                            <th><span id="grandTotalPayment">Rp {{ number_format($gr->nilai_gr ?? 0,0,',','.') }}</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-end mt-4">
                    <a href="{{ route('realisasi.index') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-success">Simpan Payment</button>
                </div>
            </form>
        </div>
    </div>
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
    return Number(String(value).replace(/[^\d\-]/g, '')) || 0;
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
@endpush