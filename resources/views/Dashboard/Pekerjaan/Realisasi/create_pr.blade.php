@extends('Dashboard.base')

@section('title', 'Form Input PR')

@section('content')
<div class="page-inner">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
        <div>
            <h2 class="page-title">Formulir Input Perencanaan (PR)</h2>
            <h5 class="fw-normal text-muted">Lengkapi detail pekerjaan dan anggaran perencanaan.</h5>
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

    <form action="{{ route('realisasi.storePR') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Kolom Kiri: Detail Form --}}
            <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        {{-- Detail Pekerjaan --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-tasks me-2 text-primary"></i>Detail Pekerjaan
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan <span
                                        class="text-danger">*</span></label>
                                <select name="jenis_pekerjaan" id="jenis_pekerjaan"
                                    class="form-select @error('jenis_pekerjaan') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Jenis Pekerjaan --</option>
                                    <option value="Konsultan Perencana"
                                        {{ old('jenis_pekerjaan') == 'Konsultan Perencana' ? 'selected' : '' }}>
                                        Konsultan Perencana
                                    </option>
                                    <option value="Pelaksanaan Fisik"
                                        {{ old('jenis_pekerjaan') == 'Pelaksanaan Fisik' ? 'selected' : '' }}>
                                        Pelaksanaan Fisik
                                    </option>
                                    <option value="Konsultan Pengawas"
                                        {{ old('jenis_pekerjaan') == 'Konsultan Pengawas' ? 'selected' : '' }}>
                                        Konsultan Pengawas
                                    </option>
                                </select>
                                @error('jenis_pekerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="pekerjaan_id" class="form-label">Prodef SAP <span
                                        class="text-danger">*</span></label>
                                <select name="pekerjaan_id" id="pekerjaan_id"
                                    class="form-select @error('pekerjaan_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Prodef SAP --</option>
                                    @foreach($pekerjaans as $p)
                                    <option value="{{ $p->id }}" data-nama="{{ $p->nama_investasi }}"
                                        {{ old('pekerjaan_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nomor_prodef_sap }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('pekerjaan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="nama_investasi_display" class="form-label">Definisi Proyek</label>
                                <input type="text" id="nama_investasi_display" class="form-control bg-light" readonly>
                            </div>

                            <div class="col-md-12">
                                <label for="nama_investasi" class="form-label">Nama Investasi <span
                                        class="text-danger">*</span></label>
                                <textarea name="sub_pekerjaan" id="nama_investasi"
                                    class="form-control @error('sub_pekerjaan') is-invalid @enderror" rows="3"
                                    placeholder="Masukkan nama atau deskripsi singkat investasi..."
                                    required>{{ old('sub_pekerjaan') }}</textarea>
                                @error('sub_pekerjaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Data PR --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-barcode me-2 text-primary"></i>Data PR
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomor_pr" class="form-label">Nomor PR <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nomor_pr" id="nomor_pr"
                                    class="form-control @error('nomor_pr') is-invalid @enderror"
                                    placeholder="Cth: 4000123456" value="{{ old('nomor_pr') }}" required>
                                @error('nomor_pr')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_pr" class="form-label">Tanggal PR <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pr" id="tanggal_pr"
                                    class="form-control @error('tanggal_pr') is-invalid @enderror"
                                    value="{{ old('tanggal_pr', date('Y-m-d')) }}" required>
                                @error('tanggal_pr')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="tahun_anggaran" class="form-label">Tahun Anggaran <span
                                        class="text-danger">*</span></label>
                                <select name="tahun_anggaran" id="tahun_anggaran"
                                    class="form-select @error('tahun_anggaran') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Tahun --</option>
                                    @php
                                    $currentYear = date('Y');
                                    $startYear = $currentYear - 5;
                                    $endYear = $currentYear + 5;
                                    for ($i = $endYear; $i >= $startYear; $i--) {
                                    $selected = ($i == old('tahun_anggaran', $currentYear)) ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i</option>";
                                    }
                                    @endphp
                                </select>
                                @error('tahun_anggaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan & Aksi --}}
            <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 sticky-lg-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3 border-bottom pb-2">
                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Ringkasan Anggaran
                        </h5>
                        <div class="mb-4">
                            <label for="nilai_pr_display" class="form-label fw-bold">Nilai PR <span
                                    class="text-danger">*</span></label>
                            <div class="input-group @error('nilai_pr') has-validation @enderror">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" id="nilai_pr_display"
                                    class="form-control form-control-lg text-end fs-5 fw-bold" placeholder="0"
                                    value="{{ old('nilai_pr_display') }}" required>
                                <input type="hidden" name="nilai_pr" id="nilai_pr" value="{{ old('nilai_pr') }}">
                                @error('nilai_pr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="p-3 bg-body-tertiary border rounded-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center text-success-emphasis">
                                <h6 class="fw-bold mb-0">Total Nilai PR:</h6>
                                <h5 class="fw-bolder mb-0" id="total-pr">Rp 0</h5>
                                <input type="hidden" name="total_pr" id="total-pr-hidden"
                                    value="{{ old('total_pr', 0) }}">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Simpan PR
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
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
<script>
$(document).ready(function() {
    // Tampilkan definisi proyek sesuai pilihan Prodef SAP
    $('#pekerjaan_id').on('change', function() {
        let selectedNama = $(this).find(':selected').data('nama') || '';
        $('#nama_investasi_display').val(selectedNama);
    });

    if ($('#pekerjaan_id').val()) {
        $('#pekerjaan_id').trigger('change');
    }

    // Format Rupiah
    const displayInput = $('#nilai_pr_display');
    const hiddenInput = $('#nilai_pr');

    const formatRupiah = (angka) => {
        if (!angka || isNaN(angka)) return '';
        return new Intl.NumberFormat('id-ID', {
            style: 'decimal',
            minimumFractionDigits: 0
        }).format(angka);
    };

    function hitungTotal() {
        let nilai = parseInt(hiddenInput.val()) || 0;
        $('#total-pr').text('Rp ' + formatRupiah(nilai));
        $('#total-pr-hidden').val(nilai);
    }

    displayInput.on('input', function() {
        let value = this.value.replace(/\D/g, '');
        hiddenInput.val(value);
        this.value = value ? formatRupiah(value) : '';
        hitungTotal();
    });

    if (hiddenInput.val()) {
        displayInput.val(formatRupiah(hiddenInput.val()));
        hitungTotal();
    }
});
</script>
@endpush

@endsection