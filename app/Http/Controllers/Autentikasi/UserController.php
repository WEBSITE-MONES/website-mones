<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function create()
{
    return view('Autentikasi.create');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|email|unique:users',
        'role'     => 'required|in:superadmin,admin,user',
        'password' => 'required|string|min:6'
    ]);

    \App\Models\User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'role' => $request->role,
        'password' => bcrypt($request->password),
    ]);

    return redirect()->route('dashboard.user')->with('success', 'User berhasil dibuat!');
}

public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('Autentikasi.update', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'role'     => 'required|in:superadmin,admin,user',
            'password' => 'nullable|string|min:6'
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('dashboard.user')->with('success', 'User berhasil diperbarui!');
    }
    public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('dashboard.user')->with('success', 'User berhasil dihapus!');
}


}