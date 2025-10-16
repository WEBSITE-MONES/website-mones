/**
 * Format detail untuk expansion row di DataTable Database
 * @param {Array} data - Array data dari baris tabel
 * @returns {string} HTML string untuk detail row
 */
function formatDetails(data) {
    const coa = data[5] || '-';
    const programInv = data[6] || '-';
    const tipeInv = data[7] || '-';
    const kebutuhanDana = data[8] || 'Rp 0';
    const kategoriInv = data[9] || '-';
    const manfaatInv = data[10] || '-';
    const jenisInv = data[11] || '-';

    return `
        <div class="p-4 bg-light rounded-3" style="font-size: 0.9em; border-left: 4px solid #1572E8;">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="detail-item">
                        <small class="text-muted d-block mb-1">Kebutuhan Dana</small>
                        <strong class="text-primary">${kebutuhanDana}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <small class="text-muted d-block mb-1">COA</small>
                        <strong>${coa}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <small class="text-muted d-block mb-1">Program Investasi</small>
                        <strong>${programInv}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <small class="text-muted d-block mb-1">Tipe Investasi</small>
                        <strong>${tipeInv}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <small class="text-muted d-block mb-1">Kategori Investasi</small>
                        <strong>${kategoriInv}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <small class="text-muted d-block mb-1">Manfaat Investasi</small>
                        <strong>${manfaatInv}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <small class="text-muted d-block mb-1">Jenis Investasi</small>
                        <strong>${jenisInv}</strong>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Konfigurasi default untuk DataTables
 */
const dataTableOptions = {
    responsive: true,
    language: {
        paginate: {
            previous: "<i>Previous</i>",
            next: "<i>Next</i>"
        },
        search: "_INPUT_",
        searchPlaceholder: "Cari data...",
        lengthMenu: "Tampilkan _MENU_ data per halaman",
        zeroRecords: "Data tidak ditemukan",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data tersedia",
        infoFiltered: "(disaring dari _MAX_ total data)"
    },
    dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
         "<'row'<'col-sm-12'tr>>" +
         "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>",
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
};

/**
 * Initialize DataTables dan Event Handlers
 */
$(document).ready(function() {
    
    // Initialize DataTable untuk Tabel Realisasi
    if ($('#tabelRealisasi').length) {
        $('#tabelRealisasi').DataTable(dataTableOptions);
    }
    
    // Initialize DataTable untuk Tabel Database
    let dbTable = null;
    if ($('#tabelDatabase').length) {
        dbTable = $('#tabelDatabase').DataTable(dataTableOptions);
    }

    // Event listener untuk expansion row di tabel Database
    if (dbTable) {
        $('#tabelDatabase tbody').on('click', 'td.dt-control', function() {
            const tr = $(this).closest('tr');
            const row = dbTable.row(tr);

            if (row.child.isShown()) {
                // Tutup detail row
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Buka detail row
                const rowData = [];
                row.cells().every(function() {
                    rowData.push(this.data());
                });

                row.child(formatDetails(rowData)).show();
                tr.addClass('shown');
            }
        });
    }

    // Smooth scroll untuk tab navigation
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('data-bs-target');
        
        // Scroll smooth ke konten tab
        $('html, body').animate({
            scrollTop: $(target).offset().top - 100
        }, 300);
    });

    // ========================================
    // KONFIRMASI HAPUS dengan SweetAlert2
    // ========================================
    
    // Handle klik tombol hapus dengan event delegation
    $(document).on('click', '.dropdown-item.text-danger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = $(this);
        const form = button.closest('form');
        
        // Cek apakah ini form delete
        if (form.length && form.attr('action') && form.attr('action').includes('destroy')) {
            
            // Cek apakah SweetAlert2 tersedia
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: "Data PR beserta <strong>PO, Progress, GR, dan Payment</strong> akan dihapus!<br><small class='text-muted'>Data yang dihapus tidak dapat dikembalikan.</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-trash me-2"></i>Ya, Hapus Semua!',
                    cancelButtonText: '<i class="fa fa-times me-2"></i>Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Menghapus Data...',
                            html: 'Mohon tunggu, sedang menghapus data beserta relasinya',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Submit form
                        form[0].submit();
                    }
                });
            } else {
                // Fallback ke confirm() biasa
                if (confirm('Apakah Anda yakin ingin menghapus data PR beserta PO, Progress, GR, dan Payment?\n\nData yang dihapus tidak dapat dikembalikan!')) {
                    form[0].submit();
                }
            }
        }
    })

    // Tooltip initialization (jika menggunakan Bootstrap tooltip)
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Dropdown menu animation
    $('.dropdown').on('show.bs.dropdown', function() {
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(200);
    });

    $('.dropdown').on('hide.bs.dropdown', function() {
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
    });

    // Auto-hide alerts after 5 seconds
    if ($('.alert').length) {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    }

    // Prevent multiple form submissions
    $('form:not([action*="destroy"])').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });

    // Add loading state to buttons with data-loading attribute
    $('[data-loading]').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        const loadingText = btn.data('loading') || 'Loading...';
        
        btn.html(`<i class="fa fa-spinner fa-spin me-2"></i>${loadingText}`);
        btn.prop('disabled', true);
        
        // Re-enable after 10 seconds (safety fallback)
        setTimeout(function() {
            btn.html(originalText);
            btn.prop('disabled', false);
        }, 10000);
    });

});

/**
 * Export functions untuk digunakan di luar scope
 */
window.RealisasiModule = {
    formatDetails: formatDetails,
    dataTableOptions: dataTableOptions
};