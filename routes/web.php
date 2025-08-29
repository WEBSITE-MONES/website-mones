<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Autentikasi\AuthController;

// Route::get('/', function () {
//     return view('welcome');
// });

// login dan register
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/Autentikasi/register', [AuthController::class, 'register'])->name('register.submit');

// login
Route::get('/loginn', [AuthController::class, 'loginForm'])->name('login');
Route::post('/loginn', [AuthController::class, 'login'])->name('login.submit');

// logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// dashboard
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/kota', [DashboardController::class, 'kota'])->name('dashboard.kota');
});