@extends('Dashboard.base')

@section('title', $wilayah->nama)

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Kota {{ $wilayah->nama }}</h4>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Data Pekerjaan Di Kota {{ $wilayah->nama }}</h4>
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
                                    <th>Nama Pekerjaan</th>
                                    <th style="vertical-align: middle; white-space: nowrap;">Status Pekerjaan</th>
                                    <th style="vertical-align: middle; white-space: nowrap;">Kebutuhan Dana</th>
                                    <th>Tahun</th>
                                    <th>Tanggal Pekerjaan</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wilayah->pekerjaans as $pekerjaan)
                                <tr>
                                    <td>{{ $pekerjaan->nama_pekerjaan }}</td>
                                    <td>{{ $pekerjaan->status }}</td>
                                    <td style="vertical-align: middle; white-space: nowrap;">Rp
                                        {{ number_format($pekerjaan->kebutuhan_dana, 0, ',', '.') }}</td>
                                    <td>{{ $pekerjaan->tahun }}</td>
                                    <td style="vertical-align: middle; white-space: nowrap;">
                                        {{ \Carbon\Carbon::parse($pekerjaan->tanggal)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                {{-- Tombol Detail --}}
                                                <li>
                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#detailModal{{ $pekerjaan->id }}">
                                                        <i class="fa fa-eye text-info"></i> Detail
                                                    </button>
                                                </li>

                                                @if(auth()->user()->role === 'superadmin')
                                                {{-- Tombol Edit --}}
                                                <li>
                                                    <a href="{{ route('pekerjaan.edit', $pekerjaan->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-edit text-primary"></i> Edit
                                                    </a>
                                                </li>

                                                {{-- Tombol Hapus --}}
                                                <li>
                                                    <form action="{{ route('pekerjaan.destroy', $pekerjaan->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pekerjaan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-times"></i> Hapus
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection