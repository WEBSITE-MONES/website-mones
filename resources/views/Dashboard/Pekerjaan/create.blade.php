@extends('Dashboard.base')

@section('title', 'Tambah Rencana Kerja')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Tambah Rencana Kerja</h4>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-header text-center">
            <h3 class="card-title">Form Tambah Rencana Kerja</h3>
        </div>

        <form action="{{ route('pekerjaan.store') }}" method="POST">
            @csrf
            <div class="card-body">

                <div class="row">
                    {{-- Kode --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Entitas/Terminal<span class="text-danger">*</span></label>
                            <select name="wilayah_id" class="form-control" required>
                                <option value="">-- Pilih Wilayah --</option>
                                @foreach ($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id }}">{{ $wilayah->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Nama Pekerjaan --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pekerjaan" class="form-control"
                                placeholder="Masukkan nama pekerjaan" required>
                        </div>
                    </div>

                    {{-- Status Pekerjaan --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Pending">Pending</option>
                                <option value="On Progress">On Progress</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Nilai --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nilai Pekerjaan <span class="text-danger">*</span></label>
                            <input type="number" name="nilai" class="form-control nilai" value="0"
                                placeholder="Masukkan nilai pekerjaan" required>
                        </div>
                    </div>

                    {{-- Kebutuhan Dana --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kebutuhan Dana <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" name="kebutuhan_dana" class="form-control dana" value="0"
                                    step="1000" placeholder="Masukkan kebutuhan dana" required>
                            </div>
                        </div>
                    </div>

                    {{-- Tahun --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tahun <span class="text-danger">*</span></label>
                            <input type="text" name="tahun" class="form-control yearpicker" value="{{ date('Y') }}"
                                placeholder="2025" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Tanggal --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Waktu Pekerjaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                    </div>
                </div>

                {{-- Total Biaya --}}
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="alert alert-secondary text-right">
                            <strong>Total Kebutuhan Dana: </strong> <span id="total-dana">Rp 0</span>
                        </div>
                        {{-- Hidden input untuk mengirim total ke server --}}
                        <input type="hidden" name="total_dana" id="total-dana-hidden" value="0">
                    </div>
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-undo mr-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-check mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
</script>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" />

<script>
$(document).ready(function() {
    // ===== Yearpicker =====
    $('.yearpicker').datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true
    });

    // ===== Hitung Total Dana =====
    function hitungTotal() {
        // Karena hanya satu input dana
        let total = parseFloat($('.dana').val()) || 0;
        $('#total-dana').text('Rp ' + total.toLocaleString('id-ID'));
        $('#total-dana-hidden').val(total);
    }

    // Panggil saat input berubah
    $('.dana').on('input', hitungTotal);

    // Jalankan sekali saat load
    hitungTotal();
});
</script>
@endpush

@endsection