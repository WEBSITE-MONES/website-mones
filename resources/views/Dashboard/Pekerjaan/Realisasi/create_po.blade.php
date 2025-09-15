@extends('Dashboard.base')

@section('title', 'Form Input PO')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Form Input PO</h4>
    </div>

    {{-- Validasi Error --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card card-round shadow-sm">
        <div class="card-header text-white text-center rounded-top">
            <h3 class="card-title mb-0">FORM INPUT PO</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('realisasi.storePO', $pr->id) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal PO/Kontrak <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_po" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor PO <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_po" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">No. Kontrak/SPPP/SPK</label>
                        <input type="text" name="nomor_kontrak" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nilai PO <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nilai_po" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estimated (Periode)</label>
                        <div class="input-group">
                            <input type="date" name="estimated_start" class="form-control" placeholder="Dari tanggal">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="estimated_end" class="form-control" placeholder="Sampai tanggal">
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Waktu Pelaksanaan</label>
                        <div class="input-group">
                            <input type="number" name="waktu_pelaksanaan" class="form-control" min="1"
                                placeholder="Lama hari">
                            <span class="input-group-text">Hari</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pelaksana</label>
                        <input type="text" name="pelaksana" class="form-control"
                            placeholder="Contoh: PT. Intan Sejahtera">
                    </div>


                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mekanisme Pembayaran</label>
                        <select name="mekanisme_pembayaran" class="form-select">
                            <option value="Uang muka">Uang muka</option>
                            <option value="Termin">Termin</option>
                        </select>
                    </div>
                </div>

                {{-- Total Dana (opsional) --}}
                <div class="row mb-3">
                    <div class="col-md-12 text-end">
                        <strong>Total PO: </strong>
                        <span id="total-po">Rp 0</span>
                        <input type="hidden" name="total_po" id="total-po-hidden" value="0">
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('realisasi.index') }}" class="btn btn-secondary me-2">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan PO
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Hitung total P0 otomatis (jika perlu)
    function hitungTotal() {
        let nilai = parseFloat($('input[name="nilai_po"]').val()) || 0;
        $('#total-po').text('Rp ' + nilai.toLocaleString('id-ID'));
        $('#total-po-hidden').val(nilai);
    }
    $('input[name="nilai_po"]').on('input', hitungTotal);
    hitungTotal();
});
</script>
@endpush
@endsection