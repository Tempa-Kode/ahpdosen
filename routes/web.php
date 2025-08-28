<?php

use App\Http\Controllers\PenilaianController;
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
    Route::prefix('sub-sub-indikator')->group(function () {
        Route::get('/tambah', [\App\Http\Controllers\SubSubIndikatorController::class, 'tambah'])->name('subsubindikator.tambah');
        Route::post('/simpan', [\App\Http\Controllers\SubSubIndikatorController::class, 'simpan'])->name('subsubindikator.simpan');
        Route::get('/edit/{id}', [\App\Http\Controllers\SubSubIndikatorController::class, 'edit'])->name('subsubindikator.edit');
        Route::put('/update/{id}', [\App\Http\Controllers\SubSubIndikatorController::class, 'update'])->name('subsubindikator.update');
        Route::delete('/hapus/{id}', [\App\Http\Controllers\SubSubIndikatorController::class, 'hapus'])->name('subsubindikator.hapus');
    });

    Route::get('/penilaian/{dosen}/form', [PenilaianController::class, 'form'])->name('penilaian.form');
    Route::post('/penilaian/{dosen}/store', [PenilaianController::class, 'store'])->name('penilaian.store');

    // AHP Routes
    Route::prefix('ahp')->group(function () {
        Route::get('/', [\App\Http\Controllers\AhpController::class, 'dashboard'])->name('ahp.dashboard');
        Route::get('/detail/{dosen}', [\App\Http\Controllers\AhpController::class, 'detail'])->name('ahp.detail');
        Route::get('/comparison', [\App\Http\Controllers\AhpController::class, 'comparison'])->name('ahp.comparison');
        Route::get('/debug', [\App\Http\Controllers\AhpController::class, 'debug'])->name('ahp.debug');
    });

    // Perhitungan Routes
    Route::prefix('perhitungan')->group(function () {
        Route::get('/pendidikan-dan-pembelajaran', [\App\Http\Controllers\PerhitunganController::class, 'showPendidikanDanPembelajaran'])->name('perhitungan.show.pendidikan-dan-pembelajaran');
        Route::get('/api/pendidikan-dan-pembelajaran', [\App\Http\Controllers\PerhitunganController::class, 'pendidikanDanPembelajaran'])->name('perhitungan.pendidikan-dan-pembelajaran');
    });

    // AHP Penelitian Routes (Web Interface)
    Route::prefix('ahp-penelitian')->group(function () {
        Route::get('/', [\App\Http\Controllers\AhpPenelitianWebController::class, 'index'])->name('ahp.penelitian.index');
        Route::get('/langkah-perhitungan', [\App\Http\Controllers\AhpPenelitianWebController::class, 'langkahPerhitungan'])->name('ahp.penelitian.langkah');
        Route::get('/dosen/{dosen_id}', [\App\Http\Controllers\AhpPenelitianWebController::class, 'detailDosen'])->name('ahp.penelitian.detail.dosen');
    });

    // Legacy route - untuk backward compatibility
    Route::get('/perhitungan', [\App\Http\Controllers\PerhitunganController::class, 'pendidikanDanPembelajaran'])->name('perhitungan.pendidikanDanPembelajaran');
});

// API Routes untuk Penelitian (tanpa middleware auth untuk kemudahan akses)
Route::prefix('api/penelitian')->name('api.penelitian.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PerhitunganPenelitianController::class, 'hitungSemuaDosen'])->name('api.penelitian');
    Route::get('/skala-interval-kpt01', [\App\Http\Controllers\PerhitunganPenelitianController::class, 'laporanSkalaIntervalKPT01'])->name('skala.interval.kpt01');
});

// API Routes untuk AHP Penelitian
Route::prefix('api/ahp-penelitian')->name('api.ahp.penelitian.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AhpPenelitianController::class, 'perhitunganAhpPenelitian'])->name('perhitungan.lengkap');
    Route::get('/dosen/{dosen_id}', [\App\Http\Controllers\AhpPenelitianController::class, 'detailDosenAhp'])->name('detail.dosen');
});
