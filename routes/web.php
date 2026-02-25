<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PanoramaController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\CctvController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ManualImportController;
use App\Http\Controllers\TemplateController;

// =====================
// 👤 GUEST ROUTES
// =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionsController::class, 'create'])->name('view-login');
    Route::post('/session', [SessionsController::class, 'store'])->name('login');

    // ✅ Tangkap GET ke /session agar tidak error
    Route::get('/session', function () {
        return redirect()->route('view-login');
    });

    Route::get('/login/forgot-password', [ResetController::class, 'create']);
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

// =====================
// 🔒 AUTH ROUTES
// =====================
Route::middleware('auth')->group(function () {
    Route::get('/', [LokasiController::class, 'cctvlokasi'])->name('lokasi.index');
    Route::get('/cctv-publik', [PanoramaController::class, 'dashboard'])->name('panorama.panorama');

    Route::get('/dashboard', [LokasiController::class, 'dashboard'])
        ->middleware('role:admin')
        ->name('dashboard');


    Route::get('user-management', fn() => view('users.menu-users'))->name('user-management');

    Route::get('cctv-lokasi', [LokasiController::class, 'index'])->name('menu-lokasi');
    Route::get('menu-cctv-panorama', [PanoramaController::class, 'index'])->name('menu-panorama');

    Route::get('billing', fn() => view('billing'))->name('billing');
    Route::get('profile', fn() => view('profile'))->name('profile');
    Route::get('rtl', fn() => view('rtl'))->name('rtl');
    Route::get('tables', fn() => view('tables'))->name('tables');
    Route::get('virtual-reality', fn() => view('virtual-reality'))->name('virtual-reality');
    Route::get('static-sign-in', fn() => view('static-sign-in'))->name('sign-in');
    Route::get('static-sign-up', fn() => view('static-sign-up'))->name('sign-up');

    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/profile-user', [InfoUserController::class, 'showProfile'])->name('profile-user');
    Route::post('/profile-user/update', [InfoUserController::class, 'updateProfile'])->name('profile-user.update');
    Route::post('/profile-user/change-password', [InfoUserController::class, 'changePassword'])->name('profile-user.change-password');

    // Profil pengguna
    Route::get('/profil', [InfoUserController::class, 'showProfile'])->name('profil.pengguna');

    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');

    Route::post('/logout', [SessionsController::class, 'destroy'])->name('logout');
});

// =====================
// 🏠 PUBLIC ROUTES
// =====================
Route::get('/diy', [HomeController::class, 'home'])->name('welcome');

// =====================
// 📹 CCTV ROUTES
// =====================
Route::prefix('cctv')->group(function () {
    Route::get('/', [CctvController::class, 'index'])->name('cctv.index');
    Route::get('/create', [CctvController::class, 'create'])->name('cctv.create');
    Route::post('/', [CctvController::class, 'store'])->name('cctv.store');
    Route::get('/{cctv}', [CctvController::class, 'show'])->name('cctv.show');
    Route::get('/edit/{cctv}', [CctvController::class, 'edit'])->name('cctv.edit');
    Route::post('/{cctv}', [CctvController::class, 'update'])->name('cctv.update');
    Route::delete('/{cctv}', [CctvController::class, 'delete'])->name('cctv.delete');
});

// =====================
// 📍 LOKASI ROUTES
// =====================
Route::prefix('lokasi')->group(function () {
    Route::get('/index', [LokasiController::class, 'index'])->name('lokasi.index');
    Route::get('/create', [LokasiController::class, 'create'])->name('lokasi.create');
    Route::post('/', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::get('/edit/{lokasi}', [LokasiController::class, 'edit'])->name('lokasi.edit');
    Route::post('/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');
    Route::delete('/{lokasi}', [LokasiController::class, 'delete'])->name('lokasi.delete');

    Route::get('/check-duplicate', [LokasiController::class, 'checkDuplicate'])->name('lokasi.checkDuplicate');
    Route::get('/getWilayah', [LokasiController::class, 'getWilayah'])->name('lokasi.getWilayah');
    Route::get('/search', [LokasiController::class, 'search'])->name('lokasi.search');
    Route::get('/cctv/export', [LokasiController::class, 'export'])->name('lokasi.export');
    Route::get('/template/download', [TemplateController::class, 'download'])->name('lokasi.template.download');

    // Import manual
    Route::get('/import/manual', [ManualImportController::class, 'form'])->name('lokasi.import.manual.form');
    Route::post('/import/manual', [ManualImportController::class, 'import'])->name('lokasi.import.manual');
});

// Rekapan Lokasi
Route::get('/rekapan/cctv-lokasi', [LokasiController::class, 'showRekapanCCTV'])->name('rekapan.cctv.lokasi');
Route::get('/rekapan/detaillokasi', [LokasiController::class, 'daftarLokasi'])->name('rekapan.detaillokasi');

// =====================
// 🌄 PANORAMA ROUTES
// =====================
// Route::get('/cctv-publik', [PanoramaController::class, 'dashboard'])->name('panorama.panorama');
Route::prefix('panorama')->group(function () {
    Route::get('/index', [PanoramaController::class, 'index'])->name('panorama.index');
    Route::post('/store', [PanoramaController::class, 'store'])->name('panorama.store');
    Route::post('/{id}', [PanoramaController::class, 'update'])->name('panorama.update');
    Route::delete('/{id}', [PanoramaController::class, 'delete'])->name('panorama.delete');
    Route::get('/cctv/export', [PanoramaController::class, 'export'])->name('panorama.export');
    
        // Import manual panorama (POST only, for AJAX)
        Route::post('/import/manual', [PanoramaController::class, 'importManual'])->name('panorama.import.manual');
});

// Rekapan Panorama
Route::get('/rekapan/cctv-panorama', [PanoramaController::class, 'showPanorama'])->name('rekapan.cctv.panorama');
Route::get('/rekapan/cctv-panorama/{wilayah}', [PanoramaController::class, 'detailWilayah'])->name('rekapan.cctv.panorama.detail');

// Rekapan User
Route::get('/rekapan/users', [InfoUserController::class, 'daftarAdmin'])->name('rekapan.users');
