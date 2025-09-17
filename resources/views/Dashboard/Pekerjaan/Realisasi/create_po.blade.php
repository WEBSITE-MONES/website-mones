@extends('Dashboard.base')

@section('title', 'Form Input PO')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Form Input PO</h4>
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

    <div class="card card-round shadow-sm">
        <div class="card-header text-white text-center rounded-top">
            <h3 class="card-title mb-0">FORM INPUT PO</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('realisasi.storePO', $pr->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal PO/Kontrak <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_po" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor PO <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_po" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">No. Kontrak/SPPP/SPK</label>
                        <input type="text" name="nomor_kontrak" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nilai PO <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="nilai_po" id="nilai_po" class="form-control" required
                                placeholder="Misal: 10.000.000">
                        </div>
                    </div>


                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estimated (Periode)</label>
                        <div class="input-group">
                            <input type="date" name="estimated_start" class="form-control">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="estimated_end" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Waktu Pelaksanaan</label>
                        <div class="input-group">
                            <input type="number" name="waktu_pelaksanaan" id="waktu_pelaksanaan" class="form-control"
                                readonly>
                            <span class="input-group-text">Hari</span>
                        </div>
                    </div>


                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pelaksana</label>
                        <input type="text" name="pelaksana" class="form-control"
                            placeholder="Contoh: PT. Intan Sejahtera">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mekanisme Pembayaran <span class="text-danger">*</span></label>
                        <select name="mekanisme_pembayaran" id="mekanisme_pembayaran" class="form-control" required>
                            <option value="">-- Pilih Mekanisme --</option>
                            <option value="uang_muka">Dengan Uang Muka</option>
                            <option value="termin">Tanpa Uang Muka</option>

                        </select>
                    </div>
                </div>

                {{-- Termin / detail pembayaran --}}
                <table class="table table-bordered" id="termin-table">
                    <thead>
                        <tr class="table-light">
                            <th>Uraian</th>
                            <th>Persentase (%)</th>
                            <th>Syarat Pembayaran</th>
                            <th>Nilai Pembayaran (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="button" id="add-termin" class="btn btn-success btn-sm">Tambah Baris</button>

                {{-- Ringkasan Persentase --}}
                <div class="mt-4">
                    <h6>Total Persentase Termin: <span id="totalPersen">0</span>%</h6>
                    <h6>Sisa Persentase Termin: <span id="sisaPersen">100</span>%</h6>
                    <h6>Sisa Nilai: Rp <span id="sisaNilai">0</span></h6>

                    <div class="progress">
                        <div id="progressBar" class="progress-bar bg-info" role="progressbar" style="width: 0%">
                            0%
                        </div>
                    </div>
                    <small id="persenWarning" class="text-danger d-none"></small>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-danger">Batal</a>
                    <button type="submit" id="btnSimpanPO" class="btn btn-primary" disabled>Simpan PO</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
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

    function parseNumber(val) {
        if (!val && val !== 0) return 0;
        let s = String(val).replace(/\./g, '').replace(/,/g, '.').replace(/[^\d\.-]/g, '');
        return parseFloat(s) || 0;
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
        let row = `<tr>
                        <td><input type="text" name="termins[${index}][uraian]" value="${uraian}" placeholder="Misal: Uang Muka / Termin 1" class="form-control inp-uraian"></td>
                        <td><input type="number" name="termins[${index}][persentase]" value="${persen}" placeholder="Contoh: 20" class="form-control inp-persen" min="0" max="100"></td>
                        <td><input type="text" name="termins[${index}][syarat_pembayaran]" value="${syarat}" placeholder="Contoh: Material On Site / Bast" class="form-control inp-syarat"></td>
                        <td><input type="text" name="termins[${index}][nilai_pembayaran]" class="form-control nilai-pembayaran" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-termin">Hapus</button></td>
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