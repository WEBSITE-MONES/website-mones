{{-- MODAL EDIT APPROVER (FIXED) --}}
<div class="modal fade" id="modalEdit{{ $setting->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('laporan.approval-settings.update', $setting->id) }}" method="POST"
                enctype="multipart/form-data" id="formEditApproval{{ $setting->id }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Approval Setting
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Signature Type Selector --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tipe Tanda Tangan <span class="text-danger">*</span></label>
                        <div class="signature-type-selector">
                            <label
                                class="signature-type-card {{ $setting->signature_type === 'manual' ? 'active' : '' }}">
                                <input type="radio" name="signature_type" value="manual"
                                    {{ $setting->signature_type === 'manual' ? 'checked' : '' }}>
                                <div class="signature-type-icon">✍️</div>
                                <strong>Manual</strong>
                                <p class="small text-muted mb-0">Upload tanda tangan</p>
                            </label>
                            <label class="signature-type-card {{ $setting->signature_type === 'qr' ? 'active' : '' }}">
                                <input type="radio" name="signature_type" value="qr"
                                    {{ $setting->signature_type === 'qr' ? 'checked' : '' }}>
                                <div class="signature-type-icon">📱</div>
                                <strong>QR Code</strong>
                                <p class="small text-muted mb-0">Generate QR verifikasi</p>
                            </label>
                            <label
                                class="signature-type-card {{ $setting->signature_type === 'hybrid' ? 'active' : '' }}">
                                <input type="radio" name="signature_type" value="hybrid"
                                    {{ $setting->signature_type === 'hybrid' ? 'checked' : '' }}>
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
                                @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ $setting->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                            <select name="role_approval" class="form-select" required>
                                <option value="manager_teknik"
                                    {{ $setting->role_approval == 'manager_teknik' ? 'selected' : '' }}>Manager Teknik
                                </option>
                                <option value="assisten_manager"
                                    {{ $setting->role_approval == 'assisten_manager' ? 'selected' : '' }}>Assisten
                                    Manager</option>
                                <option value="direktur" {{ $setting->role_approval == 'direktur' ? 'selected' : '' }}>
                                    Direktur</option>
                                <option value="general_manager"
                                    {{ $setting->role_approval == 'general_manager' ? 'selected' : '' }}>General Manager
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Approver <span class="text-danger">*</span></label>
                            <input type="text" name="nama_approver" class="form-control"
                                value="{{ $setting->nama_approver }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="{{ $setting->jabatan }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Urutan <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" class="form-control" value="{{ $setting->urutan }}"
                                min="1" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                Tanda Tangan <small class="text-muted">(PNG/JPG, max 2MB)</small>
                                <span class="text-danger required-indicator" style="display: none;">*</span>
                            </label>
                            <input type="file" name="tanda_tangan" class="form-control signature-file-input"
                                accept="image/png,image/jpg,image/jpeg">

                            {{-- Catatan requirement dinamis --}}
                            <div class="file-upload-note mt-1">
                                <small class="text-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Opsional</strong> - Kosongkan jika tidak ingin mengubah
                                </small>
                            </div>

                            @if($setting->tanda_tangan || $setting->qr_code_path)
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1">Signature saat ini:</small>
                                @if($setting->qr_code_path)
                                <img src="{{ Storage::url($setting->qr_code_path) }}" class="signature-preview">
                                @else
                                <img src="{{ Storage::url($setting->tanda_tangan) }}" class="signature-preview">
                                @endif
                            </div>
                            @endif

                            {{-- Preview image untuk upload baru --}}
                            <div id="image_preview_edit_{{ $setting->id }}" class="mt-2" style="display: none;">
                                <small class="text-muted d-block mb-1">Preview baru:</small>
                                <img src="" id="preview_img_edit_{{ $setting->id }}" class="signature-preview">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input"
                                    id="isActive{{ $setting->id }}" {{ $setting->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isActive{{ $setting->id }}">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>