@extends('Dashboard.base')

@section('title', 'Form Edit PR')

@section('content')
<div class="page-inner">
    {{-- Header Halaman Ditingkatkan --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-edit me-2 text-primary"></i> Form Edit Purchase Requisition (PR)
        </h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    ---

    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Kartu Utama Form --}}
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-dark p-3 rounded-top-3">
                    <h4 class="card-title mb-0 text-center fw-bolder">
                        <i class="fas fa-file-invoice-dollar me-2"></i> EDIT DATA PR
                    </h4>
                </div>

                <form action="{{ route('realisasi.updatePR', $pr->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4">
                        {{-- Alert Validasi Error (Ditingkatkan) --}}
                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <h5 class="alert-heading fs-6 fw-bold">
                                <i class="fas fa-exclamation-triangle me-2"></i> Kesalahan Input!
                            </h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        {{-- Section: Rincian PR --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-clipboard-list me-1"></i> Detail PR
                            </legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="jenis_pekerjaan" class="form-label fw-semibold">
                                        <i class="fas fa-cogs me-1 text-muted"></i> Jenis Pekerjaan <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="jenis_pekerjaan" id="jenis_pekerjaan"
                                        class="form-select form-select-sm" required>
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
                                    <label for="pekerjaan_id" class="form-label fw-semibold">
                                        <i class="fas fa-tasks me-1 text-muted"></i> Nama Pekerjaan <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="pekerjaan_id" id="pekerjaan_id" class="form-select form-select-sm"
                                        required>
                                        <option value="">-- Pilih Pekerjaan --</option>
                                        @foreach($pekerjaans as $p)
                                        <option value="{{ $p->id }}"
                                            {{ $p->id == $pr->pekerjaan_id ? 'selected' : '' }}>
                                            {{ $p->nama_investasi }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="nomor_pr" class="form-label fw-semibold">
                                        <i class="fas fa-file-alt me-1 text-muted"></i> Nomor PR <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nomor_pr" id="nomor_pr"
                                        class="form-control form-control-sm"
                                        value="{{ old('nomor_pr', $pr->nomor_pr) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="tanggal_pr" class="form-label fw-semibold">
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i> Tanggal PR <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="tanggal_pr" id="tanggal_pr"
                                        class="form-control form-control-sm"
                                        value="{{ old('tanggal_pr', $pr->tanggal_pr->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Anggaran PR --}}
                        <fieldset class="border p-3 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-dollar-sign me-1"></i> Informasi Anggaran
                            </legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nilai_pr_display" class="form-label fw-semibold">
                                        <i class="fas fa-coins me-1 text-muted"></i> Nilai PR <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="nilai_pr_display" class="form-control"
                                            value="{{ number_format($pr->nilai_pr, 0, ',', '.') }}" required>
                                        <input type="hidden" name="nilai_pr" id="nilai_pr" value="{{ $pr->nilai_pr }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-calendar-check me-1 text-muted"></i> Tahun Anggaran
                                    </label>
                                    <input type="text" class="form-control form-control-sm"
                                        value="{{ $pr->tahun_anggaran }}" readonly disabled>
                                </div>

                                <div class="col-md-12">
                                    <div class="alert alert-primary text-end fw-bold shadow-sm p-2 mb-0">
                                        Total Nilai PR: <span id="total-pr">Rp
                                            {{ number_format($pr->nilai_pr, 0, ',', '.') }}</span>
                                        <input type="hidden" name="total_pr" id="total-pr-hidden"
                                            value="{{ $pr->nilai_pr }}">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Card Footer Aksi --}}
                    <div class="card-footer d-flex justify-content-end p-3 bg-light border-top">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-danger me-2 px-4 shadow-sm">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary text-white px-4 shadow-sm">
                            <i class="fas fa-sync-alt me-1"></i> Update PR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// LOGIKA JAVASCRIPT DIBIARKAN SAMA PERSIS
$(function() {
    const displayInput = $('#nilai_pr_display');
    const hiddenInput = $('#nilai_pr');

    // Mengatur event listener untuk input display
    displayInput.on('input', function() {
        let value = this.value.replace(/\D/g, '');
        // Memastikan input tidak kosong sebelum format
        if (value) {
            this.value = new Intl.NumberFormat('id-ID').format(value);
        }
        hiddenInput.val(value);
        hitungTotal();
    });

    // Fungsi untuk menghitung dan menampilkan total
    function hitungTotal() {
        let nilai = parseInt(hiddenInput.val()) || 0;
        $('#total-pr').text('Rp ' + new Intl.NumberFormat('id-ID').format(nilai));
        $('#total-pr-hidden').val(nilai);
    }

    // Memanggil fungsi saat halaman dimuat untuk inisialisasi
    hitungTotal();
});
</script>
@endpush
@endsection