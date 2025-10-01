@extends('Dashboard.base')

@section('title', 'Edit GR')

@push('styles')
{{-- CSS Terpadu (sama dengan Create GR) --}}
<style>
.custom-file-upload {
    border: 2px dashed #0d6efd;
    border-radius: .5rem;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    background-color: #f8f9fa;
    transition: all .3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 150px;
}

.custom-file-upload:hover {
    background-color: #e9ecef;
    border-color: #0b5ed7;
}

.custom-file-upload .icon {
    font-size: 2.5rem;
    color: #0d6efd;
    margin-bottom: .5rem;
}

.custom-file-upload .file-text {
    display: block;
    font-weight: 600;
    color: #495057;
}

.custom-file-upload .file-name {
    display: block;
    font-size: .875rem;
    color: #0d6efd;
    font-weight: 500;
    margin-top: .5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.current-file-info {
    background-color: #eef7ff;
    border: 1px solid #cfe2ff;
    border-radius: .375rem;
    padding: .5rem 1rem;
    margin-bottom: .75rem;
    font-size: .9rem;
}

.current-file-info a {
    font-weight: 600;
    text-decoration: none;
}

.current-file-info a i {
    color: #0d6efd;
}
</style>
@endpush

@section('content')
<div class="page-inner py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">Edit Good Receipt (GR)</h2>
            <h5 class="fw-normal text-muted">Perbarui detail penerimaan barang/jasa dan lampirkan dokumen.</h5>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm d-none d-md-block">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('realisasi.updateGR', [$pr->id, $gr->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Blok Kiri --}}
            <div class="col-lg-8">
                <div class="card card-round shadow-sm border-0 mb-4">
                    <div class="card-body p-4">

                        {{-- Informasi Utama --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>Informasi Utama
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nomor_gr" class="form-label">Nomor GR</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" id="nomor_gr" name="nomor_gr"
                                        class="form-control @error('nomor_gr') is-invalid @enderror"
                                        value="{{ old('nomor_gr', $gr->nomor_gr) }}" required>
                                </div>
                                @error('nomor_gr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_gr" class="form-label">Tanggal GR</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" id="tanggal_gr" name="tanggal_gr"
                                        class="form-control @error('tanggal_gr') is-invalid @enderror"
                                        value="{{ old('tanggal_gr', $gr->tanggal_gr) }}" required>
                                </div>
                                @error('tanggal_gr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="termin_id" class="form-label">Pilih Termin Pembayaran</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                    <select id="termin_id" name="termin_id"
                                        class="form-select @error('termin_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Termin --</option>
                                        @foreach($termins as $t)
                                        <option value="{{ $t->id }}" data-nilai="{{ $t->nilai_pembayaran }}"
                                            {{ old('termin_id', $gr->termin_id) == $t->id ? 'selected' : '' }}>
                                            {{ $t->uraian }} — Rp {{ number_format($t->nilai_pembayaran,0,',','.') }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('termin_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nilai_gr_input" class="form-label">Nilai GR</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="nilai_gr_input" name="nilai_gr"
                                        class="form-control fw-bold @error('nilai_gr') is-invalid @enderror"
                                        value="{{ old('nilai_gr', $gr->nilai_gr) }}" required>
                                </div>
                                <small class="form-text text-muted">Otomatis dari termin, bisa diubah manual.</small>
                                @error('nilai_gr')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Lampiran --}}
                        <h5 class="card-title fw-bold mb-4 border-bottom pb-2">
                            <i class="fas fa-paperclip text-primary me-2"></i>Lampiran Dokumen (.pdf)
                        </h5>
                        <div class="row">
                            {{-- BA Pemeriksaan --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">BA Pemeriksaan Pekerjaan</label>
                                @if($gr->file_ba_pemeriksaan)
                                <div class="current-file-info mb-2">
                                    <a href="{{ asset('storage/'.$gr->file_ba_pemeriksaan) }}" target="_blank">
                                        <i class="fas fa-file-alt me-2"></i>{{ basename($gr->file_ba_pemeriksaan) }}
                                    </a>
                                </div>
                                @endif
                                <label for="ba_pemeriksaan" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file BAP...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="ba_pemeriksaan" name="ba_pemeriksaan" class="d-none file-input"
                                    accept=".pdf">
                                @error('ba_pemeriksaan')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- BA Serah Terima --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">BA Serah Terima Pekerjaan</label>
                                @if($gr->file_ba_serah_terima)
                                <div class="current-file-info mb-2">
                                    <a href="{{ asset('storage/'.$gr->file_ba_serah_terima) }}" target="_blank">
                                        <i class="fas fa-file-alt me-2"></i>{{ basename($gr->file_ba_serah_terima) }}
                                    </a>
                                </div>
                                @endif
                                <label for="ba_serah_terima" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file BAST...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="ba_serah_terima" name="ba_serah_terima" class="d-none file-input"
                                    accept=".pdf">
                                @error('ba_serah_terima')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- BA Pembayaran --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">BA Pembayaran</label>
                                @if($gr->file_ba_pembayaran)
                                <div class="current-file-info mb-2">
                                    <a href="{{ asset('storage/'.$gr->file_ba_pembayaran) }}" target="_blank">
                                        <i class="fas fa-file-alt me-2"></i>{{ basename($gr->file_ba_pembayaran) }}
                                    </a>
                                </div>
                                @endif
                                <label for="ba_pembayaran" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file BAPembayaran...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="ba_pembayaran" name="ba_pembayaran" class="d-none file-input"
                                    accept=".pdf">
                                @error('ba_pembayaran')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Laporan/Dokumentasi --}}
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Laporan / Dokumentasi</label>
                                @if($gr->file_laporan_dokumentasi)
                                <div class="current-file-info mb-2">
                                    <a href="{{ asset('storage/'.$gr->file_laporan_dokumentasi) }}" target="_blank">
                                        <i
                                            class="fas fa-file-alt me-2"></i>{{ basename($gr->file_laporan_dokumentasi) }}
                                    </a>
                                </div>
                                @endif
                                <label for="laporan_dokumentasi" class="custom-file-upload">
                                    <i class="fas fa-cloud-upload-alt icon"></i>
                                    <span class="file-text">Pilih file Laporan...</span>
                                    <span class="file-name text-success"></span>
                                </label>
                                <input type="file" id="laporan_dokumentasi" name="laporan_dokumentasi"
                                    class="d-none file-input" accept=".pdf">
                                @error('laporan_dokumentasi')
                                <div class="text-danger mt-2 small d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Blok Kanan --}}
            <div class="col-lg-4">
                <div class="card card-round shadow-sm border-0 mb-4 sticky-lg-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3">
                            <i class="fas fa-dollar-sign text-primary me-2"></i>Total Nilai
                        </h5>
                        <div class="text-center bg-light p-3 rounded mb-4">
                            <h6 class="text-muted mb-1">NILAI PEKERJAAN (GR)</h6>
                            <h3 class="fw-bold text-success mb-0" id="display_nilai_gr">
                                Rp {{ number_format(old('nilai_gr', $gr->nilai_gr),0,',','.') }}
                            </h3>
                        </div>

                        <input type="hidden" name="nilai_gr" id="nilai_gr_hidden"
                            value="{{ old('nilai_gr', (int)$gr->nilai_gr) }}">


                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Update GR
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const terminSelect = document.getElementById('termin_id');
    const nilaiInput = document.getElementById('nilai_gr_input');
    const nilaiHidden = document.getElementById('nilai_gr_hidden');
    const displayNilai = document.getElementById('display_nilai_gr');

    function formatToRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(number);
    }

    function cleanNumber(value) {
        if (!value) return 0;

        // Kalau langsung numeric (contoh 1000000 atau "1000000"), return langsung
        if (!isNaN(value) && String(value).indexOf('.') === -1) {
            return Number(value);
        }

        // Kalau ada titik/koma (misalnya "1.000.000" atau "1000000.00"), bersihkan
        return Number(String(value).replace(/[^\d]/g, '')) || 0;
    }


    function updateNilaiGR(rawValue) {
        nilaiInput.value = new Intl.NumberFormat('id-ID').format(rawValue);
        nilaiHidden.value = rawValue;
        displayNilai.textContent = formatToRupiah(rawValue);
    }

    terminSelect.addEventListener('change', function() {
        const sel = this.selectedOptions[0];
        const raw = sel ? cleanNumber(sel.dataset.nilai) : 0;
        updateNilaiGR(raw);
    });
    nilaiInput.addEventListener('input', function() {
        updateNilaiGR(cleanNumber(this.value));
    });

    document.querySelectorAll('.file-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            const label = this.previousElementSibling;
            if (label && label.classList.contains('custom-file-upload')) {
                label.querySelector('.file-name').textContent = fileName;
                label.querySelector('.file-text').textContent = fileName ?
                    'File siap diupload:' : 'Pilih file...';
            }
        });
    });

    // Init
    updateNilaiGR(cleanNumber(nilaiHidden.value));

});
</script>
@endpush