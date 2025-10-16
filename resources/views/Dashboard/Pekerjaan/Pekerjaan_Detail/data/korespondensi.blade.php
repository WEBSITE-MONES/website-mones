@extends('Dashboard.base')

@section('title', 'Korespondensi')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Korespondensi</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#uploadKorespondensiModal">
                        <i class="fa fa-plus"></i> Tambah Korespondensi
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="korespondensiTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Jenis</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pekerjaan->korespondensis as $korespondensi)
                                <tr>
                                    <td>
                                        <div class="dropdown dropstart">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="aksiDropdown{{ $korespondensi->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu"
                                                aria-labelledby="aksiDropdown{{ $korespondensi->id }}">
                                                <li>
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#editKorespondensiModal{{ $korespondensi->id }}">
                                                        <i class="fa fa-edit text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('pekerjaan.data.korespondensi.destroy', ['id' => $pekerjaan->id, 'korespondensi' => $korespondensi->id]) }}"
                                                        method="POST" onsubmit="return confirm('Yakin ingin hapus?');">
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
                                    <td>{{ $korespondensi->jenis }}</td>
                                    <td>{{ $korespondensi->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($korespondensi->tanggal)->format('d-m-Y') }}</td>
                                    <td>
                                        @if($korespondensi->file_path)
                                        <a href="{{ asset('storage/' . $korespondensi->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="fa fa-file"></i> Lihat
                                        </a>
                                        @else
                                        <span class="text-muted">Tidak ada file</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Modal Edit --}}
                                <div class="modal fade" id="editKorespondensiModal{{ $korespondensi->id }}"
                                    tabindex="-1" aria-labelledby="editKorespondensiModalLabel{{ $korespondensi->id }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Korespondensi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('pekerjaan.data.korespondensi.update', ['id' => $pekerjaan->id, 'korespondensi' => $korespondensi->id]) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis Korespondensi</label>
                                                        <select class="form-select" name="jenis" required>
                                                            <option value="Persuratan"
                                                                {{ $korespondensi->jenis == 'Persuratan' ? 'selected' : '' }}>
                                                                Persuratan</option>
                                                            <option value="Berita Acara"
                                                                {{ $korespondensi->jenis == 'Berita Acara' ? 'selected' : '' }}>
                                                                Berita Acara</option>
                                                            <option value="Lainnya"
                                                                {{ $korespondensi->jenis == 'Lainnya' ? 'selected' : '' }}>
                                                                Lainnya</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Judul</label>
                                                        <input type="text" name="judul" class="form-control"
                                                            value="{{ $korespondensi->judul }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal</label>
                                                        <input type="date" name="tanggal" class="form-control"
                                                            value="{{ $korespondensi->tanggal }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Upload File (Opsional)</label>
                                                        <input type="file" name="file_korespondensi"
                                                            class="form-control">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> Simpan Perubahan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data korespondensi</td>
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

{{-- Modal Tambah --}}
<div class="modal fade" id="uploadKorespondensiModal" tabindex="-1" aria-labelledby="uploadKorespondensiModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadKorespondensiModalLabel">Upload Korespondensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pekerjaan.data.korespondensi.store', $pekerjaan->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Jenis Korespondensi</label>
                        <select class="form-select" name="jenis" required>
                            <option value="Persuratan">Persuratan</option>
                            <option value="Berita Acara">Berita Acara</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul dokumen"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <input type="file" name="file_korespondensi" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Upload
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#korespondensiTable').DataTable({
    pageLength: -1,
    responsive: true,
    language: {
        paginate: {
            previous: "Previous",
            next: "Next"
        },
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        search: "_INPUT_",
        searchPlaceholder: "Search...",
        lengthMenu: "Tampilkan _MENU_ data"
    }
});
</script>
@endpush
@endsection