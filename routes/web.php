<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Autentikasi\AuthController;
use App\Http\Controllers\Autentikasi\UserController;
use App\Http\Controllers\Autentikasi\AccountSettingController;
use App\Http\Controllers\Dashboard\PekerjaanController;
use App\Http\Controllers\Dashboard\SettingAplikasiController;
use App\Http\Middleware\RoleMiddleware;



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

    // Semua role bisa lihat dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/kota/{id}', [DashboardController::class, 'kota'])->name('dashboard.kota');
    Route::get('/user', [AuthController::class, 'user'])->name('dashboard.user');

    Route::get('/account', [AccountSettingController::class, 'index'])->name('account.index');

    // rencana pekerjaan
    Route::get('/pekerjaan', [PekerjaanController::class, 'index'])->name('pekerjaan.index');

    // update profile
    Route::get('/account/edit', [AccountSettingController::class, 'edit'])->name('account.edit');
    Route::put('/account/update', [AccountSettingController::class, 'update'])->name('account.update');

    Route::get('/account/setting', [AccountSettingController::class, 'accountSetting'])->name('account.setting');
    Route::post('/account/setting/update', [AccountSettingController::class, 'updateAccountSetting'])->name('account.setting.update');
    // -----------------------
    // Superadmin only
    // -----------------------
    Route::middleware(['role:superadmin'])->group(function() {

        // Pekerjaan
        Route::get('/pekerjaan/create', [PekerjaanController::class, 'create'])->name('pekerjaan.create');
        Route::post('/dashboard/pekerjaan', [PekerjaanController::class, 'store'])->name('pekerjaan.store');
        Route::get('/pekerjaan/{id}/edit', [PekerjaanController::class, 'edit'])->name('pekerjaan.edit');
        Route::put('/pekerjaan/{id}', [PekerjaanController::class, 'update'])->name('pekerjaan.update');
        Route::delete('/pekerjaan/{id}', [PekerjaanController::class, 'destroy'])->name('pekerjaan.destroy');

        

        // User management
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        //setting aplikasi
        Route::get('setting-aplikasi', [SettingAplikasiController::class, 'index'])->name('setting_aplikasi.index');
    Route::get('setting-aplikasi/{id}/edit', [SettingAplikasiController::class, 'edit'])->name('setting_aplikasi.edit');
    Route::put('setting-aplikasi/{id}', [SettingAplikasiController::class, 'update'])->name('setting_aplikasi.update');

    });

    // -----------------------
    // Admin only
    // -----------------------
    Route::middleware(['role:admin,superadmin'])->group(function() {
        // Admin bisa approve progress pekerjaan
        Route::post('/pekerjaan/{id}/approve', [PekerjaanController::class, 'approve'])->name('pekerjaan.approve');
    });

    // -----------------------
    // Semua role bisa lihat detail pekerjaan
    // -----------------------
    Route::get('/pekerjaan/{id}', [PekerjaanController::class, 'show'])->name('pekerjaan.show');
});