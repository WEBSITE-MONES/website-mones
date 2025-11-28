@extends('Dashboard.base')

@section('title', 'Pengaturan Approval')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
/* -- Style UI/UX Tambahan -- */
:root {
    --primary-rgb: 102, 126, 234;
    --primary-light: rgba(102, 126, 234, 0.1);
}

.card {
    border: none;
    border-radius: 0.75rem;
    /* Lebih rounded */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.btn-action {
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.badge-urutan {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 600;
}

.signature-preview {
    max-width: 200px;
    max-height: 100px;
    object-fit: contain;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 5px;
    background: #f9f9f9;
}

.qr-code-preview {
    max-width: 100px;
    max-height: 100px;
    border: 1px solid #007bff;
    border-radius: 8px;
    padding: 5px;
}

.badge-verify {
    font-size: 0.75rem;
    text-decoration: none;
    padding: 0.3em 0.6em;
    font-weight: 500;
}

.signature-type-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* -- Style Pilihan Tipe Signature di Modal -- */
.signature-type-selector {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
}

.signature-type-card {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    position: relative;
}

.signature-type-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.signature-type-card.active {
    border-color: #667eea;
    background: var(--primary-light);
}

.signature-type-card.active::after {
    content: '✔';
    position: absolute;
    top: 8px;
    right: 8px;
    color: #667eea;
    font-weight: bold;
}

.signature-type-card input[type="radio"] {
    display: none;
}

.signature-type-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

/* -- Style untuk Grup Input yang Dinonaktifkan -- */
.file-upload-group {
    transition: all 0.3s ease;
    padding: 1rem;
    border-radius: 8px;
}

.file-upload-group.input-disabled {
    background-color: #f8f9fa;
    opacity: 0.7;
}

.file-upload-group.input-disabled label {
    color: #6c757d;
}

.file-upload-note {
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state i {
    font-size: 4rem;
    color: #e0e0e0;
}

/* Pembatas/Legend di Modal */
.modal-fieldset {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
}

.modal-legend {
    font-size: 1rem;
    font-weight: 600;
    padding: 0 0.5rem;
    width: auto;
    margin-bottom: 0.5rem;
    color: var(--bs-primary);
}
</style>
@endpush

@section('content')
<div class="page-inner">
    {{-- HEADER --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="fas fa-pen-signature text-primary me-2"></i>
                    Pengaturan Approval
                </h3>
                <p class="text-muted mb-0">Kelola alur persetujuan, approver, dan tanda tangan digital.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4 btn-action" data-bs-toggle="modal"
                    data-bs-target="#modalTambah">
                    <i class="fa fa-plus"></i> Tambah Approver
                </button>
            </div>
        </div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- INFO CARD --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-1 text-center">
                    <i class="fas fa-shield-alt fa-3x text-primary"></i>
                </div>
                <div class="col-md-11">
                    <h5 class="mb-2 fw-bold text-primary">Informasi: Tipe Tanda Tangan Digital</h5>
                    <ul class="mb-0 small text-muted" style="list-style-type: disc; padding-left: 1.2rem;">
                        <li><strong>Manual</strong>: Anda meng-upload gambar tanda tangan (PNG/JPG).</li>
                        <li><strong>QR Code</strong>: Sistem men-generate QR Code unik untuk verifikasi digital.</li>
                        <li><strong>Hybrid</strong>: Menggabungkan gambar Tanda Tangan Manual (yang Anda upload) dengan
                            QR Code.</li>
                        <li><strong>Verifikasi</strong>: Scan QR pada laporan untuk memvalidasi keaslian approver secara
                            real-time.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 rounded-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Daftar Approver ({{ $approvalSettings->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 200px;">Action</th>
                            <th class="text-center" style="width: 80px;">Urutan</th>
                            <th>Nama Approver</th>
                            <th>Role</th>
                            <th>Jabatan</th>
                            <th class="text-center">Signature Type</th>
                            <th class="text-center">Tanda Tangan</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvalSettings as $setting)
                        <tr>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle p-0" type="button"
                                        data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <h6 class="dropdown-header text-uppercase small">Menu Aksi</h6>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $setting->id }}">
                                                <i class="fa fa-edit text-warning me-2"></i> Edit Approver
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form
                                                action="{{ route('laporan.approval-settings.destroy', $setting->id) }}"
                                                method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-trash me-2"></i> Hapus Approver
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-circle badge-urutan" data-bs-toggle="tooltip"
                                    title="Urutan ke-{{ $setting->urutan }}">
                                    {{ $setting->urutan }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong class="d-block">{{ $setting->nama_approver }}</strong>
                                    <small class="text-muted" data-bs-toggle="tooltip" title="Akun User">
                                        <i class="fas fa-user-circle me-1"></i>
                                        {{ $setting->user->name }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">
                                    {{ $setting->role_label }}
                                </span>
                            </td>
                            <td>{{ $setting->jabatan ?? '-' }}</td>
                            <td class="text-center">
                                @if($setting->signature_type === 'qr')
                                <span class="badge bg-success signature-type-badge">
                                    <i class="fas fa-qrcode"></i> QR Code
                                </span>
                                @elseif($setting->signature_type === 'hybrid')
                                <span class="badge bg-primary signature-type-badge">
                                    <i class="fas fa-stamp"></i> Hybrid
                                </span>
                                @else
                                <span class="badge bg-primary signature-type-badge">
                                    <i class="fas fa-pen"></i> Manual
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($setting->signature_type === 'qr' && $setting->qr_code_path)
                                <div class="digital-signature-indicator">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#previewModal{{ $setting->id }}">
                                        <i class="fas fa-qrcode"></i> Lihat QR
                                    </button>
                                    @if($setting->signature_id)
                                    <a href="{{ route('verify.signature', $setting->signature_id) }}" target="_blank"
                                        class="badge bg-success text-white badge-verify mt-1 d-inline-block">
                                        <i class="fas fa-check-circle"></i> Verify
                                    </a>
                                    @endif
                                </div>

                                @elseif($setting->signature_type === 'hybrid' && $setting->qr_code_path)
                                <div class="digital-signature-indicator">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#previewModal{{ $setting->id }}">
                                        <i class="fas fa-stamp"></i> Lihat Hybrid
                                    </button>
                                    @if($setting->signature_id)
                                    <a href="{{ route('verify.signature', $setting->signature_id) }}" target="_blank"
                                        class="badge bg-success text-white badge-verify mt-1 d-inline-block">
                                        <i class="fas fa-shield-alt"></i> Verify
                                    </a>
                                    @endif
                                </div>

                                @elseif($setting->tanda_tangan)
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#previewModal{{ $setting->id }}">
                                    <i class="fas fa-pen"></i> Lihat TTD
                                </button>

                                @else
                                <span class="text-muted">
                                    <i class="fas fa-image-slash"></i> Belum upload
                                </span>
                                @endif
                            </td>

                            {{-- Modal Preview (tambahkan di dalam loop, setelah td) --}}
                            <div class="modal fade" id="previewModal{{ $setting->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-image me-2"></i>
                                                Preview Tanda Tangan - {{ $setting->nama_approver }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            @if($setting->signature_type === 'qr' && $setting->qr_code_path)
                                            <img src="{{ Storage::url($setting->qr_code_path) }}" alt="QR Code"
                                                class="img-fluid" style="max-height: 400px;">

                                            @elseif($setting->signature_type === 'hybrid' && $setting->qr_code_path)
                                            <img src="{{ Storage::url($setting->qr_code_path) }}" alt="Hybrid Signature"
                                                class="img-fluid" style="max-height: 400px;">

                                            @elseif($setting->tanda_tangan)
                                            <img src="{{ Storage::url($setting->tanda_tangan) }}" alt="Tanda Tangan"
                                                class="img-fluid" style="max-height: 400px;">
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                                <i class="fas fa-times"></i> Tutup
                                            </button>
                                            @if($setting->signature_type !== 'qr')
                                            <a href="{{ Storage::url($setting->tanda_tangan ?? $setting->qr_code_path) }}"
                                                target="_blank" class="btn btn-primary">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input toggle-active" type="checkbox"
                                        data-id="{{ $setting->id }}" id="toggle{{ $setting->id }}"
                                        {{ $setting->is_active ? 'checked' : '' }} style="font-size: 1.25rem;">
                                    <label class="form-check-label ms-2" for="toggle{{ $setting->id }}">
                                        <span class="badge {{ $setting->is_active ? 'bg-success' : 'bg-primary' }}">
                                            {{ $setting->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </div>
                            </td>


                        </tr>
                        @include('Dashboard.Pekerjaan.Realisasi.Laporan.partials.modal_edit', ['setting' =>
                        $setting])

                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <h5 class="text-muted mt-3">Belum Ada Approver</h5>
                                    <p class="text-muted">Belum ada approver yang dikonfigurasi untuk laporan.</p>
                                    <button type="button" class="btn btn-primary mt-2 rounded-pill px-4"
                                        data-bs-toggle="modal" data-bs-target="#modalTambah">
                                        <i class="fa fa-plus"></i> Tambah Approver Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('Dashboard.Pekerjaan.Realisasi.Laporan.partials.modal_tambah')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ===== APPROVAL SETTINGS SCRIPT (REVISED UI/UX) =====
$(document).ready(function() {
    console.log('Approval Settings JS initialized - REVISED UI/UX');

    // Inisialisasi Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // ===== SIGNATURE TYPE SELECTOR (KARTU) =====
    $(document).on('click', '.signature-type-card', function() {
        const $parent = $(this).closest('.signature-type-selector');
        $parent.find('.signature-type-card').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });

    // ===== LOGIKA UTAMA: VALIDASI TIPE SIGNATURE (REVISED) =====
    function handleSignatureTypeChange() {
        const $radio = $(this);
        const selectedType = $radio.val();
        const $modal = $radio.closest('.modal');

        const $fileGroup = $modal.find('.file-upload-group');
        const $fileInput = $modal.find('.signature-file-input');
        const $fileNote = $modal.find('.file-upload-note');
        const $requiredIndicator = $modal.find('.required-indicator');

        // Cek mode Edit: (form punya input _method=PUT)
        const isEditMode = $modal.find('input[name="_method"][value="PUT"]').length > 0;

        console.log(`Type: ${selectedType}, EditMode: ${isEditMode}`);

        if (selectedType === 'qr') {
            // TIPE QR: Selalu nonaktifkan & tidak wajib
            $fileInput.prop('disabled', true).prop('required', false).val(''); // Hapus file jika ada
            $fileGroup.addClass('input-disabled');
            $requiredIndicator.hide();
            $fileNote.html(
                '<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Tidak perlu upload.</span><br><small>QR Code akan di-generate otomatis.</small>'
            );

            $modal.find('.image-preview-container').hide();
            $modal.find('.signature-preview-current').hide();

        } else {
            // TIPE MANUAL atau HYBRID: Aktifkan
            $fileInput.prop('disabled', false);
            $fileGroup.removeClass('input-disabled');

            // Tampilkan preview
            $modal.find('.image-preview-container').show();
            $modal.find('.signature-preview-current').show();

            if (isEditMode) {
                // Mode Edit: Opsional
                $fileInput.prop('required', false);
                $requiredIndicator.hide();
                $fileNote.html(
                    '<span class="text-info fw-bold"><i class="fas fa-info-circle"></i> Opsional.</span><br><small>Kosongkan jika tidak ingin mengubah tanda tangan.</small>'
                );
            } else {
                $fileInput.prop('required', true);
                $requiredIndicator.show();
                $fileNote.html(
                    `<span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle"></i> Wajib diupload.</span><br><small>Dibutuhkan untuk tipe ${selectedType === 'hybrid' ? 'Hybrid' : 'Manual'}.</small>`
                );
            }
        }
    }

    // Pasang listener
    $(document).on('change', 'input[name="signature_type"]', handleSignatureTypeChange);
    $(document).on('change', '.signature-file-input', function(e) {
        const file = e.target.files[0];
        const $modal = $(this).closest('.modal');
        const $previewContainer = $modal.find('.image-preview-container');
        const $previewImg = $modal.find('.preview-img');

        console.log('File selected:', file ? file.name : 'none');

        if (file) {
            const maxSize = 2 * 1024 * 1024;
            const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];

            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Salah',
                    text: 'Hanya file PNG, JPG, dan JPEG yang diperbolehkan',
                });
                $(this).val('');
                return;
            }

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB',
                });
                $(this).val('');
                return;
            }

            // Tampilkan preview
            const reader = new FileReader();
            reader.onload = function(event) {
                $previewImg.attr('src', event.target.result);
                $previewContainer.show();
                console.log('Preview loaded successfully');
            };
            reader.readAsDataURL(file);
        } else {
            $previewContainer.hide();
        }
    });

    $('.toggle-active').on('change', function() {
        const id = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const $label = $(this).next('label').find('.badge');
        const $checkbox = $(this);

        console.log('Toggle active for ID:', id, 'Status:', isChecked);

        $.ajax({
            url: `{{ url('dashboard/laporan/approval-settings') }}/${id}/toggle-active`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Toggle response:', response);
                if (response.success) {
                    $label.removeClass('bg-secondary bg-success')
                        .addClass(response.is_active ? 'bg-success' : 'bg-secondary')
                        .text(response.is_active ? 'Aktif' : 'Nonaktif');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function(xhr) {
                console.error('Toggle error:', xhr);
                $checkbox.prop('checked', !isChecked);

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message ||
                        'Terjadi kesalahan saat mengubah status',
                });
            }
        });
    });

    // ===== CONFIRM DELETE =====
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Anda yakin ingin menghapus approver ini? Digital signature terkait (jika ada) juga akan di-revoke.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
            cancelButtonText: '<i class="fa fa-times"></i> Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('form[id^="formTambahApproval"], form[id^="formEditApproval"]').on('submit', function(e) {
        const $form = $(this);
        const signatureType = $form.find('input[name="signature_type"]:checked').val();
        const hasFile = $form.find('.signature-file-input')[0].files.length > 0;
        const isEditMode = $form.find('input[name="_method"][value="PUT"]').length > 0;

        console.log('Form submitting - Type:', signatureType, 'Has File:', hasFile, 'Edit Mode:',
            isEditMode);


        if (!isEditMode && (signatureType === 'hybrid' || signatureType === 'manual') && !hasFile) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'File Tanda Tangan Diperlukan',
                text: `Untuk tipe ${signatureType === 'hybrid' ? 'Hybrid' : 'Manual'}, Anda harus meng-upload file gambar tanda tangan.`,
                confirmButtonText: 'OK'
            });
            return false;
        }

        console.log('Form validation passed, submitting to server...');
        $form.find('button[type="submit"]').prop('disabled', true).html(
            '<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
    });

    // ===== TRIGGER HANDLER SAAT MODAL DIBUKA =====
    $('.modal').on('show.bs.modal', function() {
        console.log('Modal opened, triggering signature type check...');
        const $checkedRadio = $(this).find('input[name="signature_type"]:checked');
        if ($checkedRadio.length > 0) {
            $checkedRadio.trigger('change');
        }

        $(this).find('button[type="submit"]').prop('disabled', false).html(
            '<i class="fa fa-save"></i> Simpan');
    });

    $('input[name="signature_type"]:checked').each(handleSignatureTypeChange);

    console.log('All event handlers initialized successfully (REVISED)');
});
</script>
@endpush