<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wilayah;

class UserController extends Controller
{
    /**
     * Form tambah user
     */
    public function create()
    {
        $wilayahs = Wilayah::all(); // ✅ kirim daftar wilayah
        return view('Autentikasi.create', compact('wilayahs'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'required|email|unique:users',
            'role'     => 'required|in:superadmin,admin,user',
            'wilayah_id' => 'nullable|exists:wilayah,id', // ✅ validasi wilayah
            'password' => 'required|string|min:6'
        ]);

        User::create([
            'name'       => $request->name,
            'username'   => $request->username,
            'email'      => $request->email,
            'role'       => $request->role,
            'wilayah_id' => $request->wilayah_id,
            'password'   => bcrypt($request->password),
        ]);

        return redirect()->route('dashboard.user')
                         ->with('success', 'User berhasil dibuat!');
    }

    /**
     * Form edit user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $wilayahs = Wilayah::all(); // ✅ kirim daftar wilayah
        return view('Autentikasi.update', compact('user', 'wilayahs'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'role'     => 'required|in:superadmin,admin,user',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'password' => 'nullable|string|min:6'
        ]);

        $user->update([
            'name'       => $request->name,
            'username'   => $request->username,
            'email'      => $request->email,
            'role'       => $request->role,
            'wilayah_id' => $request->wilayah_id,
            'password'   => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->route('dashboard.user')
                         ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dashboard.user')
                         ->with('success', 'User berhasil dihapus!');
    }
}