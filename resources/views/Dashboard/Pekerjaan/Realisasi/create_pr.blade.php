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

    <div class="card shadow-sm border-0">
        <div class="card-header text-center text-white">
            <h3 class="card-title mb-0">FORM INPUT PR</h3>
        </div>

        <form action="{{ route('realisasi.storePR') }}" method="POST">
            @csrf
            <div class="card-body">

                {{-- Jenis Pekerjaan & Nama Pekerjaan --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                        <select name="jenis_pekerjaan" class="form-select" required>
                            <option value="">-- Pilih Jenis Pekerjaan --</option>
                            <option value="Konsultan Perencana">Konsultan Perencana</option>
                            <option value="Pelaksanaan Fisik">Pelaksanaan Fisik</option>
                            <option value="Konsultan Pengawas">Konsultan Pengawas</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Pekerjaan <span class="text-danger">*</span></label>
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
                        <label class="form-label">Nilai PR <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="nilai_pr_display" class="form-control"
                                placeholder="Misal: 10.000.000" required>
                            <input type="hidden" name="nilai_pr" id="nilai_pr">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor PR <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_pr" class="form-control" required>
                    </div>
                </div>

                {{-- Tanggal PR & Tahun Anggaran --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal PR <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pr" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                        <select name="tahun_anggaran" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            @php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 20;
                            $endYear = $currentYear + 15;
                            for ($i = $startYear; $i <= $endYear; $i++) { $selected=($i==$currentYear) ? 'selected' : ''
                                ; echo "<option value='$i' $selected>$i</option>" ; } @endphp </select>
                    </div>
                </div>

                {{-- Total Dana --}}
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
                <button type="submit" class="btn btn-primary">Simpan PR</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const displayInput = $('#nilai_pr_display');
    const hiddenInput = $('#nilai_pr');

    displayInput.on('input', function() {
        let value = this.value.replace(/\D/g, '');
        this.value = new Intl.NumberFormat('id-ID').format(value);
        hiddenInput.val(value);
        hitungTotal();
    });

    function hitungTotal() {
        let nilai = parseInt(hiddenInput.val()) || 0;
        $('#total-pr').text('Rp ' + new Intl.NumberFormat('id-ID').format(nilai));
        $('#total-pr-hidden').val(nilai);
    }

    hitungTotal();
});
</script>
@endpush
@endsection