{{-- MODAL TAMBAH APPROVER (COMPLETELY FIXED) --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('laporan.approval-settings.store') }}" method="POST" enctype="multipart/form-data"
                id="formTambahApproval">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Approval Setting
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Signature Type Selector --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tipe Tanda Tangan <span class="text-danger">*</span></label>
                        <div class="signature-type-selector">
                            <label class="signature-type-card active">
                                <input type="radio" name="signature_type" value="manual" checked>
                                <div class="signature-type-icon">✍️</div>
                                <strong>Manual</strong>
                                <p class="small text-muted mb-0">Upload tanda tangan</p>
                            </label>
                            <label class="signature-type-card">
                                <input type="radio" name="signature_type" value="qr">
                                <div class="signature-type-icon">📱</div>
                                <strong>QR Code</strong>
                                <p class="small text-muted mb-0">Generate QR verifikasi</p>
                            </label>
                            <label class="signature-type-card">
                                <input type="radio" name="signature_type" value="hybrid">
                                <div class="signature-type-icon">🔒</div>
                                <strong>Hybrid</strong>
                                <p class="small text-muted mb-0">TTD + QR Code</p>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">User <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Pilih User</option>
                                @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih user yang akan menjadi approver</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                            <select name="role_approval" class="form-select" required>
                                <option value="">Pilih Role</option>
                                <option value="manager_teknik">Manager Teknik</option>
                                <option value="assisten_manager">Assisten Manager</option>
                                <option value="direktur">Direktur</option>
                                <option value="general_manager">General Manager</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Approver <span class="text-danger">*</span></label>
                            <input type="text" name="nama_approver" class="form-control"
                                placeholder="BUDI SANTOSO, S.T." required>
                            <small class="text-muted">Nama lengkap dengan gelar (huruf kapital)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control"
                                placeholder="Manager Teknik Divisi Pemeliharaan">
                            <small class="text-muted">Jabatan lengkap (opsional)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Urutan <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" class="form-control" value="1" min="1" required>
                            <small class="text-muted">Urutan approval (1 = pertama, 2 = kedua, dst)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Tanda Tangan <small class="text-muted">(PNG/JPG, max 2MB)</small>
                                <span class="text-danger required-indicator">*</span>
                            </label>
                            <input type="file" name="tanda_tangan" class="form-control signature-file-input"
                                accept="image/png,image/jpg,image/jpeg" id="tanda_tangan_input" required>

                            {{-- Catatan requirement dinamis --}}
                            <div class="file-upload-note mt-1">
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Wajib</strong> untuk Manual
                                </small>
                            </div>

                            {{-- Preview image --}}
                            <div id="image_preview" class="mt-2" style="display: none;">
                                <img src="" id="preview_img" class="signature-preview">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActiveNew"
                                    checked>
                                <label class="form-check-label fw-bold" for="isActiveNew">Aktif</label>
                                <small class="text-muted d-block">Akan langsung digunakan untuk laporan baru</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitTambah">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>