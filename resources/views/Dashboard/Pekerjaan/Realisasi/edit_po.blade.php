@extends('Dashboard.base')

@section('title', 'Edit PO')

@section('content')
<div class="page-inner">
    {{-- Header Halaman Ditingkatkan --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title fw-bold">
            <i class="fas fa-edit me-2 text-primary"></i> Edit Purchase Order (PO)
        </h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    ---

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white p-3 rounded-top-3">
                    <h4 class="card-title mb-0 text-center fw-bolder">
                        <i class="fas fa-file-invoice-dollar me-2"></i> FORM EDIT DATA PO
                    </h4>
                </div>

                <form action="{{ route('realisasi.updatePO', $po->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4">
                        {{-- Notifikasi Error --}}
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

                        {{-- Section: Detail PO --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-info-circle me-1"></i> Informasi PO
                            </legend>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="tanggal_po" class="form-label fw-semibold">
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i> Tanggal PO/Kontrak <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="tanggal_po" id="tanggal_po"
                                        class="form-control form-control-sm"
                                        value="{{ old('tanggal_po', $po->tanggal_po) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nomor_po" class="form-label fw-semibold">
                                        <i class="fas fa-barcode me-1 text-muted"></i> Nomor PO <span
                                            class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nomor_po" id="nomor_po"
                                        class="form-control form-control-sm"
                                        value="{{ old('nomor_po', $po->nomor_po) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nomor_kontrak" class="form-label fw-semibold">
                                        <i class="fas fa-file-contract me-1 text-muted"></i> No. Kontrak/SPPP/SPK
                                    </label>
                                    <input type="text" name="nomor_kontrak" id="nomor_kontrak"
                                        class="form-control form-control-sm"
                                        value="{{ old('nomor_kontrak', $po->nomor_kontrak) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="pelaksana" class="form-label fw-semibold">
                                        <i class="fas fa-user-tie me-1 text-muted"></i> Pelaksana
                                    </label>
                                    <input type="text" name="pelaksana" id="pelaksana"
                                        class="form-control form-control-sm"
                                        value="{{ old('pelaksana', $po->pelaksana) }}">
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Anggaran & Waktu --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-dollar-sign me-1"></i> Anggaran & Jadwal
                            </legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nilai_po" class="form-label fw-semibold">
                                        <i class="fas fa-money-bill-wave me-1 text-muted"></i> Nilai PO <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="nilai_po" id="nilai_po" class="form-control"
                                            value="{{ old('nilai_po', number_format($po->nilai_po, 0, ',', '.')) }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="mekanisme_pembayaran" class="form-label fw-semibold">
                                        <i class="fas fa-hand-holding-usd me-1 text-muted"></i> Mekanisme Pembayaran
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="mekanisme_pembayaran" id="mekanisme_pembayaran"
                                        class="form-control form-control-sm" required>
                                        <option value="">-- Pilih Mekanisme --</option>
                                        <option value="uang_muka"
                                            {{ old('mekanisme_pembayaran', $po->mekanisme_pembayaran) == 'uang_muka' ? 'selected' : '' }}>
                                            Dengan Uang Muka
                                        </option>
                                        <option value="termin"
                                            {{ old('mekanisme_pembayaran', $po->mekanisme_pembayaran) == 'termin' ? 'selected' : '' }}>
                                            Tanpa Uang Muka
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="estimated_start" class="form-label fw-semibold">
                                        <i class="fas fa-calendar-check me-1 text-muted"></i> Estimated (Periode)
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="date" name="estimated_start" id="estimated_start"
                                            class="form-control"
                                            value="{{ old('estimated_start', $po->estimated_start) }}">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" name="estimated_end" id="estimated_end" class="form-control"
                                            value="{{ old('estimated_end', $po->estimated_end) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="waktu_pelaksanaan" class="form-label fw-semibold">
                                        <i class="fas fa-hourglass-half me-1 text-muted"></i> Waktu Pelaksanaan
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="waktu_pelaksanaan" id="waktu_pelaksanaan"
                                            class="form-control"
                                            value="{{ old('waktu_pelaksanaan', $po->waktu_pelaksanaan) }}" readonly>
                                        <span class="input-group-text">Hari</span>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{-- Section: Termin Pembayaran --}}
                        <fieldset class="border p-3 mb-4 rounded-3">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                                <i class="fas fa-list-ol me-1"></i> Rincian Termin
                            </legend>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="termin-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Uraian</th>
                                            <th style="width: 15%;">Persentase (%)</th>
                                            <th>Syarat Pembayaran</th>
                                            <th>Nilai Pembayaran (Rp)</th>
                                            <th style="width: 10%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($po->termins as $i => $termin)
                                        <tr>
                                            <td><input type="text" name="termins[{{ $i }}][uraian]"
                                                    value="{{ $termin->uraian }}"
                                                    class="form-control form-control-sm inp-uraian"></td>
                                            <td><input type="number" name="termins[{{ $i }}][persentase]"
                                                    value="{{ $termin->persentase }}"
                                                    class="form-control form-control-sm inp-persen" min="0" max="100">
                                            </td>
                                            <td><input type="text" name="termins[{{ $i }}][syarat_pembayaran]"
                                                    value="{{ $termin->syarat_pembayaran }}"
                                                    class="form-control form-control-sm inp-syarat"></td>
                                            <td><input type="text" class="form-control form-control-sm nilai-pembayaran"
                                                    value="{{ number_format($termin->nilai_pembayaran, 0, ',', '.') }}"
                                                    readonly></td>
                                            <td><button type="button" class="btn btn-danger btn-sm remove-termin"><i
                                                        class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="add-termin" class="btn btn-success btn-sm mt-2">
                                <i class="fas fa-plus me-1"></i> Tambah Baris
                            </button>

                            {{-- Ringkasan & Progress Bar --}}
                            <div class="mt-4 p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold mb-2">Ringkasan Termin</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1">Total Persentase: <strong id="totalPersen"
                                                class="text-primary">0%</strong></p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1">Sisa Persentase: <strong id="sisaPersen"
                                                class="text-danger">100%</strong></p>
                                    </div>
                                    <div class="col-12">
                                        <div class="progress mt-2" style="height: 25px;">
                                            <div id="progressBar" class="progress-bar" role="progressbar"
                                                style="width: 0%">0%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <h6 class="mb-0">Sisa Nilai: <strong class="text-danger">Rp <span
                                                id="sisaNilai">0</span></strong></h6>
                                    <small id="persenWarning" class="text-danger fw-bold d-none"></small>
                                </div>
                            </div>

                        </fieldset>
                    </div>

                    {{-- Card Footer Aksi --}}
                    <div class="card-footer d-flex justify-content-end p-3 bg-light border-top">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-danger me-2 px-4 shadow-sm">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" id="btnSimpanPO" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-sync-alt me-1"></i> Update PO
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
$(document).ready(function() {
    const formatter = new Intl.NumberFormat('id-ID');

    // format input Rupiah saat ketik
    $("#nilai_po").on("input", function() {
        let val = $(this).val().replace(/\D/g, ''); // hanya angka
        $(this).val(new Intl.NumberFormat('id-ID').format(val));
        hitungNilaiPembayaran();
    });

    function parseNumber(val) {
        if (!val) return 0;
        let s = String(val).replace(/\./g, '').replace(/,/g, '.').replace(/[^\d\.-]/g, '');
        return parseFloat(s) || 0;
    }

    function hitungNilaiPembayaran() {
        let nilaiPO = parseNumber($("#nilai_po").val());
        $("#termin-table tbody tr").each(function() {
            let persen = parseFloat($(this).find(".inp-persen").val()) || 0;
            let nilai = (persen / 100) * nilaiPO;
            $(this).find(".nilai-pembayaran").val(new Intl.NumberFormat('id-ID').format(Math.round(
                nilai)));
        });
    }

    $(document).on("input", ".inp-persen", hitungNilaiPembayaran);

    function hitungWaktuPelaksanaan() {
        let start = $("input[name='estimated_start']").val();
        let end = $("input[name='estimated_end']").val();

        if (start && end) {
            let startDate = new Date(start);
            let endDate = new Date(end);

            if (!isNaN(startDate) && !isNaN(endDate) && endDate >= startDate) {
                let diffTime = endDate.getTime() - startDate.getTime();
                let diffDays = Math.floor(diffTime / (1000 * 3600 * 24)) + 1;
                $("#waktu_pelaksanaan").val(diffDays);
            } else {
                $("#waktu_pelaksanaan").val("");
            }
        } else {
            $("#waktu_pelaksanaan").val("");
        }
    }

    // binding event
    $(document).on("change", "input[name='estimated_start'], input[name='estimated_end']",
        hitungWaktuPelaksanaan);

    function formatRupiah(angka) {
        return formatter.format(angka);
    }

    function isUangMukaText(text) {
        if (!text) return false;
        return /uang[\s_-]*muka/i.test(String(text).trim());
    }

    function hitungNilaiPembayaran() {
        let nilaiPO = parseNumber($("input[name='nilai_po']").val());
        let totalPersenTermin = 0;
        let totalNilai = 0;
        let uangMukaPersen = 0;
        let uangMukaNilai = 0;

        // cek apakah ada baris uang muka
        const uangMukaRow = $("#termin-table tbody tr").filter(function() {
            return isUangMukaText($(this).find(".inp-uraian").val());
        }).first();

        if (uangMukaRow.length) {
            uangMukaPersen = parseNumber(uangMukaRow.find(".inp-persen").val());
            uangMukaNilai = (uangMukaPersen / 100) * nilaiPO;
        }

        $("#termin-table tbody tr").each(function() {
            let $row = $(this);
            let uraian = $row.find(".inp-uraian").val() || "";
            let persen = parseNumber($row.find(".inp-persen").val());
            let nilaiBayar = 0;

            if (isUangMukaText(uraian)) {
                // Uang muka dihitung normal
                nilaiBayar = uangMukaNilai;
            } else if (uangMukaPersen > 0) {
                // Termin dikurangi proporsi uang muka
                nilaiBayar = ((persen / 100) * nilaiPO) - ((persen / 100) * uangMukaNilai);
                totalPersenTermin += persen;
            } else {
                // Tanpa uang muka
                nilaiBayar = (persen / 100) * nilaiPO;
                totalPersenTermin += persen;
            }

            if (nilaiBayar < 0) nilaiBayar = 0;

            totalNilai += nilaiBayar;
            $row.find(".nilai-pembayaran").val(formatRupiah(Math.round(nilaiBayar)));
        });

        // update total & sisa (hanya termin)
        $("#totalPersen").text(totalPersenTermin.toFixed(2));
        let sisaPersen = 100 - totalPersenTermin;
        $("#sisaPersen").text(sisaPersen.toFixed(2));

        let sisaNilai = Math.max(0, Math.round(nilaiPO - totalNilai));
        $("#sisaNilai").text(formatRupiah(sisaNilai));

        // progress bar
        let pct = Math.max(0, Math.min(100, totalPersenTermin));
        $("#progressBar").css("width", pct + "%").text(pct.toFixed(2) + "%");

        // kontrol tombol
        if (totalPersenTermin === 100) {
            $("#progressBar").removeClass("bg-warning bg-danger").addClass("bg-success");
            $("#persenWarning").addClass("d-none");
            $("#btnSimpanPO").prop("disabled", false);
        } else if (totalPersenTermin < 100) {
            $("#progressBar").removeClass("bg-success bg-danger").addClass("bg-warning");
            $("#persenWarning").removeClass("d-none").text("Total persentase termin kurang dari 100%.");
            $("#btnSimpanPO").prop("disabled", true);
        } else {
            $("#progressBar").removeClass("bg-success bg-warning").addClass("bg-danger");
            $("#persenWarning").removeClass("d-none").text("Total persentase termin lebih dari 100%.");
            $("#btnSimpanPO").prop("disabled", true);
        }
    }

    function addRow(uraian = '', persen = '', syarat = '') {
        let index = $("#termin-table tbody tr").length;
        let row = `<tr>
                            <td><input type="text" name="termins[${index}][uraian]" value="${uraian}" placeholder="Misal: Uang Muka / Termin 1" class="form-control form-control-sm inp-uraian"></td>
                            <td><input type="number" name="termins[${index}][persentase]" value="${persen}" placeholder="Contoh: 20" class="form-control form-control-sm inp-persen" min="0" max="100"></td>
                            <td><input type="text" name="termins[${index}][syarat_pembayaran]" value="${syarat}" placeholder="Contoh: Material On Site / Bast" class="form-control form-control-sm inp-syarat"></td>
                            <td><input type="text" name="termins[${index}][nilai_pembayaran]" class="form-control form-control-sm nilai-pembayaran" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-termin"><i class="fas fa-trash"></i></button></td>
                        </tr>`;
        $('#termin-table tbody').append(row);
        hitungNilaiPembayaran();
    }

    // Mekanisme pembayaran -> generate baris pertama otomatis
    $("#mekanisme_pembayaran").change(function() {
        let mekanisme_pembayaran = $(this).val();
        $("#termin-table tbody").empty();

        if (mekanisme_pembayaran === "uang_muka") {
            addRow("Uang Muka", "", "");
        } else if (mekanisme_pembayaran === "termin") {
            addRow("Termin 1", "", "");
        }

        hitungNilaiPembayaran();
    });

    // event binding
    $(document).on("input change", "input[name='nilai_po'], .inp-persen, .inp-uraian", hitungNilaiPembayaran);

    $('#add-termin').click(function() {
        addRow();
    });

    $(document).on('click', '.remove-termin', function() {
        $(this).closest('tr').remove();
        hitungNilaiPembayaran();
    });

    // trigger awal
    hitungNilaiPembayaran();
});
</script>
@endpush

@endsection