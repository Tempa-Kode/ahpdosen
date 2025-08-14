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
    Route::prefix('kriteria')->group(function () {
        Route::get('/', [\App\Http\Controllers\KriteriaController::class, 'index'])->name('kriteria.index');
        Route::get('/tambah', [\App\Http\Controllers\KriteriaController::class, 'tambah'])->name('kriteria.tambah');
        Route::post('/simpan', [\App\Http\Controllers\KriteriaController::class, 'simpan'])->name('kriteria.simpan');
        Route::get('/edit/{id}', [\App\Http\Controllers\KriteriaController::class, 'edit'])->name('kriteria.edit');
        Route::get('/detail/{id}', [\App\Http\Controllers\KriteriaController::class, 'detail'])->name('kriteria.detail');
        Route::post('/update/{id}', [\App\Http\Controllers\KriteriaController::class, 'update'])->name('kriteria.update');
        Route::delete('/hapus/{id}', [\App\Http\Controllers\KriteriaController::class, 'hapus'])->name('kriteria.hapus');
    });
    Route::prefix('indikator')->group(function () {
        Route::get('/tambah', [\App\Http\Controllers\IndikatorController::class, 'tambah'])->name('indikator.tambah');
        Route::post('/simpan', [\App\Http\Controllers\IndikatorController::class, 'simpan'])->name('indikator.simpan');
        Route::get('/edit/{id}', [\App\Http\Controllers\IndikatorController::class, 'edit'])->name('indikator.edit');
        Route::post('/update/{id}', [\App\Http\Controllers\IndikatorController::class, 'update'])->name('indikator.update');
        Route::delete('/hapus/{id}', [\App\Http\Controllers\IndikatorController::class, 'hapus'])->name('indikator.hapus');
        Route::get('detail/{id}', [\App\Http\Controllers\IndikatorController::class, 'detail'])->name('indikator.detail');
    });
    Route::prefix('sub-indikator')->group(function () {
        Route::get('/tambah', [\App\Http\Controllers\SubIndikatorController::class, 'tambah'])->name('subindikator.tambah');
        Route::post('/simpan', [\App\Http\Controllers\SubIndikatorController::class, 'simpan'])->name('subindikator.simpan');
        Route::get('/edit/{id}', [\App\Http\Controllers\SubIndikatorController::class, 'edit'])->name('subindikator.edit');
        Route::put('/update/{id}', [\App\Http\Controllers\SubIndikatorController::class, 'update'])->name('subindikator.update');
        Route::delete('/hapus/{id}', [\App\Http\Controllers\SubIndikatorController::class, 'hapus'])->name('subindikator.hapus');
        Route::get('/detail/{id}', [\App\Http\Controllers\SubIndikatorController::class, 'detail'])->name('subindikator.detail');
    });
});
