(() => {
    let newProgressIndex = 0;

    function pushDelete(name, value) {
        const container = document.getElementById('deletes');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        container.appendChild(input);
    }

    // Tambah Progress Baru
    document.getElementById('addProgress')?.addEventListener('click', () => {
        newProgressIndex++;
        const idx = newProgressIndex;
        const html = `
        <div class="card mb-2 progress-group" data-progress-id="new_${idx}">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Pekerjaan</label>
                        <input type="text" name="new_progress[${idx}][jenis_pekerjaan]" class="form-control form-control-sm">
                    </div>
                    <div class="text-end ms-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-new-progress">Hapus Progress</button>
                    </div>
                </div>

                <div class="mt-2 text-end">
                    <button type="button" class="btn btn-success btn-sm add-sub" data-progress-id="new_${idx}">
                        + Tambah Sub Pekerjaan
                    </button>
                </div>
            </div>
        </div>`;
        document.getElementById('progressGroups').insertAdjacentHTML('beforeend', html);
    });

    // Tambah Sub Baru
    $(document).on('click', '.add-sub', function () {
        let progressId = $(this).data('progress-id');
        let subIdx = Date.now(); // supaya unik
        let newSubId = `${progressId}_${subIdx}`;

        let subHtml = `
        <div class="card mb-2 p-2 border border-secondary sub-group" data-sub-id="${newSubId}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1 pe-2">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Sub Pekerjaan</label>
                            <input type="text" class="form-control form-control-sm"
                                   name="new_subs[${progressId}][${subIdx}][sub_pekerjaan]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Volume</label>
                            <input type="number" step="0.01" class="form-control form-control-sm"
                                   name="new_subs[${progressId}][${subIdx}][volume]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sat.</label>
                            <input type="text" class="form-control form-control-sm"
                                   name="new_subs[${progressId}][${subIdx}][satuan]">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Bobot (%)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm"
                                   name="new_subs[${progressId}][${subIdx}][bobot]">
                        </div>
                    </div>
                </div>
                <div class="text-end ms-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-sub">Hapus Sub</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Minggu</th>
                            <th>Tanggal Awal</th>
                            <th>Tanggal Akhir</th>
                            <th>Bulan</th>
                            <th>Rencana (%)</th>
                            <th>Realisasi (%)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="details-${newSubId}"></tbody>
                </table>
            </div>

            <div class="mt-1 text-end">
                <button type="button" class="btn btn-success btn-sm add-minggu" data-sub-id="${newSubId}">
                    + Minggu
                </button>
            </div>
        </div>`;
        $(this).closest('.progress-group').find('.card-body').append(subHtml);
    });

    // Tambah Detail (Minggu)
    document.addEventListener('click', e => {
        if (!e.target.classList.contains('add-minggu')) return;
        const sid = e.target.dataset.subId;
        const tbody = document.getElementById('details-' + sid);
        if (!tbody) return;
        const idx = tbody.querySelectorAll('tr').length;
        const tmp = Date.now();
        const row = `
            <tr>
                <td>${idx + 1}</td>
                <td><input type="number" name="new_details[${sid}][${tmp}][minggu]" class="form-control form-control-sm"></td>
                <td><input type="date" name="new_details[${sid}][${tmp}][tanggal_awal_minggu]" class="form-control form-control-sm"></td>
                <td><input type="date" name="new_details[${sid}][${tmp}][tanggal_akhir_minggu]" class="form-control form-control-sm"></td>
                <td class="bulan-cell">-</td>
                <td><input type="number" step="0.01" name="new_details[${sid}][${tmp}][rencana]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="new_details[${sid}][${tmp}][realisasi]" class="form-control form-control-sm"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-new-detail">Hapus</button></td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });

    // Update Bulan Otomatis
    document.addEventListener('input', e => {
        if (e.target.type === 'date') {
            const row = e.target.closest('tr');
            if (!row) return;
            const start = row.querySelector('input[name*="[tanggal_awal_minggu]"]')?.value;
            const end = row.querySelector('input[name*="[tanggal_akhir_minggu]"]')?.value;
            const cell = row.querySelector('.bulan-cell');
            if (start) {
                const bulanAwal = new Date(start).toLocaleString('id-ID', { month: 'long' });
                const bulanAkhir = end ? new Date(end).toLocaleString('id-ID', { month: 'long' }) : bulanAwal;
                cell.textContent = bulanAwal === bulanAkhir ? bulanAwal : `${bulanAwal} - ${bulanAkhir}`;
            } else {
                cell.textContent = '-';
            }
        }
    });

    // Handler hapus lama
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove-progress')) {
            const id = e.target.dataset.progressId;
            pushDelete('delete_progress[]', id);
            e.target.closest('.progress-group').remove();
        }
        if (e.target.classList.contains('remove-sub')) {
            const id = e.target.dataset.subId;
            pushDelete('delete_subs[]', id);
            e.target.closest('.sub-group').remove();
        }
        if (e.target.classList.contains('remove-detail')) {
            const id = e.target.dataset.detailId;
            pushDelete('delete_details[]', id);
            e.target.closest('tr').remove();
        }
    });

    // Handler hapus baru
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove-new-progress')) {
            e.target.closest('.progress-group').remove();
        }
        if (e.target.classList.contains('remove-new-detail')) {
            e.target.closest('tr').remove();
        }
    });
})();
