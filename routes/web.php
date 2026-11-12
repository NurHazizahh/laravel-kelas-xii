<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FilmController,
    AuthController,
    KritikController,
};
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\FilmController as AdminFilmController;
use App\Http\Controllers\Admin\GenreController as AdminGenreController;
use App\Http\Controllers\Admin\CastController as AdminCastController;
use App\Http\Controllers\Admin\PeranController as AdminPeranController;
use App\Http\Controllers\Admin\KritikController as AdminKritikController;

Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('/movies/{film}/kritik', [KritikController::class, 'store'])->name('kritik.store')->middleware('auth');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('films', AdminFilmController::class)->except(['show']);
    Route::resource('genres', AdminGenreController::class)->except(['show']);
    Route::resource('casts', AdminCastController::class)->except(['show']);
    Route::resource('perans', AdminPeranController::class)->except(['show']);
    Route::resource('kritiks', AdminKritikController::class)->only(['index', 'destroy']);
    Route::resource('komentar', AdminKritikController::class)->only(['index', 'destroy'])->names([
        'index' => 'komentar.index',
        'destroy' => 'komentar.destroy',
    ]);
});
