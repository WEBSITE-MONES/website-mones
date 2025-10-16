@extends('Dashboard.base')

@section('title', 'Kontrak')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Daftar Kontrak</h4>
                    @if(auth()->user()->role === 'superadmin' || auth()->user()->role === 'user')
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#modalKontrak">
                        <i class="fa fa-plus"></i> Tambah Kontrak
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="kontrakTable" class="display table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;">Action</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kontraks as $kontrak)
                                <tr>
                                    <td class="text-center">
                                        <div class="dropdown dropstart">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="aksiDropdown{{ $kontrak->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="aksiDropdown{{ $kontrak->id }}">
                                                <li>
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditKontrak{{ $kontrak->id }}">
                                                        <i class="fa fa-edit text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('pekerjaan.data.kontrak.destroy', [$pekerjaan->id, $kontrak->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-times"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>{{ $kontrak->keterangan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($kontrak->tanggal_kontrak)->format('d-m-Y') }}</td>
                                    <td>
                                        @if($kontrak->file_path)
                                        <a href="{{ asset('storage/' . $kontrak->file_path) }}"
                                            class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-file"></i> Lihat
                                        </a>
                                        @else
                                        <span class="text-muted">Tidak ada file</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal Edit Kontrak --}}
                                <div class="modal fade" id="modalEditKontrak{{ $kontrak->id }}" tabindex="-1"
                                    aria-labelledby="modalEditKontrakLabel{{ $kontrak->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEditKontrakLabel{{ $kontrak->id }}">
                                                    Edit Kontrak</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form
                                                action="{{ route('pekerjaan.data.kontrak.update', [$pekerjaan->id, $kontrak->id]) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="keterangan" class="form-label">Keterangan</label>
                                                        <select name="keterangan" class="form-select" required>
                                                            <option value="">-- Pilih Keterangan --</option>
                                                            <option value="Kontrak Pekerjaan"
                                                                {{ $kontrak->keterangan == 'Kontrak Pekerjaan' ? 'selected' : '' }}>
                                                                Kontrak Pekerjaan</option>
                                                            <option value="RAB"
                                                                {{ $kontrak->keterangan == 'RAB' ? 'selected' : '' }}>
                                                                RAB</option>
                                                            <option value="RKS"
                                                                {{ $kontrak->keterangan == 'RKS' ? 'selected' : '' }}>
                                                                RKS</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="tanggal_kontrak" class="form-label">Tanggal
                                                            Kontrak</label>
                                                        <input type="date" name="tanggal_kontrak" class="form-control"
                                                            value="{{ $kontrak->tanggal_kontrak }}" required>
                                                    </div>

                                                    @if($kontrak->file_path)
                                                    <div class="mb-3">
                                                        <label class="form-label">File Sebelumnya</label><br>
                                                        <a href="{{ asset('storage/' . $kontrak->file_path) }}"
                                                            target="_blank" class="btn btn-outline-info btn-sm">
                                                            <i class="fa fa-file"></i> Lihat File Lama
                                                        </a>
                                                    </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label for="file_kontrak" class="form-label">Upload File Baru
                                                            (Opsional)</label>
                                                        <input type="file" name="file_kontrak" class="form-control"
                                                            accept=".pdf,.doc,.docx,.jpg,.png">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save me-1"></i> Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Kontrak --}}
<div class="modal fade" id="modalKontrak" tabindex="-1" aria-labelledby="modalKontrakLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKontrakLabel">Tambah Kontrak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pekerjaan.data.kontrak.store', $pekerjaan->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <select name="keterangan" id="keterangan" class="form-select" required>
                            <option value="">-- Pilih Keterangan --</option>
                            <option value="Kontrak Pekerjaan">Kontrak Pekerjaan</option>
                            <option value="RAB">RAB</option>
                            <option value="RKS">RKS</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kontrak" class="form-label">Tanggal Kontrak</label>
                        <input type="date" id="tanggal_kontrak" name="tanggal_kontrak" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="file_kontrak" class="form-label">Upload File Kontrak</label>
                        <input type="file" id="file_kontrak" name="file_kontrak" class="form-control"
                            accept=".pdf,.doc,.docx,.jpg,.png">
                        <small class="text-muted">Format: PDF, Word, JPG, PNG</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#kontrakTable').DataTable({
    pageLength: 10,
    responsive: true,
    language: {
        paginate: {
            previous: "Sebelumnya",
            next: "Berikutnya"
        },
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        search: "_INPUT_",
        searchPlaceholder: "Cari...",
        lengthMenu: "Tampilkan _MENU_ data"
    }
});
</script>
@endpush
@endsection