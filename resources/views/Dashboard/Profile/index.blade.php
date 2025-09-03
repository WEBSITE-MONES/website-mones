@extends('Dashboard.base')

@section('title', 'Profil Saya')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-8 order-last order-md-first">
            <div class="card">
                <div class="card-header">
                    <div class="text-center">
                        <h3 class="card-title">Profil</h3>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table nowrap mb-0">
                            <tbody>
                                <tr>
                                    <td width="10%">Nama</td>
                                    <td width="5%">:</td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>:</td>
                                    <td>{{ $profile->jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle; white-space: nowrap;">Tanggal Lahir</td>
                                    <td>:</td>
                                    <td>
                                        {{ $profile && $profile->tanggal_lahir 
                                            ? \Carbon\Carbon::parse($profile->tanggal_lahir)->format('d M Y') 
                                            : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Agama</td>
                                    <td>:</td>
                                    <td>{{ $profile->agama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: middle; white-space: nowrap;">Jenis Kelamin</td>
                                    <td>:</td>
                                    <td>
                                        @if($profile && $profile->jenis_kelamin === 'L')
                                        Laki-laki
                                        @elseif($profile && $profile->jenis_kelamin === 'P')
                                        Perempuan
                                        @else
                                        -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>No. Telp</td>
                                    <td>:</td>
                                    <td>{{ $profile->nomor_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>:</td>
                                    <td>{{ $profile->alamat ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Foto & Aksi --}}
        <div class="col-md-4 order-first order-md-last">
            <div class="card card-profile">
                <div class="card-header">
                    <div class=" profile-picture">
                        <div class="avatar avatar-xl">
                            <img src="{{ asset("assets/img/kaiadmin/user.png") }}" alt="User profile picture"
                                class="avatar-img rounded-circle">
                        </div>
                    </div>
                </div>
                <div class="card-body pb-3">
                    <div class="user-profile text-center">
                        <div class="name">{{ $user->name }}</div>
                        <div class="job">{{ $profile->jabatan ?? '-' }}</div>
                    </div>
                </div>
                <div class="card-footer p-0">
                    <table class="table table-hover nowrap mb-0">
                        <tr>
                            <td>
                                <a href="{{ route('account.edit') }}">
                                    <i class="icon-pencil mr-2 text-warning"></i> Edit Profile
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="{{ route('account.setting') }}">
                                    <i class="icon-settings mr-2"></i> Account Setting
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection