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
use App\Http\Controllers\Dashboard\WeatherController;
use App\Http\Controllers\Dashboard\GeoController;
use App\Http\Controllers\Dashboard\LaporanController;
use App\Http\Controllers\Dashboard\LaporanApprovalSettingController;
use App\Http\Controllers\LandingPage\ProgresController;
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

// ====================== PUBLIC SIGNATURE VERIFICATION ======================
Route::get('/verify-signature/{signatureId}', [LaporanApprovalSettingController::class, 'verifySignature'])
    ->name('verify.signature');


// ====================== DASHBOARD ======================
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Semua role bisa lihat dashboard (root of prefix)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/kota/{id}', [DashboardController::class, 'kota'])->name('dashboard.kota');
    Route::get('/user', [AuthController::class, 'user'])->name('dashboard.user');
    Route::get('/user-location', [GeoController::class, 'getLocation'])->name('user.location');
    Route::get('/sidebar-weather', [WeatherController::class, 'getWeather'])->name('sidebar.weather');

    // ====================== ACCOUNT ======================
    Route::prefix('account')->group(function () {
        Route::get('/', [AccountSettingController::class, 'index'])->name('account.index');
        Route::get('/edit', [AccountSettingController::class, 'edit'])->name('account.edit');
        Route::put('/update', [AccountSettingController::class, 'update'])->name('account.update');
        Route::get('/setting', [AccountSettingController::class, 'accountSetting'])->name('account.setting');
        Route::post('/setting/update', [AccountSettingController::class, 'updateAccountSetting'])->name('account.setting.update');
    });

    // ====================== REALISASI ======================
    Route::prefix('realisasi')->group(function () {

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
        Route::get('/download-template', [RealisasiController::class, 'downloadTemplate'])->name('realisasi.downloadTemplate');
        Route::get('/realisasi/modal-data/{item}', [RealisasiController::class, 'getModalData']);

        // GR
        Route::get('/create-gr/{pr}', [RealisasiController::class, 'createGR'])->name('realisasi.createGR');
        Route::post('/store-gr/{pr}/{po}', [RealisasiController::class, 'storeGR'])->name('realisasi.storeGR');
        Route::get('/edit-gr/{pr}', [RealisasiController::class, 'editGR'])->name('realisasi.editGR');
        Route::put('/update-gr/{pr}/{gr}', [RealisasiController::class, 'updateGR'])->name('realisasi.updateGR');

        // Payment Request
        Route::get('/create-payment/{pr}', [RealisasiController::class, 'createPayment'])->name('realisasi.createPayment');
        Route::post('/store-payment/{pr}', [RealisasiController::class, 'storePayment'])->name('realisasi.storePayment');
        Route::get('/{pr}/payment/{payment}/edit', [RealisasiController::class, 'editPayment'])->name('realisasi.editPayment');
        Route::put('/{pr}/payment/{payment}', [RealisasiController::class, 'updatePayment'])->name('realisasi.updatePayment');

        // Update status bertahap (PR → PO → GR → Payment)
        Route::get('/update-status/{pr}/{status}', [RealisasiController::class, 'updateStatus'])->name('realisasi.updateStatus');

        // Hapus PR
        Route::delete('/{pr}/destroy', [RealisasiController::class, 'destroy'])->name('realisasi.destroy');
    });

    // ====================== LAPORAN ======================
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/create', [LaporanController::class, 'create'])->name('create');
        Route::post('/store', [LaporanController::class, 'store'])->name('store');

        // Admin & Superadmin only
        Route::middleware('role:admin,superadmin')->group(function () {
            Route::get('/approval-settings', [LaporanApprovalSettingController::class, 'index'])
                ->name('approval-settings.index');
            Route::post('/approval-settings', [LaporanApprovalSettingController::class, 'store'])
                ->name('approval-settings.store');
            Route::put('/approval-settings/{id}', [LaporanApprovalSettingController::class, 'update'])
                ->name('approval-settings.update');
            Route::delete('/approval-settings/{id}', [LaporanApprovalSettingController::class, 'destroy'])
                ->name('approval-settings.destroy');
            Route::post('/approval-settings/{id}/toggle-active', [LaporanApprovalSettingController::class, 'toggleActive'])
                ->name('approval-settings.toggle-active');

            // Laporan Approval
            Route::post('/{id}/approve', [LaporanController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [LaporanController::class, 'reject'])->name('reject');
            Route::delete('/{id}', [LaporanController::class, 'destroy'])->name('destroy');
        });

        // General routes
        Route::get('/{id}', [LaporanController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [LaporanController::class, 'submitForApproval'])->name('submit');
        Route::get('/{id}/export-pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{id}/export-excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
    });

    // ====================== RENCANA PEKERJAAN ======================
    Route::get('/pekerjaan', [PekerjaanController::class, 'index'])->name('pekerjaan.index');
    Route::get('/pekerjaan/json/{id}', [PekerjaanController::class, 'getJsonDetails'])->name('pekerjaan.json.detail');

    // DETAIL PEKERJAAN (prefix pekerjaan/{id})
    Route::prefix('pekerjaan/{id}')->group(function () {

        Route::get('/detail', [PekerjaanDetailController::class, 'index'])->name('pekerjaan.detail');

        // ---------- SUB PEKERJAAN ----------
        Route::get('/sub-pekerjaan', [PekerjaanDetailController::class, 'subPekerjaan'])->name('pekerjaan.sub.index');
        Route::get('/sub-pekerjaan/{sub}/progress', [PekerjaanDetailController::class, 'progresFisikSub'])->name('pekerjaan.sub.progress');

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
        Route::post('/data-kontrak/store', [PekerjaanDetailController::class, 'storeKontrak'])->name('pekerjaan.data.kontrak.store');
        Route::delete('/data-kontrak/{kontrak}/delete', [PekerjaanDetailController::class, 'destroyKontrak'])->name('pekerjaan.data.kontrak.destroy');
        Route::put('/data-kontrak/{kontrak}/update', [PekerjaanDetailController::class, 'updateKontrak'])->name('pekerjaan.data.kontrak.update');

        Route::get('/data-gambar', [PekerjaanDetailController::class, 'gambar'])->name('pekerjaan.data.gambar');
        Route::post('/data-gambar/store', [PekerjaanDetailController::class, 'storeGambar'])->name('pekerjaan.data.gambar.store');
        Route::get('/data-gambar/{gambar}/approve', [PekerjaanDetailController::class, 'approveGambar'])->name('pekerjaan.data.gambar.approve');
        Route::get('/data-gambar/{gambar}/reject', [PekerjaanDetailController::class, 'rejectGambar'])->name('pekerjaan.data.gambar.reject');
        Route::delete('/data-gambar/{gambar}/delete', [PekerjaanDetailController::class, 'destroyGambar'])->name('pekerjaan.data.gambar.destroy');

        // ---------- DATA KORESPONDENSI ----------
        Route::get('/data-korespondensi', [PekerjaanDetailController::class, 'korespondensi'])->name('pekerjaan.data.korespondensi');
        Route::post('/data-korespondensi/store', [PekerjaanDetailController::class, 'storeKorespondensi'])->name('pekerjaan.data.korespondensi.store');
        Route::put('/data-korespondensi/{korespondensi}/update', [PekerjaanDetailController::class, 'updateKorespondensi'])->name('pekerjaan.data.korespondensi.update');
        Route::delete('/data-korespondensi/{korespondensi}/delete', [PekerjaanDetailController::class, 'destroyKorespondensi'])->name('pekerjaan.data.korespondensi.destroy');

        // ---------- DOKUMEN INVESTASI ----------
        Route::get('/dokumen-investasi', [PekerjaanDetailController::class, 'dokumenInvestasi'])->name('pekerjaan.data.dokumen_investasi');
        Route::post('/dokumen-investasi/store', [PekerjaanDetailController::class, 'storeDokumenInvestasi'])->name('pekerjaan.data.dokumen_investasi.store');
        Route::put('/dokumen-investasi/{dokumen}/update', [PekerjaanDetailController::class, 'updateDokumenInvestasi'])->name('pekerjaan.data.dokumen_investasi.update');
        Route::delete('/dokumen-investasi/{dokumen}/delete', [PekerjaanDetailController::class, 'destroyDokumenInvestasi'])->name('pekerjaan.data.dokumen_investasi.destroy');

        // LAPORAN (sub-pages)
        Route::get('/data-laporan', [PekerjaanDetailController::class, 'laporan'])->name('pekerjaan.data.laporan');
        Route::get('/laporan-approval', [PekerjaanDetailController::class, 'laporanApproval'])->name('pekerjaan.laporan.approval');
        Route::post('/laporan-approval/store', [PekerjaanDetailController::class, 'storeLaporanApproval'])->name('pekerjaan.laporan.approval.store');
        Route::delete('/laporan-approval/{laporan}/delete', [PekerjaanDetailController::class, 'destroyLaporanApproval'])->name('pekerjaan.laporan.approval.destroy');
        Route::get('/laporan-approval/{laporan}/approve', [PekerjaanDetailController::class, 'approveLaporanApproval'])->name('pekerjaan.laporan.approval.approve');
        Route::get('/laporan-approval/{laporan}/reject', [PekerjaanDetailController::class, 'rejectLaporanApproval'])->name('pekerjaan.laporan.approval.reject');

        Route::get('/laporan-qa-qc', [PekerjaanDetailController::class, 'laporanQaQc'])->name('pekerjaan.laporan.qa');

        // approve laporan dokumentasi
        Route::get('/laporan-dokumentasi', [PekerjaanDetailController::class, 'laporanDokumentasi'])
            ->name('pekerjaan.laporan.dokumentasi');

        Route::get('/laporan-dokumentasi/{dailyProgressId}', [PekerjaanDetailController::class, 'showDokumentasi'])
            ->name('pekerjaan.laporan.dokumentasi.show');

        Route::get('/laporan-dokumentasi/{dailyProgressId}/approve', [PekerjaanDetailController::class, 'approveDokumentasi'])
            ->name('pekerjaan.laporan.dokumentasi.approve');

        Route::post('/laporan-dokumentasi/{dailyProgressId}/reject', [PekerjaanDetailController::class, 'rejectDokumentasi'])
            ->name('pekerjaan.laporan.dokumentasi.reject');

        Route::post('/laporan-dokumentasi/{dailyProgressId}/revise', [PekerjaanDetailController::class, 'reviseDokumentasi'])
            ->name('pekerjaan.laporan.dokumentasi.revise');

        Route::delete('/laporan-dokumentasi/{dailyProgressId}', [PekerjaanDetailController::class, 'destroyDokumentasi'])
            ->name('pekerjaan.laporan.dokumentasi.destroy');
        // end approve laporan dokumentasi

        // ---------- STATUS INVESTASI ----------
        Route::get('/status-perencanaan', [PekerjaanDetailController::class, 'perencanaan'])->name('pekerjaan.status.perencanaan');
        Route::get('/status-pelelangan', [PekerjaanDetailController::class, 'pelelangan'])->name('pekerjaan.status.pelelangan');
        Route::get('/status-pelaksanaan', [PekerjaanDetailController::class, 'pelaksanaan'])->name('pekerjaan.status.pelaksanaan');
        Route::get('/status-selesai', [PekerjaanDetailController::class, 'selesai'])->name('pekerjaan.status.selesai');
    });

    // ====================== SUPERADMIN ONLY ======================
    Route::middleware(['role:superadmin'])->group(function () {

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
    Route::middleware(['role:admin,superadmin'])->group(function () {
        Route::post('/pekerjaan/{id}/approve', [PekerjaanController::class, 'approve'])->name('pekerjaan.approve');
    });

    Route::get('/pekerjaan/{id}', [PekerjaanController::class, 'show'])->name('pekerjaan.show');
});


// ======================= PWA MOBILE ===================
Route::prefix('landingpage')->name('landingpage.')->group(function () {
    Route::middleware(['auth', 'role:user'])->group(function () {

        // ========== VIEW ROUTES ==========
        Route::get('/beranda', [ProgresController::class, 'index'])->name('index');
        Route::get('/pelaporan', [ProgresController::class, 'pelaporan'])->name('index.pelaporan');
        Route::get('/pelaporanform', [ProgresController::class, 'pelaporanform'])->name('index.pelaporanform');
        Route::get('/pelaporanform-edit', [ProgresController::class, 'pelaporanformedit'])->name('index.pelaporanformedit');
        Route::get('/dokumentasi', [ProgresController::class, 'dokumentasi'])->name('index.dokumentasi');

        // API Get Photos untuk Dokumentasi
        Route::get('/api/dokumentasi', [ProgresController::class, 'apiGetDokumentasi'])
            ->name('api.dokumentasi');
        Route::get('/debug/dokumentasi', [ProgresController::class, 'debugDokumentasi'])
            ->name('debug.dokumentasi');
        Route::post('/api/dokumentasi/export-pdf', [ProgresController::class, 'exportDokumentasiPdf'])
            ->name('api.dokumentasi.export.pdf');
        Route::get('/debug/dokumentasi', [ProgresController::class, 'debugDokumentasi'])
            ->name('debug.dokumentasi');

        // monitoring progress
        Route::get('/monitoring-progress', [ProgresController::class, 'monitoringProgress'])
            ->name('monitoring.progress');

        // ========== EXPORT & PRINT ROUTES  ==========
        Route::get('/laporan/{id}/export-pdf', [ProgresController::class, 'exportLaporanPdf'])
            ->name('laporan.export.pdf');

        // ========== PROFILE VENDOR ROUTES ==========
        Route::get('/profile', [ProgresController::class, 'vendorProfile'])->name('profile');
        Route::get('/profile/edit', [ProgresController::class, 'vendorProfileEdit'])->name('profile.edit');
        Route::put('/profile/update', [ProgresController::class, 'vendorProfileUpdate'])->name('profile.update');
        Route::get('/profile/password', [ProgresController::class, 'vendorPasswordEdit'])->name('profile.password');
        Route::post('/profile/password/update', [ProgresController::class, 'vendorPasswordUpdate'])->name('profile.password.update');

        // ========== API ROUTES - CASCADE SELECTION ==========
        Route::get('/api/po/pekerjaan/{pekerjaanId}', [ProgresController::class, 'getPoByPekerjaan'])
            ->name('api.po.by.pekerjaan');

        Route::get('/api/pekerjaan-items/po/{poId}', [ProgresController::class, 'getPekerjaanItemsByPo'])
            ->name('api.items.by.po');

        // ========== API ROUTES - DAILY PROGRESS CRUD ==========

        // GET: List all reports (Ringkasan)
        Route::get('/api/progress-harian', [ProgresController::class, 'apiGetReports'])
            ->name('api.reports');

        // POST: Create new report
        Route::post('/api/progress-harian/store', [ProgresController::class, 'apiStoreReport'])
            ->name('api.store.report');

        // GET: Show single report detail (untuk edit)
        Route::get('/api/progress-harian/{id}', [ProgresController::class, 'apiShowReport'])
            ->name('api.show.report');

        // Wilayah & Pekerjaan API
        Route::get('api/wilayah', [ProgresController::class, 'apiGetWilayah']);
        Route::get('api/pekerjaan/wilayah/{wilayahId}', [ProgresController::class, 'apiGetPekerjaanByWilayah']);

        // PUT/PATCH: Update existing report
        Route::put('/api/progress-harian/{id}', [ProgresController::class, 'apiUpdateReport'])
            ->name('api.update.report');

        // DELETE: Delete report
        Route::delete('/api/progress-harian/{id}', [ProgresController::class, 'apiDeleteReport'])
            ->name('api.delete.report');

        Route::get('/api/progress/{id}', [ProgresController::class, 'apiGetProgressData'])
            ->name('api.progress.data');
    });
});