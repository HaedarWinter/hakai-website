<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome', [
        'title' => 'Beranda',
        'menuHome' => 'active',
    ]);
}) -> name ('welcome');

//login
Route::get('/login', [AuthController::class, 'login']) -> name ('login');
Route::post('/login', [AuthController::class, 'loginProses']) -> name ('loginProses');

// Logout ga pake post
//Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Logout harus POST untuk keamanan CSRF
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('checkLogin')->group(function () {
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']) -> name ('dashboard');
    //User
    Route::get('/user', [UserController::class, 'index']) -> name ('user');
    //Tugas
    Route::get('/tugas', [TugasController::class, 'index']) -> name ('tugas');
});



