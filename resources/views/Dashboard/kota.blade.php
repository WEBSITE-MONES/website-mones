@extends('Dashboard.base')

@section('title', 'Ambon')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Kota Ambon</h4>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Data Pekerjaan Di Kota Ambon</h4>
                    <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal"
                        data-bs-target="#addRowModal">
                        <i class="fa fa-plus"></i> Input Rencana Kerja
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="add-row" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Pekerjaan</th>
                                    <th>Status Pekerjaan</th>
                                    <th>Nilai Pekerjaan</th>
                                    <th>Tahun</th>
                                    <th>Tanggal Pekerjaan</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pekerjaan Pembangunan Terminal Penumpang Pelabuhan Ambon</td>
                                    <td>Sedang Berjalan</td>
                                    <td>Rp 10.000.000.000</td>
                                    <td>2025</td>
                                    <td>28-08-2025</td>
                                    <td>
                                        <button class="btn btn-link btn-primary btn-sm"><i
                                                class="fa fa-edit"></i></button>
                                        <button class="btn btn-link btn-danger btn-sm"><i
                                                class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pekerjaan Renovasi Dermaga Ambon</td>
                                    <td>Perencanaan</td>
                                    <td>Rp 2.500.000.000</td>
                                    <td>2025</td>
                                    <td>15-09-2025</td>
                                    <td>
                                        <button class="btn btn-link btn-primary btn-sm"><i
                                                class="fa fa-edit"></i></button>
                                        <button class="btn btn-link btn-danger btn-sm"><i
                                                class="fa fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection