@extends('Dashboard.base')

@section('title', 'Tambah User')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Tambah User</h4>
    </div>

    <div class="row">
        <div class="col-md-12">

            {{-- tampilkan error kalau validasi gagal --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header text-center">
                    <h3 class="card-title">Form Tambah User</h3>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="form-horizontal"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="col-md-12 mt-3">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle fa-x5"></i>
                                <small>
                                    <cite title="Source Title">
                                        Inputan yang ditanda bintang merah (<span class="text-danger">*</span>) harus
                                        diisi!
                                    </cite>
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Username --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control"
                                        value="{{ old('username') }}" placeholder="Masukkan username" required>
                                </div>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        placeholder="Masukkan nama lengkap" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Email --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        placeholder="Masukkan email" required>
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control" id="roleSelect" required>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="superadmin" {{ old('role')=='superadmin' ? 'selected' : '' }}>
                                            Super Admin</option>
                                        <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin
                                        </option>
                                        <option value="user" {{ old('role')=='user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Wilayah (hanya untuk role admin) --}}
                        <div class="row" id="wilayahField" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Wilayah <span class="text-danger">*</span></label>
                                    <select name="wilayah_id" class="form-control">
                                        <option value="">-- Pilih Wilayah --</option>
                                        @foreach($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id }}"
                                            {{ old('wilayah_id') == $wilayah->id ? 'selected' : '' }}>
                                            {{ $wilayah->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Password --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Masukkan password" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('dashboard.user') }}" class="btn btn-sm btn-danger">
                            <i class="fas fa-undo mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-check mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleWilayahField() {
    let role = document.getElementById('roleSelect').value;
    document.getElementById('wilayahField').style.display = (role === 'admin') ? 'flex' : 'none';
}
document.getElementById('roleSelect').addEventListener('change', toggleWilayahField);
window.onload = toggleWilayahField;
</script>
@endpush

@endsection