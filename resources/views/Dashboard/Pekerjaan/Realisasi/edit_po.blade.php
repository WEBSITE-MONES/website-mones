@extends('Dashboard.base')

@section('title', 'Edit PO')

@section('content')
<div class="page-inner">
    {{-- Header Halaman (disesuaikan dengan referensi) --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
        <div>
            <h2 class="page-title">Formulir Edit Kontrak (PO)</h2>
            <h5 class="fw-normal text-muted">Perbarui detail kontrak, jadwal, dan termin pembayaran.</h5>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- Validasi Error --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i> Gagal!</strong> Terdapat kesalahan pada input Anda.
        Silakan periksa kembali.
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('realisasi.updatePO', $po->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- KOLOM KIRI: INPUT UTAMA --}}
            <div class="col-lg-8">
                {{-- CARD 1: INFORMASI KONTRAK & JADWAL --}}
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        {{-- Group: Data Kontrak Utama --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-id-card-alt text-primary me-2"></i>Informasi Kontrak
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="tanggal_po" class="form-label">Tanggal PO/Kontrak <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_po" id="tanggal_po" class="form-control"
                                    value="{{ old('tanggal_po', $po->tanggal_po) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label for="nomor_po" class="form-label">Nomor PO <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nomor_po" id="nomor_po" class="form-control"
                                    value="{{ old('nomor_po', $po->nomor_po) }}" required>
                            </div>
                            <div class="col-md-12">
                                <label for="nomor_kontrak" class="form-label">No. Kontrak/SPPP/SPK</label>
                                <input type="text" name="nomor_kontrak" id="nomor_kontrak" class="form-control"
                                    value="{{ old('nomor_kontrak', $po->nomor_kontrak) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="pelaksana" class="form-label">Pelaksana</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hard-hat"></i></span>
                                    <input type="text" name="pelaksana" id="pelaksana" class="form-control"
                                        value="{{ old('pelaksana', $po->pelaksana) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="mekanisme_pembayaran" class="form-label">Mekanisme Pembayaran <span
                                        class="text-danger">*</span></label>
                                <select name="mekanisme_pembayaran" id="mekanisme_pembayaran" class="form-select"
                                    required>
                                    <option value="">-- Pilih Mekanisme --</option>
                                    <option value="uang_muka"
                                        {{ old('mekanisme_pembayaran', $po->mekanisme_pembayaran) == 'uang_muka' ? 'selected' : '' }}>
                                        Dengan Uang Muka
                                    </option>
                                    <option value="termin"
                                        {{ old('mekanisme_pembayaran', $po->mekanisme_pembayaran) == 'termin' ? 'selected' : '' }}>
                                        Tanpa Uang Muka (Termin)
                                    </option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Group: Waktu Pelaksanaan --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>Jadwal Pelaksanaan
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Periode Pelaksanaan</label>
                                <div class="input-group">
                                    <input type="date" name="estimated_start" id="estimated_start" class="form-control"
                                        value="{{ old('estimated_start', $po->estimated_start) }}">
                                    <span class="input-group-text">s/d</span>
                                    <input type="date" name="estimated_end" id="estimated_end" class="form-control"
                                        value="{{ old('estimated_end', $po->estimated_end) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Durasi</label>
                                <div class="input-group">
                                    <input type="number" name="waktu_pelaksanaan" id="waktu_pelaksanaan"
                                        class="form-control bg-light" readonly placeholder="Otomatis"
                                        value="{{ old('waktu_pelaksanaan', $po->waktu_pelaksanaan) }}">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: DETAIL TERMIN --}}
                <div class="card card-round shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-3">
                            <i class="fas fa-list-ol text-primary me-2"></i>Detail Pembayaran (Termin)
                        </h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered table-sm align-middle" id="termin-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Uraian</th>
                                        <th class="text-center" style="width: 15%">Persen (%)</th>
                                        <th class="text-center">Syarat Pembayaran</th>
                                        <th class="text-center" style="width: 25%">Nilai Pembayaran (Rp)</th>
                                        <th class="text-center" style="width: 5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Baris termin diisi dari data yang ada --}}
                                    @foreach($po->termins as $i => $termin)
                                    <tr>
                                        <td><input type="text" name="termins[{{ $i }}][uraian]"
                                                value="{{ $termin->uraian }}"
                                                class="form-control form-control-sm inp-uraian"></td>
                                        <td><input type="number" name="termins[{{ $i }}][persentase]"
                                                value="{{ $termin->persentase }}"
                                                class="form-control form-control-sm inp-persen text-end" min="0"
                                                max="100"></td>
                                        <td><input type="text" name="termins[{{ $i }}][syarat_pembayaran]"
                                                value="{{ $termin->syarat_pembayaran }}"
                                                class="form-control form-control-sm inp-syarat"></td>
                                        <td><input type="text"
                                                class="form-control form-control-sm nilai-pembayaran text-end bg-light"
                                                value="{{ number_format($termin->nilai_pembayaran, 0, ',', '.') }}"
                                                readonly></td>
                                        <td class="text-center"><button type="button"
                                                class="btn btn-danger btn-sm remove-termin"><i
                                                    class="fas fa-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-termin" class="btn btn-success btn-sm shadow-sm">
                            <i class="fas fa-plus me-1"></i> Tambah Baris Termin
                        </button>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: RINGKASAN & AKSI (STICKY) --}}
            <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 sticky-lg-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3 border-bottom pb-2">
                            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Ringkasan Kontrak
                        </h5>
                        <div class="mb-4">
                            <label for="nilai_po" class="form-label fw-bold">Nilai PO <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" name="nilai_po" id="nilai_po"
                                    class="form-control form-control-lg text-end fs-5 fw-bold" required placeholder="0"
                                    value="{{ old('nilai_po', number_format($po->nilai_po, 0, ',', '.')) }}">
                            </div>
                        </div>

                        <div class="p-3 bg-light border rounded-3 mb-4">
                            <h6 class="fw-bold mb-2 text-dark">Status Pembayaran Termin</h6>
                            <div class="d-flex justify-content-between">
                                <p class="mb-1 text-primary fw-semibold">Total Persentase:</p>
                                <p class="mb-1 text-primary fw-semibold fs-5"><span id="totalPersen">0</span>%</p>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <p class="mb-2">Sisa Persentase:</p>
                                <p class="mb-2"><span id="sisaPersen">100</span>%</p>
                            </div>
                            <div class="progress mb-2" style="height: 25px;">
                                <div id="progressBar" class="progress-bar bg-info fw-bold fs-6" role="progressbar"
                                    style="width: 0%">0%</div>
                            </div>
                            <small id="persenWarning" class="text-danger fw-semibold d-none"></small>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between text-success">
                                <h6 class="fw-bold mb-0">Sisa Nilai:</h6>
                                <h6 class="fw-bold mb-0">Rp <span id="sisaNilai">0</span></h6>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="btnSimpanPO" class="btn btn-primary btn-lg">
                                <i class="fas fa-sync-alt me-2"></i> Update PO
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
{{-- Seluruh logika JavaScript DARI FILE EDIT PO SEBELUMNYA, DIBIARKAN SAMA PERSIS --}}
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
                nilaiBayar = uangMukaNilai;
            } else if (uangMukaPersen > 0) {
                let basisPerhitungan = nilaiPO - uangMukaNilai;
                nilaiBayar = (persen / 100) * basisPerhitungan;
                totalPersenTermin += persen;
            } else {
                nilaiBayar = (persen / 100) * nilaiPO;
                totalPersenTermin += persen;
            }

            if (nilaiBayar < 0) nilaiBayar = 0;

            totalNilai += nilaiBayar;
            $row.find(".nilai-pembayaran").val(formatRupiah(Math.round(nilaiBayar)));
        });

        $("#totalPersen").text(totalPersenTermin.toFixed(2));
        let sisaPersen = 100 - totalPersenTermin;
        $("#sisaPersen").text(sisaPersen.toFixed(2));

        let sisaNilai = Math.max(0, Math.round(nilaiPO - totalNilai));
        $("#sisaNilai").text(formatRupiah(sisaNilai));

        let pct = Math.max(0, Math.min(100, totalPersenTermin));
        $("#progressBar").css("width", pct + "%").text(pct.toFixed(2) + "%");

        const warningText = $("#persenWarning");
        warningText.addClass("d-none");

        // Menggunakan toleransi untuk perbandingan float
        if (Math.abs(totalPersenTermin - 100) < 0.01) {
            $("#progressBar").removeClass("bg-danger bg-info").addClass("bg-success");
            $("#btnSimpanPO").prop("disabled", false);
        } else {
            $("#btnSimpanPO").prop("disabled", true);
            if (totalPersenTermin < 100) {
                $("#progressBar").removeClass("bg-success bg-danger").addClass("bg-info");
                warningText.removeClass("d-none").text("Total persen termin harus 100%.");
            } else {
                $("#progressBar").removeClass("bg-success bg-info").addClass("bg-danger");
                warningText.removeClass("d-none").text("Total persen termin melebihi 100%.");
            }
        }
    }

    function addRow(uraian = '', persen = '', syarat = '') {
        let index = $("#termin-table tbody tr").length;
        let row = `<tr>
            <td><input type="text" name="termins[${index}][uraian]" value="${uraian}" placeholder="Misal: Uang Muka / Termin 1" class="form-control form-control-sm inp-uraian"></td>
            <td><input type="number" name="termins[${index}][persentase]" value="${persen}" placeholder="Cth: 20" class="form-control form-control-sm inp-persen text-end" min="0" max="100"></td>
            <td><input type="text" name="termins[${index}][syarat_pembayaran]" value="${syarat}" placeholder="Cth: Material On Site / Bast" class="form-control form-control-sm inp-syarat"></td>
            <td><input type="text" name="termins[${index}][nilai_pembayaran]" class="form-control form-control-sm nilai-pembayaran text-end bg-light" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-termin"><i class="fas fa-trash"></i></button></td>
        </tr>`;
        $('#termin-table tbody').append(row);
        updateRowIndexes();
        hitungNilaiPembayaran();
    }

    function updateRowIndexes() {
        $('#termin-table tbody tr').each(function(index) {
            $(this).find('input, select').each(function() {
                let name = $(this).attr('name');
                if (name) {
                    let newName = name.replace(/\[\d+\]/, `[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
    }

    // Mekanisme pembayaran tidak akan menghapus baris yang sudah ada di halaman edit
    // karena data harus dipertahankan. User bisa hapus manual jika perlu.
    $("#mekanisme_pembayaran").change(function() {
        // Logika ini bisa disesuaikan, untuk saat ini kita biarkan agar tidak menghapus data edit.
        // Jika ingin menghapus, bisa uncomment baris di bawah.
        // $("#termin-table tbody").empty(); 
        hitungNilaiPembayaran();
    });

    $(document).on("input change", "input[name='nilai_po'], .inp-persen, .inp-uraian", hitungNilaiPembayaran);

    $('#add-termin').click(function() {
        addRow();
    });

    $(document).on('click', '.remove-termin', function() {
        $(this).closest('tr').remove();
        updateRowIndexes();
        hitungNilaiPembayaran();
    });

    // Trigger kalkulasi awal saat halaman dimuat
    hitungNilaiPembayaran();
    hitungWaktuPelaksanaan();
});
</script>
@endpush

@endsection