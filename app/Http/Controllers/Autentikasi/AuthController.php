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
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed', // pakai password_confirmation
        ]);

        User::create([
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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(Auth::attempt($request->only('email', 'password'))){
    $request->session()->regenerate();
    return redirect()->route('dashboard.index'); // sesuaikan route dashboard prefix kamu
}

        // login gagal, tetap di halaman login
        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    // Proses logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}