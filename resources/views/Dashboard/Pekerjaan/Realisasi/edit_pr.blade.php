@extends('Dashboard.base')

@section('title', 'Form Edit PR')

@section('content')
<div class="page-inner">
    {{-- Header Halaman (disamakan dengan create_pr) --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Formulir Edit Perencanaan (PR)</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Realisasi Berjalan</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Edit Progress</a>
                </li>
            </ul>
        </div>


        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- Validasi Error --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i> Gagal!</strong> Terdapat kesalahan pada input Anda.
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('realisasi.updatePR', $pr->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- KOLOM KIRI: INPUT UTAMA --}}
            <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        {{-- Group: Detail Pekerjaan --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-tasks me-2 text-primary"></i>Detail Pekerjaan
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_pekerjaan" id="jenis_pekerjaan" class="form-select" required>
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
                                <label for="pekerjaan_id" class="form-label">Nama Pekerjaan <span
                                        class="text-danger">*</span></label>
                                <select name="pekerjaan_id" id="pekerjaan_id" class="form-select" required>
                                    <option value="">-- Pilih Pekerjaan --</option>
                                    @foreach($pekerjaans as $p)
                                    <option value="{{ $p->id }}" {{ $p->id == $pr->pekerjaan_id ? 'selected' : '' }}>
                                        {{ $p->nama_investasi }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Group: Data PR & Anggaran --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-barcode me-2 text-primary"></i>Data PR
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomor_pr" class="form-label">Nomor PR <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nomor_pr" id="nomor_pr" class="form-control"
                                    value="{{ old('nomor_pr', $pr->nomor_pr) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_pr" class="form-label">Tanggal PR <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pr" id="tanggal_pr" class="form-control"
                                    value="{{ old('tanggal_pr', $pr->tanggal_pr->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-12">
                                <label for="tahun_anggaran" class="form-label">Tahun Anggaran</label>
                                <input type="text" class="form-control" value="{{ $pr->tahun_anggaran }}" readonly
                                    disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: RINGKASAN & AKSI --}}
            <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 sticky-lg-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3 border-bottom pb-2">
                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Ringkasan Anggaran
                        </h5>
                        <div class="mb-4">
                            <label for="nilai_pr_display" class="form-label fw-bold">Nilai PR <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" id="nilai_pr_display"
                                    class="form-control form-control-lg text-end fs-5 fw-bold"
                                    value="{{ number_format($pr->nilai_pr, 0, ',', '.') }}" required>
                                <input type="hidden" name="nilai_pr" id="nilai_pr" value="{{ $pr->nilai_pr }}">
                            </div>
                        </div>

                        <div class="p-3 bg-light border rounded-3 mb-4">
                            <div class="d-flex justify-content-between text-success">
                                <h6 class="fw-bold mb-0">Total Nilai PR:</h6>
                                <h6 class="fw-bold mb-0" id="total-pr">Rp
                                    {{ number_format($pr->nilai_pr, 0, ',', '.') }}</h6>
                                <input type="hidden" name="total_pr" id="total-pr-hidden" value="{{ $pr->nilai_pr }}">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sync-alt me-2"></i> Update PR
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
{{-- LOGIKA JAVASCRIPT TETAP SAMA --}}
<script>
$(function() {
    const displayInput = $('#nilai_pr_display');
    const hiddenInput = $('#nilai_pr');

    displayInput.on('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            this.value = new Intl.NumberFormat('id-ID').format(value);
        }
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