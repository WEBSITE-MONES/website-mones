@extends('Dashboard.base')

@section('title', 'Input GR')

@section('content')
<div class="page-inner">
    <div class="card card-round">
        <div class="card-header">
            <h4 class="card-title">Input Realisasi Investasi GR</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('realisasi.storeGR', $pr->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_gr" class="form-label">Tanggal GR</label>
                        <input type="date" name="tanggal_gr" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="nomor_gr" class="form-label">Nomor GR</label>
                        <input type="text" name="nomor_gr" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="nilai_gr" class="form-label">Nilai Pekerjaan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nilai_gr" id="nilai_gr" class="form-control" value="0" readonly>
                        </div>
                    </div>
                </div>

                {{-- Attachment (tampilan saja) --}}
                <h5 class="mt-4">Attachment</h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">BA Pemeriksaan Pekerjaan Selesai</label>
                    <input type="file" name="ba_pemeriksaan" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">BA Serah Terima Pekerjaan</label>
                    <input type="file" name="ba_serah_terima" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">BA Pembayaran</label>
                    <input type="file" name="ba_pembayaran" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Laporan / Dokumentasi</label>
                    <input type="file" name="laporan_dokumentasi" class="form-control">
                </div>

                {{-- Biaya Langsung GR --}}
                <h5 class="mt-4">Biaya Langsung GR</h5>
                <table class="table table-bordered" id="biayaTable">
                    <thead class="table-light">
                        <tr>
                            <th>Pos Anggaran</th>
                            <th>Uraian</th>
                            <th style="width:220px">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- gunakan data dari $po kalau ada, kalau tidak pakai default sementara --}}
                        @php
                        $defaultPos = $po->pos_anggaran ?? '-';
                        $defaultUraian = $po->uraian ?? '-';
                        @endphp

                        <tr>
                            <td>
                                {{-- nanti diganti: ambil dari DB, sekarang readonly demo --}}
                                <input type="text" class="form-control" readonly value="{{ $defaultPos }}">
                                <input type="hidden" name="biaya[0][pos_anggaran]" value="{{ $defaultPos }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" readonly value="{{ $defaultUraian }}">
                                <input type="hidden" name="biaya[0][uraian]" value="{{ $defaultUraian }}">
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="biaya[0][nilai]" class="form-control nilai-input"
                                        value="0" min="0" step="1">
                                    <span class="input-group-text">Rp</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-end">Grand Total</th>
                            <th><span id="grandTotal">Rp 0</span></th>
                        </tr>
                    </tfoot>
                </table>

                <div class="card-footer text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-danger">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan GR</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * Update grand total + isi field numeric nilai_gr.
 * event delegation digunakan agar handler tetap bekerja jika DOM berubah.
 */
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(angka);
}

function parseNumber(value) {
    if (value === null || value === undefined) return 0;
    // hapus non-digit (titik/koma/spasi)
    return Number(String(value).replace(/[^\d\-]/g, '')) || 0;
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll(".nilai-input").forEach(input => {
        total += parseNumber(input.value);
    });

    // tampilkan formatted
    const grandEl = document.getElementById("grandTotal");
    if (grandEl) grandEl.textContent = formatRupiah(total);

    // isi input numeric (untuk disubmit)
    const nilaiInput = document.getElementById("nilai_gr");
    if (nilaiInput) nilaiInput.value = total;
}

// Event delegation: dengarkan semua perubahan input di dokumen
document.addEventListener("input", function(e) {
    if (e.target && e.target.matches && e.target.matches(".nilai-input")) {
        updateGrandTotal();
    }
});

// Onload initial compute
document.addEventListener("DOMContentLoaded", function() {
    updateGrandTotal();
});
</script>
@endpush