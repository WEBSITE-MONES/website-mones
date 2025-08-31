@extends('Dashboard.base')

@section('title', 'Edit User')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit User</h4>
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
                    <h3 class="card-title">Form Edit User</h3>
                </div>

                {{-- PERBAIKAN: kasih parameter $user->id ke route --}}
                <form action="{{ route('users.update', $user->id) }}" method="POST" class="form-horizontal"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            {{-- Username --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control"
                                        value="{{ old('username', $user->username) }}" placeholder="Masukkan username"
                                        required>
                                </div>
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Email --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required>
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control" required>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="superadmin"
                                            {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin
                                        </option>
                                        <option value="admin"
                                            {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                            User</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Password (boleh kosong kalau tidak diganti) --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password <small class="text-muted">(kosongkan jika tidak
                                            diubah)</small></label>
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Masukkan password baru (opsional)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('dashboard.user') }}" class="btn btn-sm btn-danger">
                            <i class="fas fa-undo mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-check mr-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection