<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TugasController;

Route::get('/', function () {
    return view('welcome', [
        'title' => 'Beranda',
        'menuHome' => 'active',
    ]);
})->name('welcome');

// Login
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route ini butuh login
Route::middleware('checkLogin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Hanya Admin
    Route::middleware('isAdmin')->group(function () {
        // User
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/excel', [UserController::class, 'excel'])->name('excel');
            Route::get('/pdf', [UserController::class, 'pdf'])->name('pdf');
        });

        // Tugas
        Route::prefix('tugas')->name('tugas.')->group(function () {
            Route::get('/', [TugasController::class, 'index'])->name('index');
            Route::get('/create', [TugasController::class, 'create'])->name('create');
            Route::post('/store', [TugasController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [TugasController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [TugasController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [TugasController::class, 'destroy'])->name('destroy');
            Route::get('/excel', [TugasController::class, 'excel'])->name('excel');
            Route::get('/pdf', [TugasController::class, 'pdf'])->name('pdf');
        });
    });
});
