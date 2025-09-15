@extends('Dashboard.base')

@section('title', 'Form Input PR')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Form Input PR</h4>
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
            <h3 class="card-title mb-0">FORM PR</h3>
        </div>

        <form action="{{ route('realisasi.storePR') }}" method="POST">
            @csrf
            <div class="card-body">
                {{-- Jenis Pekerjaan & Nama Pekerjaan --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Jenis Pekerjaan <span class="text-danger">*</span></label>
                        <select name="jenis_pekerjaan[]" class="form-select" multiple required>
                            <option value="Konsultan Perencana">Konsultan Perencana</option>
                            <option value="Pelaksanaan Fisik">Pelaksanaan Fisik</option>
                            <option value="Konsultan Pengawas">Konsultan Pengawas</option>
                        </select>
                        <small class="text-muted">Tekan Ctrl (Windows) / Cmd (Mac) untuk pilih lebih dari satu</small>
                    </div>
                    <div class="col-md-6">
                        <label>Nama Pekerjaan <span class="text-danger">*</span></label>
                        <select name="pekerjaan_id" class="form-select" required>
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach($pekerjaans as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_investasi }}</option>
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
                            <input type="number" name="nilai_pr" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>Nomor PR <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_pr" class="form-control" required>
                    </div>
                </div>

                {{-- Tanggal PR & Tahun Anggaran --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Tanggal PR <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pr" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>Tahun Anggaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ date('Y') }}" readonly>
                    </div>
                </div>

                {{-- Total Dana (opsional) --}}
                <div class="row mb-3">
                    <div class="col-md-12 text-end">
                        <strong>Total PR: </strong>
                        <span id="total-pr">Rp 0</span>
                        <input type="hidden" name="total_pr" id="total-pr-hidden" value="0">
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer text-end">
                <a href="{{ url()->previous() }}" class="btn btn-danger">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Hitung total PR otomatis (jika perlu)
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