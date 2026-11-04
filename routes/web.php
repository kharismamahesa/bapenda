<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\KategoriBeritaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HalamanKontenController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Publik (Official Website)
Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/profil/{slug}', [PublicController::class, 'halaman'])->name('profil.show');
Route::get('/halaman/{slug}', [PublicController::class, 'halaman'])->name('halaman.show');
Route::get('/berita', [PublicController::class, 'beritaIndex'])->name('berita.index');
Route::get('/berita/{slug}', [PublicController::class, 'beritaShow'])->name('berita.show');
Route::get('/informasi-pajak-kendaraan', [PublicController::class, 'pajakKendaraan'])->name('pajak-kendaraan.index');
Route::get('/api/informasi-pajak-kendaraan', [PublicController::class, 'pajakKendaraanApi'])->middleware('throttle:10,1')->name('pajak-kendaraan.api');

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

    // CRUD User
    Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::resource('/users', UserController::class)->except(['create', 'edit']);

    // CRUD Halaman Konten
    Route::get('/halaman-konten/data', [HalamanKontenController::class, 'data'])->name('halaman-konten.data');
    Route::resource('/halaman-konten', HalamanKontenController::class)->except(['create', 'edit']);
});
