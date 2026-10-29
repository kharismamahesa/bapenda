<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\KategoriBeritaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect ke /admin
Route::get('/', function () {
    return redirect()->route('admin');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman /admin diarahkan ke login (bila belum terautentikasi) atau ke dashboard (bila sudah terautentikasi)
Route::get('/admin', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
})->name('admin');

// Admin Group Routes (Protected dengan middleware auth)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Kategori Berita
    Route::get('/kategori-berita/data', [KategoriBeritaController::class, 'data'])->name('kategori-berita.data');
    Route::resource('/kategori-berita', KategoriBeritaController::class)->except(['create', 'edit']);

    // CRUD Berita
    Route::get('/berita/data', [BeritaController::class, 'data'])->name('berita.data');
    Route::resource('/berita', BeritaController::class)->except(['create', 'edit']);
});
