<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Redirect sesuai role
            if ($user->role === 'user') {
                return redirect()->route('landingpage.index'); 
            }
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
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', 
        ]);

        return back()->with('success', 'Akun Anda berhasil dibuat, silakan login!');
    }

    // Tampilkan form login
    public function loginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Redirect sesuai role
            if ($user->role === 'user') {
                return redirect()->route('landingpage.index'); 
            }
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

        if (Auth::attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // 🎯 REDIRECT BERDASARKAN ROLE
            if ($user->role === 'user') {
                // User biasa → ke PWA Mobile
                return redirect()->route('landingpage.index')
                    ->with('success', 'Selamat datang di P-Mones Mobile!');
            } else {
                // Admin/Superadmin → ke Dashboard
                return redirect()->route('dashboard.index')
                    ->with('success', 'Selamat datang, ' . $user->name);
            }
        }

        // Login gagal
        return back()->withErrors([
            'username' => 'Username atau password salah'
        ])->withInput($request->only('username'));
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Anda telah logout');
    }

    // User list (untuk dashboard admin)
    public function user()
    {
        $users = User::all();
        return view('Autentikasi.user', compact('users')); 
    }
}