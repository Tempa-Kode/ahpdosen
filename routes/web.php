<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'prosesLogin'])->name('prosesLogin');
Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\LoginController::class, 'dashboard'])->name('dashboard');
    Route::prefix('dosen')->group(function () {
        Route::get('/', [\App\Http\Controllers\DosenController::class, 'index'])->name('dosen.index');
        Route::get('/tambah', [\App\Http\Controllers\DosenController::class, 'tambah'])->name('dosen.tambah');
        Route::post('/simpan', [\App\Http\Controllers\DosenController::class, 'simpan'])->name('dosen.simpan');
        Route::get('/edit/{id}', [\App\Http\Controllers\DosenController::class, 'edit'])->name('dosen.edit');
        Route::post('/update/{id}', [\App\Http\Controllers\DosenController::class, 'update'])->name('dosen.update');
        Route::delete('/hapus/{id}', [\App\Http\Controllers\DosenController::class, 'hapus'])->name('dosen.hapus');
    });
});
