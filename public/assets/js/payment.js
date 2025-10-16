document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('paymentForm');
    const terminCheckboxes = document.querySelectorAll('.termin-checkbox');
    const selectedTerminsList = document.getElementById('selectedTerminsList');
    const grandTotalPaymentEl = document.getElementById('grandTotalPayment');
    const selectedCountEl = document.getElementById('selectedCount');
    const emptyState = document.getElementById('emptyState');
    const submitBtn = document.getElementById('submitBtn');

    // Fungsi untuk memformat angka menjadi format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }

    // Fungsi untuk memperbarui ringkasan pembayaran
    function updateSummary() {
        let grandTotal = 0;
        let selectedCount = 0;
        selectedTerminsList.innerHTML = ''; // Kosongkan daftar

        terminCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const nilai = parseFloat(checkbox.dataset.nilai);
                const uraian = checkbox.dataset.uraian;

                grandTotal += nilai;
                selectedCount++;

                // Tambahkan item ke daftar rincian
                const rincianItem = document.createElement('div');
                rincianItem.className = 'rincian-item';
                rincianItem.innerHTML = `
                    <span class="rincian-item-label">${uraian}</span>
                    <span class="rincian-item-value">${formatRupiah(nilai)}</span>
                `;
                selectedTerminsList.appendChild(rincianItem);
            }
        });

        // Tampilkan atau sembunyikan empty state
        if (selectedCount > 0) {
            emptyState.style.display = 'none';
        } else {
            selectedTerminsList.appendChild(emptyState);
            emptyState.style.display = 'block';
        }

        // Update total dan jumlah termin terpilih
        grandTotalPaymentEl.textContent = formatRupiah(grandTotal);
        selectedCountEl.textContent = `${selectedCount} Termin Dipilih`;

        // Aktifkan atau nonaktifkan tombol submit
        validateForm();
    }

    // Fungsi untuk validasi form sebelum submit
    function validateForm() {
        const terminSelected = document.querySelectorAll('.termin-checkbox:checked').length > 0;
        const tanggalPayment = document.getElementById('tanggal_payment').value.trim() !== '';
        const nomorPayment = document.getElementById('nomor_payment').value.trim() !== '';

        if (terminSelected && tanggalPayment && nomorPayment) {
            submitBtn.disabled = false;
            return true;
        } else {
            submitBtn.disabled = true;
            return false;
        }
    }

    // Tambahkan event listener untuk setiap checkbox termin
    terminCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSummary);
    });

    // Tambahkan event listener untuk input informasi pembayaran
    document.getElementById('tanggal_payment').addEventListener('input', validateForm);
    document.getElementById('nomor_payment').addEventListener('input', validateForm);
    
    // Logika untuk upload file
    document.querySelectorAll('.file-input').forEach(input => {
        const container = input.closest('.custom-file-container');
        const uploadLabel = container.querySelector('.custom-file-upload-label');
        const infoContainer = container.querySelector('.file-info-container');
        const fileNameEl = container.querySelector('.file-name');
        const removeBtn = container.querySelector('.btn-remove-file');

        input.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameEl.textContent = this.files[0].name;
                uploadLabel.classList.add('d-none');
                infoContainer.classList.remove('d-none');
            }
        });

        removeBtn.addEventListener('click', function() {
            input.value = ''; // Reset file input
            uploadLabel.classList.remove('d-none');
            infoContainer.classList.add('d-none');
        });
    });


    // Event listener untuk submit form
    form.addEventListener('submit', function (e) {
        if (!validateForm()) {
            e.preventDefault();
            const validationAlert = document.getElementById('validation-alert');
            const validationMessage = document.getElementById('validation-message');
            let messages = [];

            if (document.querySelectorAll('.termin-checkbox:checked').length === 0) {
                messages.push('Anda harus memilih minimal satu termin.');
            }
            if (document.getElementById('tanggal_payment').value.trim() === '') {
                messages.push('Tanggal payment tidak boleh kosong.');
            }
            if (document.getElementById('nomor_payment').value.trim() === '') {
                messages.push('Nomor payment request tidak boleh kosong.');
            }
            
            validationMessage.innerHTML = messages.join('<br>');
            validationAlert.classList.remove('d-none');
            window.scrollTo(0, 0); // Scroll ke atas untuk melihat notifikasi
        }
    });

    // Panggil updateSummary di awal untuk inisialisasi
    updateSummary();
});