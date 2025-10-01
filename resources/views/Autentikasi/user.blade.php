@extends('Dashboard.base')

@section('title', 'Data User')

@section('content')
<div class="page-inner">
    {{-- Header Halaman Ditingkatkan --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <ul class="breadcrumbs" style="font-size: 1.1rem; font-weight: 500;">
            <li class="nav-home">
                {{-- Arahkan ke route dashboard utama Anda --}}
                <a href="{{ route('dashboard.index') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                {{-- Arahkan ke halaman daftar rencana kerja/wilayah --}}
                <a href="{{ route('dashboard.user') }}">Data Pengguna</a>
            </li>
        </ul>
        {{-- Tombol Tambah di Header untuk akses cepat --}}
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah User Baru
        </a>
    </div>

    ---

    <div class="row">
        <div class="col-md-12">
            {{-- Kartu Utama Data --}}
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-light border-bottom d-flex align-items-center p-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark">
                        Daftar Pengguna Sistem
                    </h5>
                    {{-- Tombol Tambah bisa juga diletakkan di sini jika design mengharuskan (opsional)--}}
                </div>

                {{-- Alert Sukses (Ditingkatkan) --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="card-body p-3">
                    <div class="table-responsive">
                        {{-- ID 'add-row' dipertahankan untuk kompatibilitas JS DataTables --}}
                        <table id="add-row" class="display table table-striped table-hover table-bordered align-middle">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center" style="width: 5%">#</th>
                                    {{-- Nomor urut ditambahkan untuk UX --}}
                                    <th>Nama Lengkap</th>
                                    <th>Username/Email</th>
                                    <th style="width: 15%">Role Akses</th>
                                    <th class="text-center" style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $user->username }}</span>
                                    </td>
                                    <td>
                                        {{-- Tampilan Role dengan badge untuk visualisasi yang lebih baik --}}
                                        <span
                                            class="badge {{ $user->role == 'Admin' ? 'bg-danger' : 'bg-info' }} fw-bold">
                                            {{ strtoupper($user->role) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{-- Grup Tombol Aksi --}}
                                        <div class="btn-group" role="group" aria-label="Aksi User">
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Tombol Delete --}}
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                onsubmit="return confirm('ANDA YAKIN INGIN MENGHAPUS USER {{ $user->username }}? Tindakan ini tidak bisa dibatalkan.')"
                                                class="d-inline ms-1"> {{-- d-inline agar form sejajar --}}
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Data">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
$(document).ready(function() {
    $('#add-row').DataTable({
        pageLength: 10,
        responsive: true,
        language: {
            paginate: {
                previous: "Sebelumnya",
                next: "Berikutnya"
            },
            search: "_INPUT_",
            searchPlaceholder: "Pencarian..",
            lengthMenu: "Tampilkan _MENU_ data"
        }
    });
});
</script>
@endpush

@endsection