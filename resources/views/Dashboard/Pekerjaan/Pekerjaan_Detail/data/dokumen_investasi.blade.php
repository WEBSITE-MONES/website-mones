@extends('Dashboard.base')

@section('title', 'Dokumen Usulan Investasi')

@push('styles')
<style>
.action-btn {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 3px;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endpush

@section('content')
<div class="page-inner">

    {{-- HEADER --}}
    <div class="page-header">
        <h4 class="page-title">Dokumen Usulan Investasi</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="#"><i class="icon-home"></i></a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('pekerjaan.index') }}">Daftar Pekerjaan</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ route('pekerjaan.show', $pekerjaan->id) }}">Detail</a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Usulan Investasi</a></li>
        </ul>
        <div class="ms-auto">
            <a href="{{ route('pekerjaan.show', $pekerjaan->id) }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail
            </a>
        </div>
    </div>

    {{-- TABEL DOKUMEN --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">
                        Daftar Dokumen
                        <small class="text-muted d-block">Proyek: {{ $pekerjaan->nama_investasi }}</small>
                    </h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#uploadModal">
                        <i class="fa fa-plus"></i> Tambah Dokumen
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dokumenUsulanTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Kategori</th>
                                    <th>Nama Dokumen</th>
                                    <th>Tanggal Unggah</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dokumen as $doc)
                                <tr>
                                    <td>
                                        <div class="dropdown dropstart">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $doc->id }}">
                                                        <i class="fa fa-edit text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('pekerjaan.data.dokumen_investasi.destroy', [$pekerjaan->id, $doc->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin hapus dokumen ini?');">
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
                                    <td>{{ $doc->kategori }}</td>
                                    <td>{{ $doc->nama_dokumen }}</td>
                                    <td>{{ \Carbon\Carbon::parse($doc->tanggal)->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ asset('storage/dokumen/'.$doc->file_name) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="fa fa-file"></i> Lihat
                                        </a>
                                    </td>
                                </tr>

                                {{-- Modal Edit --}}
                                <div class="modal fade" id="editModal{{ $doc->id }}" tabindex="-1"
                                    aria-labelledby="editModalLabel{{ $doc->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Dokumen Usulan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form
                                                action="{{ route('pekerjaan.data.dokumen_investasi.update', [$pekerjaan->id, $doc->id]) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Dokumen</label>
                                                        <input type="text" class="form-control" name="nama_dokumen"
                                                            value="{{ $doc->nama_dokumen }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Kategori</label>
                                                        <select class="form-select" name="kategori" required>
                                                            <option value="Justifikasi"
                                                                {{ $doc->kategori == 'Justifikasi' ? 'selected' : '' }}>
                                                                Justifikasi</option>
                                                            <option value="Analisis Finansial"
                                                                {{ $doc->kategori == 'Analisis Finansial' ? 'selected' : '' }}>
                                                                Analisis Finansial</option>
                                                            <option value="Analisis Teknis"
                                                                {{ $doc->kategori == 'Analisis Teknis' ? 'selected' : '' }}>
                                                                Analisis Teknis</option>
                                                            <option value="Lampiran"
                                                                {{ $doc->kategori == 'Lampiran' ? 'selected' : '' }}>
                                                                Lampiran</option>
                                                            <option value="Lainnya"
                                                                {{ $doc->kategori == 'Lainnya' ? 'selected' : '' }}>
                                                                Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal</label>
                                                        <input type="date" name="tanggal" class="form-control"
                                                            value="{{ $doc->tanggal }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Simpan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada dokumen yang diunggah.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Tambah Dokumen Usulan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pekerjaan.data.dokumen_investasi.store', $pekerjaan->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="documentFile" class="form-label">Pilih File</label>
                        <input class="form-control" type="file" id="documentFile" name="file" required>
                    </div>
                    <div class="mb-3">
                        <label for="documentName" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="documentName" name="nama_dokumen"
                            placeholder="Contoh: Justifikasi Kebutuhan Proyek">
                    </div>
                    <div class="mb-3">
                        <label for="documentCategory" class="form-label">Kategori</label>
                        <select class="form-select" id="documentCategory" name="kategori" required>
                            <option selected disabled value="">Pilih Kategori...</option>
                            <option value="Justifikasi">Justifikasi</option>
                            <option value="Analisis Finansial">Analisis Finansial</option>
                            <option value="Analisis Teknis">Analisis Teknis</option>
                            <option value="Lampiran">Lampiran</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#dokumenUsulanTable').DataTable({
    pageLength: 10,
    responsive: true,
});
</script>
@endpush