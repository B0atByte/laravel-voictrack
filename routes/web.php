<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::post('/search', [PublicController::class, 'search'])->name('search');
Route::get('/download/{id}', [PublicController::class, 'download'])->name('download');
Route::get('/download-all/{code}', [PublicController::class, 'downloadAll'])->name('download.all');
Route::get('/stream/{id}', [PublicController::class, 'stream'])->name('stream');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/upload', [AdminController::class, 'upload'])->name('admin.upload');
    Route::delete('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');
    Route::post('/delete-all', [AdminController::class, 'deleteAll'])->name('admin.deleteAll');
});
