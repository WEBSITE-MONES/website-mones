@extends('Dashboard.base')

@section('title', 'Gambar')

@section('content')
<div class="page-inner">
    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card card-round">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Daftar Gambar</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#uploadModal">
                        <i class="fa fa-plus"></i> Tambah Gambar
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="gambarTable" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Keterangan</th>
                                    <th>File</th>
                                    <th>Tanggal Upload</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DED</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file-image"></i>
                                            Lihat</a></td>
                                    <td>06-09-2025</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-success"><i class="fa fa-check"></i>
                                            Approve</button>
                                        <button class="btn btn-sm btn-warning"><i class="fa fa-times"></i>
                                            Reject</button>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shop Drawing</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file-image"></i>
                                            Lihat</a></td>
                                    <td>05-09-2025</td>
                                    <td><span class="badge bg-success">Approved</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>As Built</td>
                                    <td><a href="#" class="btn btn-sm btn-info"><i class="fa fa-file-image"></i>
                                            Lihat</a></td>
                                    <td>04-09-2025</td>
                                    <td><span class="badge bg-danger">Rejected</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> {{-- card-body --}}
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload --}}
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload / Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Form Dummy --}}
                <form>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <select id="keterangan" class="form-select" required>
                            <option value="">-- Pilih Keterangan --</option>
                            <option value="DED">DED</option>
                            <option value="Shop Drawing">Shop Drawing</option>
                            <option value="As Built">As Built</option>
                        </select>
                    </div>

                    {{-- Pilihan Opsi --}}
                    <div class="mb-3">
                        <label for="opsiInput" class="form-label">Pilih Cara Input</label>
                        <select id="opsiInput" class="form-select" onchange="toggleInputOption()" required>
                            <option value="">-- Pilih Opsi --</option>
                            <option value="upload">Upload File</option>
                            <option value="kamera">Ambil Foto Kamera</option>
                        </select>
                    </div>

                    {{-- Upload File --}}
                    <div class="mb-3" id="uploadSection" style="display:none;">
                        <label class="form-label">Upload Gambar</label>
                        <input type="file" class="form-control" accept="image/*">
                    </div>

                    {{-- Kamera --}}
                    <div class="mb-3 text-center" id="cameraSection" style="display:none;">
                        <label class="form-label d-block">Ambil Foto dengan Kamera</label>
                        <video id="cameraStream" width="100%" autoplay playsinline
                            style="border:1px solid #ddd; border-radius:8px;"></video>
                        <canvas id="snapshot"
                            style="display:none; width:100%; border:1px solid #ddd; border-radius:8px; margin-top:10px;"></canvas>
                        <div class="mt-2">
                            <button type="button" class="btn btn-success btn-sm" onclick="takePhoto()">📸 Ambil
                                Foto</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="retakePhoto()">🔄
                                Ulangi</button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#gambarTable').DataTable({
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

// --- Toggle Opsi Upload / Kamera ---
function toggleInputOption() {
    let opsi = document.getElementById("opsiInput").value;
    let uploadSection = document.getElementById("uploadSection");
    let cameraSection = document.getElementById("cameraSection");

    if (opsi === "upload") {
        uploadSection.style.display = "block";
        cameraSection.style.display = "none";
    } else if (opsi === "kamera") {
        uploadSection.style.display = "none";
        cameraSection.style.display = "block";
    } else {
        uploadSection.style.display = "none";
        cameraSection.style.display = "none";
    }
}

// --- Kamera ---
let video = document.getElementById('cameraStream');
let canvas = document.getElementById('snapshot');
let context = canvas.getContext('2d');
let streamGlobal = null;

$('#uploadModal').on('shown.bs.modal', function() {
    let opsi = document.getElementById("opsiInput").value;
    if (opsi === "kamera") {
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                streamGlobal = stream;
                video.srcObject = stream;
                video.play();
            })
            .catch(err => {
                console.error("Gagal akses kamera:", err);
                alert("Tidak bisa mengakses kamera: " + err.message);
            });
    }
});

$('#uploadModal').on('hidden.bs.modal', function() {
    if (streamGlobal) {
        streamGlobal.getTracks().forEach(track => track.stop());
        streamGlobal = null;
    }
    video.srcObject = null;
    video.style.display = "block";
    canvas.style.display = "none";
});

function takePhoto() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.style.display = "block";
    video.style.display = "none";
}

function retakePhoto() {
    canvas.style.display = "none";
    video.style.display = "block";
}
</script>
@endpush
@endsection