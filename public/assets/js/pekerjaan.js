$(function () {

    /** ==============================
     * Mapping Data
     * ============================== */
    const coaSubMap = {
        '201': 'Bangunan Fasilitas',
        '202': 'Kapal',
        '203': 'Alat-Alat Fasilitas',
        '204': 'Instalasi Fasilitas',
        '211': 'Tanah dan Hak Atas Tanah',
        '212': 'Jalan, Bangunan, Sarana dan Prasarana',
        '213': 'Peralatan dan Perlengkapan',
        '221': 'Kendaraan',
        '222': 'Emplasemen'
    };

    const tipeInvestasiMap = {
        'A': 'Investasi Murni',
        'B': 'Investasi Multi Year',
        'B1': 'Investasi Multi Year',
        'B2': 'Investasi Multi Year',
        'B3': 'Investasi Multi Year',
        'B4': 'Investasi Multi Year',
        'C': 'Investasi Carry Forward/Over',
        'KAP': 'Kapitalisasi Bunga',
        'PMPI': 'Penyertaan Modal'
    };

    /** ==============================
     * Utility Functions
     * ============================== */
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0
        }).format(angka);
    }

    function parseNumber(str) {
    if (!str) return 0;
    const digits = String(str).replace(/\D/g, '');
    return parseInt(digits) || 0;
}

    function rkapInput(tahun) {
        return `
            <div class="col-12">
                <label class="form-label fw-semibold small mb-0">
                    RKAP ${tahun} <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control rkap format-rupiah"
                           data-tahun="${tahun}" value="0" required>
                    <input type="hidden" name="rkap[${tahun}]" id="rkap-hidden-${tahun}" value="0">
                </div>
            </div>
        `;
    }

    function generateRkapFields(tipe, tahunUsulan) {
        const wrapper = $('#rkap-wrapper');
        wrapper.empty();

        if (!tipe || !tahunUsulan) {
            wrapper.html(
                '<p class="text-muted small m-2">Pilih Tipe dan Tahun Usulan untuk melihat alokasi RKAP.</p>'
            );
            return;
        }

        if (['A', 'C', 'KAP', 'PMPI'].includes(tipe)) {
            wrapper.append(rkapInput(tahunUsulan));
        } else if (['B', 'B1', 'B2', 'B3', 'B4'].includes(tipe)) {
            const tahunAwal = parseInt(tahunUsulan);
            const tahunAkhir = tahunAwal + 2;
            for (let th = tahunAwal; th <= tahunAkhir; th++) {
                wrapper.append(rkapInput(th));
            }
        } else {
            wrapper.html('<p class="text-muted small m-2">Tipe ini tidak memerlukan alokasi RKAP.</p>');
        }
    }

    function hitungTotal() {
        const kebutuhan = parseNumber($('#kebutuhan_dana').val());
        let totalRkap = 0;

        $('.rkap').each(function () {
            const nilai = parseNumber($(this).val());
            totalRkap += nilai;
        });

        // Debug log
        console.log('Kebutuhan Dana:', kebutuhan);
        console.log('Total RKAP:', totalRkap);
        console.log('Selisih:', Math.abs(kebutuhan - totalRkap));

        // Update hidden input
        $('#total-dana-hidden').val(totalRkap);

        // Cek kesamaan dengan toleransi kecil untuk floating point
        const selisih = Math.abs(totalRkap - kebutuhan);
        
        if (selisih > 0) {
            $('#total-dana').html(`⚠️ RKAP tidak sama dengan Kebutuhan Dana!<br><small>Selisih: Rp ${formatRupiah(selisih)}</small>`);
            $('#total-dana').addClass('text-danger');
        } else {
            $('#total-dana').removeClass('text-danger');
            $('#total-dana').text(
                new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(totalRkap)
            );
        }
    }

    /** ==============================
     * Event Bindings
     * ============================== */
    $('#coa').on('change', function () {
        const val = $(this).val();
        $('#coa_sub').val(coaSubMap[val] || '');
    });

    $('#tipe').on('change', function () {
        const val = $(this).val();
        $('#tipe_investasi').val(tipeInvestasiMap[val] || '');
        generateRkapFields(val, $('#tahun_usulan').val());
        
        // Load existing RKAP after fields generated
        if (typeof window.existingRkap !== 'undefined') {
            setTimeout(loadExistingRkap, 100);
        } else {
            hitungTotal();
        }
    });

    $('#tahun_usulan').on('change', function () {
        generateRkapFields($('#tipe').val(), $(this).val());
        
        // Load existing RKAP after fields generated
        if (typeof window.existingRkap !== 'undefined') {
            setTimeout(loadExistingRkap, 100);
        } else {
            hitungTotal();
        }
    });

    $(document).on('input', '#kebutuhan_dana_display, .rkap', function () {
        let val = parseNumber($(this).val());
        $(this).val(formatRupiah(val));

        if ($(this).hasClass('rkap')) {
            let tahun = $(this).data('tahun');
            $('#rkap-hidden-' + tahun).val(val);
        } else {
            $('#kebutuhan_dana').val(val);
        }

        hitungTotal();
    });

    /** ==============================
     * Load Existing RKAP
     * ============================== */
    function loadExistingRkap() {
        if (typeof window.existingRkap === 'undefined') return;

        Object.keys(window.existingRkap).forEach(function (tahun) {
            const nilai = parseInt(window.existingRkap[tahun]) || 0;
            
            if (nilai === 0) return; // Skip jika 0

            const inputDisplay = $('#rkap-wrapper').find(`input.rkap[data-tahun="${tahun}"]`);
            const inputHidden = $(`#rkap-hidden-${tahun}`);

            if (inputDisplay.length && inputHidden.length) {
                inputDisplay.val(formatRupiah(nilai));
                inputHidden.val(nilai);
                console.log(`Loaded RKAP ${tahun}: ${nilai}`);
            }
        });

        hitungTotal();
    }

    /** ==============================
     * Init on Page Load
     * ============================== */
    $('#coa').trigger('change');
    $('#tipe').trigger('change');

});