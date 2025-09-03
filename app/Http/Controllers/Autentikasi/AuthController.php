<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan form register
    public function registerForm()
    {
        if (Auth::check()) {
        return redirect()->route('dashboard.index');
    }
    return view('Autentikasi.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // pakai password_confirmation
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // default role user
        ]);

        return back()->with('success', 'Akun Anda berhasil dibuat, silakan login!');
    }

    // Tampilkan form login
    public function loginForm()
    {
        if (Auth::check()) {
        return redirect()->route('dashboard.index');
    }
    return view('Autentikasi.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if(Auth::attempt($request->only('username', 'password'))){
    $request->session()->regenerate();
    return redirect()->route('dashboard.index'); 
}

        // login gagal, tetap di halaman login
        return back()->withErrors(['username' => 'Username atau password salah']);
    }

    // Proses logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    // user
    public function user()
{
    $users = User::all(); // ambil semua data user
    return view('Autentikasi.user', compact('users')); 
}
}