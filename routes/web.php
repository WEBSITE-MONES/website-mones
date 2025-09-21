<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Autentikasi\AuthController;
use App\Http\Controllers\Autentikasi\UserController;
use App\Http\Controllers\Autentikasi\AccountSettingController;
use App\Http\Controllers\Dashboard\PekerjaanController;
use App\Http\Controllers\Dashboard\SettingAplikasiController;
use App\Http\Controllers\Dashboard\PekerjaanDetailController;
use App\Http\Controllers\Dashboard\RealisasiController;
use App\Http\Middleware\RoleMiddleware;


// ====================== AUTH ======================
// register
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/Autentikasi/register', [AuthController::class, 'register'])->name('register.submit');

// login
Route::get('/loginn', [AuthController::class, 'loginForm'])->name('login');
Route::post('/loginn', [AuthController::class, 'login'])->name('login.submit');

// logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ====================== DASHBOARD ======================
Route::prefix('dashboard')->middleware('auth')->group(function () {

    // Semua role bisa lihat dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/kota/{id}', [DashboardController::class, 'kota'])->name('dashboard.kota');
    Route::get('/user', [AuthController::class, 'user'])->name('dashboard.user');

    // ====================== ACCOUNT ======================
    Route::prefix('account')->group(function() {
        Route::get('/', [AccountSettingController::class, 'index'])->name('account.index');
        Route::get('/edit', [AccountSettingController::class, 'edit'])->name('account.edit');
        Route::put('/update', [AccountSettingController::class, 'update'])->name('account.update');
        Route::get('/setting', [AccountSettingController::class, 'accountSetting'])->name('account.setting');
        Route::post('/setting/update', [AccountSettingController::class, 'updateAccountSetting'])->name('account.setting.update');
    });

    // ====================== REALISASI ======================
    Route::prefix('realisasi')->group(function() {

    // Daftar PR
    Route::get('/', [RealisasiController::class, 'index'])->name('realisasi.index');

    // Input PR
    Route::get('/create-pr', [RealisasiController::class, 'createPR'])->name('realisasi.createPR');
    Route::post('/store-pr', [RealisasiController::class, 'storePR'])->name('realisasi.storePR');
    Route::get('/{pr}/edit', [RealisasiController::class, 'editPR'])->name('realisasi.editPR');
    Route::put('/{pr}/update', [RealisasiController::class, 'updatePR'])->name('realisasi.updatePR');

    // Kontrak (PO)
    Route::get('/create-po/{pr}', [RealisasiController::class, 'createPO'])->name('realisasi.createPO');
    Route::post('/store-po/{pr}', [RealisasiController::class, 'storePO'])->name('realisasi.storePO');
    Route::get('/po/{po}/edit', [RealisasiController::class, 'editPO'])->name('realisasi.editPO');
    Route::put('/po/{po}/update', [RealisasiController::class, 'updatePO'])->name('realisasi.updatePO');

    // (Progress)
    Route::get('/edit-progress/{po}', [RealisasiController::class, 'editProgress'])->name('realisasi.editProgress');
    Route::put('/update-progress/{po}', [RealisasiController::class, 'updateProgress'])->name('realisasi.updateProgress');
    Route::post('/{po}/import-excel', [RealisasiController::class, 'importExcel'])->name('realisasi.importExcel');
    Route::get('/download-template', [RealisasiController::class, 'downloadTemplate'])
    ->name('realisasi.downloadTemplate');
    Route::get('/realisasi/modal-data/{item}', [RealisasiController::class, 'getModalData']);


    // GR
    Route::get('/create-gr/{pr}', [RealisasiController::class, 'createGR'])->name('realisasi.createGR');
    Route::post('/store-gr/{pr}', [RealisasiController::class, 'storeGR'])->name('realisasi.storeGR');
    Route::get('/edit-gr/{pr}', [RealisasiController::class, 'editGR'])->name('realisasi.editGR');
    Route::put('/update-gr/{pr}/{gr}', [RealisasiController::class, 'updateGR'])->name('realisasi.updateGR');

    // Payment Request
    Route::get('/create-payment/{pr}', [RealisasiController::class, 'createPayment'])->name('realisasi.createPayment');
    Route::post('/store-payment/{pr}', [RealisasiController::class, 'storePayment'])->name('realisasi.storePayment');


    // Update status bertahap (PR → PO → GR → Payment)
    Route::get('/update-status/{pr}/{status}', [RealisasiController::class, 'updateStatus'])->name('realisasi.updateStatus');

    // Hapus PR
    Route::delete('/{pr}/destroy', [RealisasiController::class, 'destroy'])->name('realisasi.destroy');
});


    // ====================== RENCANA PEKERJAAN ======================
    Route::get('/pekerjaan', [PekerjaanController::class, 'index'])->name('pekerjaan.index');

    // DETAIL PEKERJAAN (prefix pekerjaan/{id})
    Route::prefix('pekerjaan/{id}')->group(function () {

        Route::get('/detail', [PekerjaanDetailController::class, 'index'])->name('pekerjaan.detail');

        // ---------- PROGRES INVESTASI ----------
        Route::get('/progres-fisik', [PekerjaanDetailController::class, 'progresFisik'])->name('pekerjaan.progres.fisik');
        Route::post('/progress/store', [PekerjaanDetailController::class, 'storeProgress'])->name('pekerjaan.progress.store');
        Route::get('/progress/create', [PekerjaanDetailController::class, 'createProgress'])->name('pekerjaan.progress.create');
        Route::get('/progress/{progress}/edit', [PekerjaanDetailController::class, 'editProgress'])->name('pekerjaan.progress.edit');
        Route::put('/progress/{progress}', [PekerjaanDetailController::class, 'updateProgress'])->name('pekerjaan.progress.update');
        Route::delete('/progress/{progress}', [PekerjaanDetailController::class, 'destroyProgress'])->name('pekerjaan.progress.destroy');
        Route::post('/progress/import', [PekerjaanDetailController::class, 'importProgress'])->name('pekerjaan.progress.import');

        Route::get('/progres-rkap', [PekerjaanDetailController::class, 'penyerapanRkap'])->name('pekerjaan.rkap');
        Route::get('/progres-pembayaran', [PekerjaanDetailController::class, 'pembayaran'])->name('pekerjaan.pembayaran');

        // ---------- DATA INVESTASI ----------
        Route::get('/data-kontrak', [PekerjaanDetailController::class, 'kontrak'])->name('pekerjaan.data.kontrak');
        Route::get('/data-gambar', [PekerjaanDetailController::class, 'gambar'])->name('pekerjaan.data.gambar');
        Route::get('/data-laporan', [PekerjaanDetailController::class, 'laporan'])->name('pekerjaan.data.laporan');
        Route::get('/data-korespondensi', [PekerjaanDetailController::class, 'korespondensi'])->name('pekerjaan.data.korespondensi');

        // ---------- STATUS INVESTASI ----------
        Route::get('/status-perencanaan', [PekerjaanDetailController::class, 'perencanaan'])->name('pekerjaan.status.perencanaan');
        Route::get('/status-pelelangan', [PekerjaanDetailController::class, 'pelelangan'])->name('pekerjaan.status.pelelangan');
        Route::get('/status-pelaksanaan', [PekerjaanDetailController::class, 'pelaksanaan'])->name('pekerjaan.status.pelaksanaan');
        Route::get('/status-selesai', [PekerjaanDetailController::class, 'selesai'])->name('pekerjaan.status.selesai');
    });

    // ====================== SUPERADMIN ONLY ======================
    Route::middleware(['role:superadmin'])->group(function() {

        // CRUD Pekerjaan
        Route::get('/pekerjaan/create', [PekerjaanController::class, 'create'])->name('pekerjaan.create');
        Route::post('/pekerjaan', [PekerjaanController::class, 'store'])->name('pekerjaan.store');
        Route::get('/pekerjaan/{id}/edit', [PekerjaanController::class, 'edit'])->name('pekerjaan.edit');
        Route::put('/pekerjaan/{id}', [PekerjaanController::class, 'update'])->name('pekerjaan.update');
        Route::delete('/pekerjaan/{id}', [PekerjaanController::class, 'destroy'])->name('pekerjaan.destroy');

        // CRUD Users
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Setting Aplikasi
        Route::get('/setting-aplikasi', [SettingAplikasiController::class, 'index'])->name('setting_aplikasi.index');
        Route::get('/setting-aplikasi/{id}/edit', [SettingAplikasiController::class, 'edit'])->name('setting_aplikasi.edit');
        Route::put('/setting-aplikasi/{id}', [SettingAplikasiController::class, 'update'])->name('setting_aplikasi.update');
    });

    // ====================== ADMIN + SUPERADMIN ======================
    Route::middleware(['role:admin,superadmin'])->group(function() {
        Route::post('/pekerjaan/{id}/approve', [PekerjaanController::class, 'approve'])->name('pekerjaan.approve');
    });

    // Semua role bisa lihat detail pekerjaan
    Route::get('/pekerjaan/{id}', [PekerjaanController::class, 'show'])->name('pekerjaan.show');
});