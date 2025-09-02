@extends('Dashboard.base')

@section('title', 'Daftar Rencana Kerja')

@section('content')
<div class="page-inner">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h4 class="page-title">Daftar Semua Rencana Kerja {{ request('tahun') ?? date('Y') }}</h4>

        {{-- Search & Filter Tahun --}}
        <form method="GET" action="{{ route('pekerjaan.index') }}" class="d-flex">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2"
                placeholder="Pencarian..">

            <select name="tahun" class="form-control me-2">
                <option value="">-- Semua Tahun --</option>
                @foreach($tahunList as $thn)
                <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>
                    {{ $thn }}
                </option>
                @endforeach
            </select>

            <button class="btn btn-primary" type="submit">Filter</button>
        </form>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Rencana Kerja</h4>
                    @if(auth()->user()->role === 'superadmin')
                    <a href="{{ route('pekerjaan.create') }}" class="btn btn-primary btn-round ms-auto">
                        <i class="fa fa-plus"></i> Input Rencana Kerja
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="add-row" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Wilayah</th>
                                    <th>Nama Pekerjaan</th>
                                    <th>Status</th>
                                    <th>Kebutuhan Dana</th>
                                    <th>Tahun</th>
                                    <th>Tanggal</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pekerjaans as $pekerjaan)
                                <tr>
                                    <td>{{ $pekerjaan->wilayah->nama }}</td>
                                    <td>{{ $pekerjaan->nama_pekerjaan }}</td>
                                    <td>{{ $pekerjaan->status }}</td>
                                    <td>Rp {{ number_format($pekerjaan->kebutuhan_dana, 0, ',', '.') }}</td>
                                    <td>{{ $pekerjaan->tahun }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pekerjaan->tanggal)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="aksiDropdown{{ $pekerjaan->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu"
                                                aria-labelledby="aksiDropdown{{ $pekerjaan->id }}">
                                                <li>
                                                    <a href="{{ route('pekerjaan.show', $pekerjaan->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-eye me-2"></i> Lihat
                                                    </a>
                                                </li>

                                                @if(auth()->user()->role === 'superadmin')
                                                <li>
                                                    <a href="{{ route('pekerjaan.edit', $pekerjaan->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-edit me-2"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('pekerjaan.destroy', $pekerjaan->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Hapus pekerjaan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-times me-2"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $pekerjaans->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection