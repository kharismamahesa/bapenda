<?php

use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HalamanKontenController;
use App\Http\Controllers\Admin\KategoriBeritaController;
use App\Http\Controllers\Admin\KategoriRegulasiController;
use App\Http\Controllers\Admin\PengaduanController;
use App\Http\Controllers\Admin\RegulasiController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SurveiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SurveiApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Publik (Official Website)
Route::get('/', [PublicController::class, 'beranda'])->name('beranda');
Route::get('/profil/{slug}', [PublicController::class, 'halaman'])->name('profil.show');
Route::get('/layanan/{slug}', [PublicController::class, 'halaman'])->name('halaman.show');
Route::get('/berita', [PublicController::class, 'beritaIndex'])->name('berita.index');
Route::get('/berita/{slug}', [PublicController::class, 'beritaShow'])->name('berita.show');
Route::get('/video', [PublicController::class, 'videoIndex'])->name('video.index');
Route::get('/survei', [PublicController::class, 'survei'])->name('survei.index');
Route::post('/survei', [PublicController::class, 'storeSurvei'])->middleware('throttle:5,1')->name('survei.store');
Route::get('/api/survei/nilai', [SurveiApiController::class, 'scores'])->middleware('throttle:30,1')->name('survei.scores.api');
Route::get('/pengaduan', [PublicController::class, 'pengaduan'])->name('pengaduan.index');
Route::post('/pengaduan', [PublicController::class, 'submitPengaduan'])->middleware('throttle:5,1')->name('pengaduan.store');
Route::get('/regulasi', [PublicController::class, 'regulasiIndex'])->name('regulasi.index');
Route::get('/regulasi/{id}/download', [PublicController::class, 'regulasiDownload'])->name('regulasi.download');
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

Route::middleware(['auth', 'role:admin,superadmin,uptup'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/data', [PengaduanController::class, 'data'])->name('pengaduan.data');
    Route::get('/pengaduan/stats', [PengaduanController::class, 'stats'])->name('pengaduan.stats');
    Route::get('/pengaduan/{id}', [PengaduanController::class, 'show'])->name('pengaduan.show');
    Route::post('/pengaduan/{id}/balas', [PengaduanController::class, 'reply'])->name('pengaduan.reply');
});

Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::delete('/pengaduan/{id}', [PengaduanController::class, 'destroy'])->name('pengaduan.destroy');
    Route::get('/survei', [SurveiController::class, 'index'])->name('survei.index');
    Route::get('/survei/download', [SurveiController::class, 'download'])->name('survei.download');
    Route::get('/api/dashboard/summary', [DashboardController::class, 'summary'])->name('api.dashboard.summary');
    Route::get('/api/pengaduan/bulanan', [DashboardController::class, 'monthlyComplaints'])->name('api.pengaduan.monthly');
    Route::get('/api/survei/dashboard', [SurveiController::class, 'dashboardData'])->name('api.survei.dashboard');

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

    // CRUD Kategori Regulasi
    Route::get('/kategori-regulasi/data', [KategoriRegulasiController::class, 'data'])->name('kategori-regulasi.data');
    Route::resource('/kategori-regulasi', KategoriRegulasiController::class)->except(['create', 'edit']);

    // CRUD Regulasi
    Route::get('/regulasi/data', [RegulasiController::class, 'data'])->name('regulasi.data');
    Route::resource('/regulasi', RegulasiController::class)->except(['create', 'edit']);

    // CRUD Slider Hero
    Route::get('/slider/data', [SliderController::class, 'data'])->name('slider.data');
    Route::resource('/slider', SliderController::class)->except(['show']);

    // CRUD Video
    Route::get('/video/data', [VideoController::class, 'data'])->name('video.data');
    Route::resource('/video', VideoController::class)->except(['show']);
});

Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/survei/review', [SurveiController::class, 'review'])->name('survei.review');
    Route::post('/survei/review/{id}/restore', [SurveiController::class, 'restore'])->name('survei.review.restore');
    Route::delete('/survei/review/{id}', [SurveiController::class, 'destroyFlagged'])->name('survei.review.destroy');
});
