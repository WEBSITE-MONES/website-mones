@extends('Dashboard.base')

@section('title', 'Edit Rencana Kerja')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Rencana Kerja</h4>
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
            <h3 class="card-title">Form Edit Rencana Kerja</h3>
        </div>

        <form action="{{ route('pekerjaan.update', $pekerjaan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">

                <div class="row">
                    {{-- Wilayah --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Entitas/Terminal <span class="text-danger">*</span></label>
                            <select name="wilayah_id" class="form-control" required>
                                <option value="">-- Pilih Wilayah --</option>
                                @foreach ($wilayahs as $wilayah)
                                <option value="{{ $wilayah->id }}"
                                    {{ ($pekerjaan->wilayah_id == $wilayah->id) ? 'selected' : '' }}>
                                    {{ $wilayah->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Nama Pekerjaan --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pekerjaan" class="form-control"
                                value="{{ old('nama_pekerjaan', $pekerjaan->nama_pekerjaan) }}" required>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="Pending" {{ $pekerjaan->status == 'Pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="On Progress" {{ $pekerjaan->status == 'On Progress' ? 'selected' : '' }}>
                                    On Progress</option>
                                <option value="Selesai" {{ $pekerjaan->status == 'Selesai' ? 'selected' : '' }}>Selesai
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Nilai --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nilai Pekerjaan <span class="text-danger">*</span></label>
                            <input type="number" name="nilai" class="form-control nilai"
                                value="{{ old('nilai', $pekerjaan->nilai) }}" required>
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
                                <input type="number" name="kebutuhan_dana" class="form-control dana"
                                    value="{{ old('kebutuhan_dana', $pekerjaan->kebutuhan_dana) }}" step="1000"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- Tahun --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tahun <span class="text-danger">*</span></label>
                            <input type="text" name="tahun" class="form-control yearpicker"
                                value="{{ old('tahun', $pekerjaan->tahun) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Tanggal --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Waktu Pekerjaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', $pekerjaan->tanggal) }}" required>
                        </div>
                    </div>
                </div>

                {{-- Total Dana --}}
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="alert alert-secondary text-right">
                            <strong>Total Kebutuhan Dana: </strong> <span id="total-dana">Rp
                                {{ number_format($pekerjaan->kebutuhan_dana,0,',','.') }}</span>
                        </div>
                        <input type="hidden" name="total_dana" id="total-dana-hidden"
                            value="{{ $pekerjaan->kebutuhan_dana }}">
                    </div>
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('dashboard.kota', $pekerjaan->wilayah_id) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-undo mr-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-check mr-1"></i> Update
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
    $('.yearpicker').datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true
    });

    function hitungTotal() {
        let total = parseFloat($('.dana').val()) || 0;
        $('#total-dana').text('Rp ' + total.toLocaleString('id-ID'));
        $('#total-dana-hidden').val(total);
    }

    $('.dana').on('input', hitungTotal);
    hitungTotal();
});
</script>
@endpush

@endsection