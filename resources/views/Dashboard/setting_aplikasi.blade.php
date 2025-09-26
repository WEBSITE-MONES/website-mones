@extends('Dashboard.base')

@section('title', 'Setting Aplikasi')

@section('content')
<div class="page-inner">
    <!-- <h4 class="page-title">Pengaturan Judul Aplikasi <span id="notifikasi"></span></h4> -->

    <div class="page-header">
        <ul class="breadcrumbs" style="font-size: 1.1rem; font-weight: 500;">
            <li class="nav-setting">
                {{-- Arahkan ke route dashboard utama Anda --}}
                <a href="{{ route('setting_aplikasi.index') }}">
                    <i class="fas fa-cog"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="">Judul Aplikasi</a>
            </li>
        </ul>
    </div>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th style="width:30px; text-align: center">Aksi</th>
                            <th>Nama Aplikasi</th>
                            <th>Nama Perusahaan</th>
                            <th>Ucapan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;">
                                <!-- Tombol edit buka modal -->
                                <button class="btn btn-link p-0" data-bs-toggle="modal"
                                    data-bs-target="#editSettingModal" title="Edit Judul">
                                    <i class="icon icon-pencil"></i>
                                </button>
                            </td>
                            <td>{{ $setting->nama_aplikasi }}</td>
                            <td>{{ $setting->nama_perusahaan }}</td>
                            <td>{{ $setting->ucapan }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editSettingModal" tabindex="-1" aria-labelledby="editSettingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('setting_aplikasi.update', $setting->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editSettingModalLabel">Edit Setting Aplikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Aplikasi</label>
                        <input type="text" name="nama_aplikasi" class="form-control"
                            value="{{ old('nama_aplikasi', $setting->nama_aplikasi) }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" class="form-control"
                            value="{{ old('nama_perusahaan', $setting->nama_perusahaan) }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Ucapan</label>
                        <textarea name="ucapan" class="form-control">{{ old('ucapan', $setting->ucapan) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label>Logo Perusahaan</label>
                        <input type="file" name="logo" class="form-control">
                        @if($setting->logo)
                        <img src="{{ asset('img/mnp/'.$setting->logo) }}" width="100" class="mt-2">
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection