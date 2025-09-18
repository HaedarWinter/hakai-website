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

Route::middleware('isLogin')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProses'])->name('loginProses');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route ini butuh login
Route::middleware('checkLogin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Hanya admin
    Route::middleware('isAdmin')->group(function () {
        // Tugas - untuk admin
        Route::get('/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
        Route::post('/tugas/store', [TugasController::class, 'store'])->name('tugas.store');
        Route::get('/tugas/edit/{id}', [TugasController::class, 'edit'])->name('tugas.edit');
        Route::post('/tugas/update/{id}', [TugasController::class, 'update'])->name('tugas.update');
        Route::delete('/tugas/destroy/{id}', [TugasController::class, 'destroy'])->name('tugas.destroy');
        Route::get('/tugas/excel', [TugasController::class, 'excel'])->name('tugas.excel');
        Route::post('/tugas/{id}/approve', [TugasController::class, 'approve'])->name('tugas.approve');
        Route::post('/tugas/{id}/reject', [TugasController::class, 'reject'])->name('tugas.reject');

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
    });

    // Tugas - untuk semua user
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{id}', [TugasController::class, 'show'])->name('tugas.show'); // Tetap di sini
    Route::post('/tugas/{id}/upload', [TugasController::class, 'upload'])->name('tugas.upload');
    Route::get('/tugas/pdf', [TugasController::class, 'pdf'])->name('tugas.pdf');
});
