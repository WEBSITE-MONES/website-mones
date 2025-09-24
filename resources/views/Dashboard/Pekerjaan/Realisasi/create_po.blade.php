@extends('Dashboard.base')

@section('title', 'Form Input PO')

@section('content')
<div class="page-inner">
    {{-- Header Halaman --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <h4 class="page-title fw-bold">
            <i class="fas fa-file-contract me-2 text-primary"></i> Form Input PO
        </h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    ---

    {{-- Validasi Error --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <h5 class="alert-heading fs-6 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Kesalahan Input!</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Kartu Utama Form --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white p-3 rounded-top-3">
            <h4 class="card-title mb-0 text-center fw-bolder">
                INPUT KONTRAK (PO)
            </h4>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('realisasi.storePO', $pr->id) }}" method="POST">
                @csrf

                {{-- Group: Data Kontrak Utama --}}
                <fieldset class="border p-3 mb-4 rounded-3">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                        <i class="fas fa-id-card-alt me-1"></i> Informasi Kontrak
                    </legend>
                    <div class="row g-3">

                        {{-- Tanggal PO/Kontrak --}}
                        <div class="col-md-4">
                            <label for="tanggal_po" class="form-label fw-semibold">Tanggal PO/Kontrak <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="tanggal_po" id="tanggal_po" class="form-control form-control-sm"
                                value="{{ old('tanggal_po') }}" required>
                        </div>

                        {{-- Nomor PO --}}
                        <div class="col-md-4">
                            <label for="nomor_po" class="form-label fw-semibold">Nomor PO <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nomor_po" id="nomor_po" class="form-control form-control-sm"
                                placeholder="Contoh: PO/2024/001" value="{{ old('nomor_po') }}" required>
                        </div>

                        {{-- No. Kontrak/SPPP/SPK --}}
                        <div class="col-md-4">
                            <label for="nomor_kontrak" class="form-label fw-semibold">No. Kontrak/SPPP/SPK</label>
                            <input type="text" name="nomor_kontrak" id="nomor_kontrak"
                                class="form-control form-control-sm" placeholder="Opsional"
                                value="{{ old('nomor_kontrak') }}">
                        </div>

                        {{-- Nilai PO --}}
                        <div class="col-md-4">
                            <label for="nilai_po" class="form-label fw-semibold">Nilai PO <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" name="nilai_po" id="nilai_po" class="form-control text-end" required
                                    placeholder="Misal: 10.000.000" value="{{ old('nilai_po') }}">
                            </div>
                        </div>

                        {{-- Pelaksana --}}
                        <div class="col-md-4">
                            <label for="pelaksana" class="form-label fw-semibold">Pelaksana</label>
                            <input type="text" name="pelaksana" id="pelaksana" class="form-control form-control-sm"
                                placeholder="Contoh: PT. Intan Sejahtera" value="{{ old('pelaksana') }}">
                        </div>

                        {{-- Mekanisme Pembayaran --}}
                        <div class="col-md-4">
                            <label for="mekanisme_pembayaran" class="form-label fw-semibold">Mekanisme Pembayaran <span
                                    class="text-danger">*</span></label>
                            <select name="mekanisme_pembayaran" id="mekanisme_pembayaran"
                                class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Pilih Mekanisme --</option>
                                <option value="uang_muka"
                                    {{ old('mekanisme_pembayaran') == 'uang_muka' ? 'selected' : '' }}>Dengan Uang Muka
                                </option>
                                <option value="termin" {{ old('mekanisme_pembayaran') == 'termin' ? 'selected' : '' }}>
                                    Tanpa Uang Muka</option>
                            </select>
                        </div>

                    </div>
                </fieldset>

                {{-- Group: Waktu Pelaksanaan --}}
                <fieldset class="border p-3 mb-4 rounded-3">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Jadwal Pelaksanaan
                    </legend>
                    <div class="row g-3">

                        {{-- Estimated (Periode) --}}
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Estimated (Periode)</label>
                            <div class="input-group input-group-sm">
                                <input type="date" name="estimated_start" id="estimated_start" class="form-control"
                                    value="{{ old('estimated_start') }}">
                                <span class="input-group-text bg-light fw-semibold">s/d</span>
                                <input type="date" name="estimated_end" id="estimated_end" class="form-control"
                                    value="{{ old('estimated_end') }}">
                            </div>
                            <small class="text-muted fst-italic">Tanggal awal dan akhir pelaksanaan.</small>
                        </div>

                        {{-- Waktu Pelaksanaan --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Waktu Pelaksanaan</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="waktu_pelaksanaan" id="waktu_pelaksanaan"
                                    class="form-control text-end bg-light" readonly placeholder="Otomatis terisi">
                                <span class="input-group-text bg-secondary text-white fw-semibold">Hari</span>
                            </div>
                            <small class="text-muted fst-italic">Dihitung otomatis dari periode.</small>
                        </div>
                    </div>
                </fieldset>

                {{-- Group: Detail Termin Pembayaran --}}
                <fieldset class="border p-3 rounded-3">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-primary">
                        <i class="fas fa-list-ol me-1"></i> Detail Pembayaran (Termin)
                    </legend>

                    {{-- Tabel Termin --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm align-middle" id="termin-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 25%">Uraian</th>
                                    <th class="text-center" style="width: 15%">Persentase (%)</th>
                                    <th class="text-center" style="width: 35%">Syarat Pembayaran</th>
                                    <th class="text-center" style="width: 20%">Nilai Pembayaran (Rp)</th>
                                    <th class="text-center" style="width: 5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Baris termin akan diisi oleh JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Tombol Tambah --}}
                    <button type="button" id="add-termin" class="btn btn-success btn-sm shadow-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Baris Termin
                    </button>

                    {{-- Ringkasan Persentase dan Progress Bar --}}
                    <div class="mt-4 p-3 bg-light border rounded-3">
                        <h6 class="fw-bold mb-2 text-dark">Ringkasan Total Termin</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-primary fw-semibold">Total Persentase: <span id="totalPersen"
                                        class="fs-5">0</span>%</p>
                                <p class="mb-0 text-secondary fw-semibold">Sisa Persentase: <span
                                        id="sisaPersen">100</span>%</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="mb-0 text-success fw-bold fs-5">Sisa Nilai: Rp <span id="sisaNilai">0</span>
                                </p>
                            </div>
                        </div>

                        <div class="progress mt-2" style="height: 25px;">
                            <div id="progressBar" class="progress-bar bg-info fw-bold" role="progressbar"
                                style="width: 0%">
                                0%
                            </div>
                        </div>
                        <small id="persenWarning" class="text-danger fw-semibold d-none mt-2"></small>
                    </div>

                </fieldset>

                {{-- Footer Aksi --}}
                <div class="d-flex justify-content-end pt-3 mt-4 border-top">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-danger me-2 px-4 shadow-sm">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" id="btnSimpanPO" class="btn btn-primary px-4 shadow-sm" disabled>
                        <i class="fas fa-save me-1"></i> Simpan PO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Seluruh logika JavaScript DIBIARKAN SAMA PERSIS agar tidak mengubah fungsionalitas.
// Hanya penambahan id pada input tanggal untuk di-bind ke JS.
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

    // Fungsi hitungNilaiPembayaran yang SAMA PERSIS
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

    // Fungsi hitungWaktuPelaksanaan yang SAMA PERSIS
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

    // binding event yang SAMA PERSIS
    $(document).on("change", "input[name='estimated_start'], input[name='estimated_end']",
        hitungWaktuPelaksanaan);

    function formatRupiah(angka) {
        return formatter.format(angka);
    }

    function parseNumber(val) {
        if (!val && val !== 0) return 0;
        let s = String(val).replace(/\./g, '').replace(/,/g, '.').replace(/[^\d\.-]/g, '');
        return parseFloat(s) || 0;
    }

    function isUangMukaText(text) {
        if (!text) return false;
        return /uang[\s_-]*muka/i.test(String(text).trim());
    }

    // Fungsi hitungNilaiPembayaran LENGKAP yang SAMA PERSIS
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
            $("#progressBar").removeClass("bg-danger bg-warning").addClass("bg-success");
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
        // Penambahan class form-control-sm dan placeholder untuk styling
        let row = `<tr data-index="${index}">
                        <td><input type="text" name="termins[${index}][uraian]" value="${uraian}" placeholder="Misal: Uang Muka / Termin 1" class="form-control form-control-sm inp-uraian"></td>
                        <td><input type="number" name="termins[${index}][persentase]" value="${persen}" placeholder="Cth: 20" class="form-control form-control-sm inp-persen text-end" min="0" max="100"></td>
                        <td><input type="text" name="termins[${index}][syarat_pembayaran]" value="${syarat}" placeholder="Cth: Material On Site / Bast" class="form-control form-control-sm inp-syarat"></td>
                        <td><input type="text" name="termins[${index}][nilai_pembayaran]" class="form-control form-control-sm nilai-pembayaran text-end bg-light" readonly></td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-termin"><i class="fas fa-trash"></i></button></td>
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
    // Trigger awal jika ada old value
    if ($("#mekanisme_pembayaran").val()) {
        $("#mekanisme_pembayaran").trigger('change');
    }


    // event binding yang SAMA PERSIS
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