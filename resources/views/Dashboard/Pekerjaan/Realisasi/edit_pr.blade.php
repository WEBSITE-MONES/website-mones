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

    <div class="card shadow-sm border-0">
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
                        <label class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                        <select name="jenis_pekerjaan" class="form-select" required>
                            <option value="Konsultan Perencana"
                                {{ $pr->jenis_pekerjaan == 'Konsultan Perencana' ? 'selected' : '' }}>
                                Konsultan Perencana
                            </option>
                            <option value="Pelaksanaan Fisik"
                                {{ $pr->jenis_pekerjaan == 'Pelaksanaan Fisik' ? 'selected' : '' }}>
                                Pelaksanaan Fisik
                            </option>
                            <option value="Konsultan Pengawas"
                                {{ $pr->jenis_pekerjaan == 'Konsultan Pengawas' ? 'selected' : '' }}>
                                Konsultan Pengawas
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Pekerjaan <span class="text-danger">*</span></label>
                        <select name="pekerjaan_id" class="form-select" required>
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach($pekerjaans as $p)
                            <option value="{{ $p->id }}" {{ $p->id == $pr->pekerjaan_id ? 'selected' : '' }}>
                                {{ $p->nama_investasi }}</option>
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
                                value="{{ number_format($pr->nilai_pr, 0, ',', '.') }}" required>
                            <input type="hidden" name="nilai_pr" id="nilai_pr" value="{{ $pr->nilai_pr }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor PR <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_pr" class="form-control" value="{{ $pr->nomor_pr }}" required>
                    </div>
                </div>

                {{-- Tanggal PR & Tahun Anggaran --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal PR <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pr" class="form-control"
                            value="{{ $pr->tanggal_pr->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $pr->tahun_anggaran }}" readonly>
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
                <a href="{{ url()->previous() }}" class="btn btn-danger">Batal</a>
                <button type="submit" class="btn btn-primary text-white">Update PR</button>
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