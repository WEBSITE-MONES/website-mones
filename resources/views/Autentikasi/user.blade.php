@extends('Dashboard.base')

@section('title', 'Data User')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Data User</h4>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Data User</h4>
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm ms-auto">
                        <i class="fa fa-plus"></i> Tambah User
                    </a>
                </div>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="add-row" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th style="width: 15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td class="d-flex">
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="btn btn-sm btn-primary me-1">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        {{-- Tombol Delete --}}
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin mau hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
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