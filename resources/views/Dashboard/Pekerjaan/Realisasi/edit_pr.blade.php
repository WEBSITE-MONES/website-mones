@extends('Dashboard.base')

@section('title', 'Form Edit PR')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Form Edit PR</h4>
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

    <div class="card">
        <div class="card-header text-center text-white">
            <h3 class="card-title mb-0">EDIT PR</h3>
        </div>

        <form action="{{ route('realisasi.updatePR', $pr->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                {{-- Jenis Pekerjaan & Nama Pekerjaan --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Jenis Pekerjaan <span class="text-danger">*</span></label>
                        <select name="jenis_pekerjaan[]" class="form-select" multiple required>
                            <option value="Konsultan Perencana" @if(in_array('Konsultan Perencana', $pr->
                                jenis_pekerjaan)) selected @endif>Konsultan Perencana</option>
                            <option value="Pelaksanaan Fisik" @if(in_array('Pelaksanaan Fisik', $pr->jenis_pekerjaan))
                                selected @endif>Pelaksanaan Fisik</option>
                            <option value="Konsultan Pengawas" @if(in_array('Konsultan Pengawas', $pr->jenis_pekerjaan))
                                selected @endif>Konsultan Pengawas</option>
                        </select>
                        <small class="text-muted">Tekan Ctrl (Windows) / Cmd (Mac) untuk pilih lebih dari satu</small>
                    </div>
                    <div class="col-md-6">
                        <label>Nama Pekerjaan <span class="text-danger">*</span></label>
                        <select name="pekerjaan_id" class="form-select" required>
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach($pekerjaans as $p)
                            <option value="{{ $p->id }}" @if($p->id == $pr->pekerjaan_id) selected
                                @endif>{{ $p->nama_investasi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nilai PR & Nomor PR --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Nilai PR <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="nilai_pr" class="form-control" value="{{ $pr->nilai_pr }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Nomor PR <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_pr" class="form-control" value="{{ $pr->nomor_pr }}" required>
                    </div>
                </div>

                {{-- Tanggal PR & Tahun Anggaran --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Tanggal PR <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pr" class="form-control"
                            value="{{ $pr->tanggal_pr->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>Tahun Anggaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $pr->tanggal_pr->format('Y') }}" readonly>
                    </div>
                </div>

                {{-- Total Dana --}}
                <div class="row mb-3">
                    <div class="col-md-12 text-end">
                        <strong>Total PR: </strong>
                        <span id="total-pr">Rp {{ number_format($pr->nilai_pr, 0, ',', '.') }}</span>
                        <input type="hidden" name="total_pr" id="total-pr-hidden" value="{{ $pr->nilai_pr }}">
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer text-end">
                <a href="{{ route('realisasi.index') }}" class="btn btn-danger">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    function hitungTotal() {
        let nilai = parseFloat($('input[name="nilai_pr"]').val()) || 0;
        $('#total-pr').text('Rp ' + nilai.toLocaleString('id-ID'));
        $('#total-pr-hidden').val(nilai);
    }
    $('input[name="nilai_pr"]').on('input', hitungTotal);
    hitungTotal();
});
</script>
@endpush

@endsection