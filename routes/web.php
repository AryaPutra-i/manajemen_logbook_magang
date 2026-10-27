<?php

use App\Http\Controllers\GantiPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenAkunController;
use App\Http\Controllers\ManajemenLogbookController;
use App\Http\Controllers\VerifikasiUbahDataController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/lupa-password', function () {
        return view('lupa-password');
    })->name('lupa-password');
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/manajemen-logbook', [ManajemenLogbookController::class, 'index'])->name('logbook.index');

    Route::middleware(['role:admin,dokter_magang,pembimbing_lapangan'])->group(function () {
        Route::get('/manajemen-akun', [ManajemenAkunController::class, 'menu'])->name('akun.menu');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/ganti-password', [GantiPasswordController::class, 'index'])->name('password.index');
        Route::post('/ganti-password', [GantiPasswordController::class, 'changePassword'])->name('password.update');

        Route::get('/manajemen-akun/tambah-akun', [ManajemenAkunController::class, 'create'])->name('akun.create');
        Route::post('/manajemen-akun', [ManajemenAkunController::class, 'tambahAkun'])->name('akun.store');
        Route::get('/manajemen-akun/{user}/edit-akun', [ManajemenAkunController::class, 'edit'])->name('akun.edit');
        Route::put('/manajemen-akun/{user}', [ManajemenAkunController::class, 'update'])->name('akun.update');
        Route::delete('/manajemen-akun/{id}', [ManajemenAkunController::class, 'destroy'])->name('akun.destroy');
        Route::patch('/manajemen-akun/{user}/reset-password', [ManajemenAkunController::class, 'resetPasswordById'])->name('akun.reset-password');
        Route::post('/manajemen-akun/upload-accounts', [ManajemenAkunController::class, 'import'])->name('akun.import');
        Route::get('/manajemen-logbook/generate-rekapan', [ManajemenLogbookController::class, 'generateRekapan'])->name('logbook.generate-rekapan');

        Route::get('/manajemen-logbook/kategori-pelayanan-kesehatan', [ManajemenLogbookController::class, 'kategoriPelayananKesehatan'])->name('logbook.kategori.index');
        Route::post('/manajemen-logbook/kategori-pelayanan-kesehatan', [ManajemenLogbookController::class, 'storeKategori'])->name('logbook.kategori.store');
        Route::delete('/manajemen-logbook/kategori-pelayanan-kesehatan/{kategoriPelayananKesehatan}', [ManajemenLogbookController::class, 'destroyKategori'])->name('logbook.kategori.destroy');
    });

    Route::middleware(['role:dokter_magang,pembimbing_lapangan'])->group(function () {
        Route::get('/manajemen-akun/ubah-data', [ManajemenAkunController::class, 'editData'])->name('akun.ubah-data.edit');
        Route::put('/manajemen-akun/{user}/ubah-data', [ManajemenAkunController::class, 'updateData'])->name('akun.ubah-data.update');

        Route::get('/manajemen-akun/ganti-password', [GantiPasswordController::class, 'index'])->name('akun.password.index');
        Route::post('/manajemen-akun/ganti-password', [GantiPasswordController::class, 'changePassword'])->name('akun.password.update');
    });

    Route::middleware(['role:dokter_magang'])->group(function () {
        Route::post('/manajemen-logbook', [ManajemenLogbookController::class, 'store'])->name('logbook.store');
        Route::get('/manajemen-logbook/tambah-log', [ManajemenLogbookController::class, 'create'])->name('logbook.create');
    });

    Route::middleware(['role:admin,dokter_magang,pembimbing_lapangan'])->group(function () {
        Route::get('/manajemen-logbook/{logMagang}/edit-log', [ManajemenLogbookController::class, 'edit'])->name('logbook.edit');
        Route::get('/manajemen-logbook/{logMagang}/detail-log', [ManajemenLogbookController::class, 'edit'])->name('logbook.detail');
        Route::put('/manajemen-logbook/{id}', [ManajemenLogbookController::class, 'update'])->name('logbook.update');
        Route::delete('/manajemen-logbook/{id}', [ManajemenLogbookController::class, 'destroy'])->name('logbook.destroy');
    });

    Route::middleware(['role:pembimbing_lapangan'])->group(function () {
        Route::get('/manajemen-akun/manajemen-ubah-data', [VerifikasiUbahDataController::class, 'index'])->name('ubah-data.queue');
        Route::get('/manajemen-akun/manajemen-ubah-data/{dokterMagang}/verifikasi-ubah-data', [VerifikasiUbahDataController::class, 'show'])->name('ubah-data.show');
        Route::patch('/manajemen-akun/manajemen-ubah-data/{dokterMagang}/verifikasi-ubah-data', [VerifikasiUbahDataController::class, 'verify'])->name('ubah-data.verify');
        Route::patch('/manajemen-akun/manajemen-ubah-data/{dokterMagang}/tolak-ubah-data', [VerifikasiUbahDataController::class, 'reject'])->name('ubah-data.reject');

        Route::patch('/manajemen-logbook/{logMagang}/verifikasi-log', [ManajemenLogbookController::class, 'verifikasiLog'])->name('logbook.verify');
        Route::patch('/manajemen-logbook/{logMagang}/minta-revisi-log', [ManajemenLogbookController::class, 'mintaRevisiLog'])->name('logbook.revise');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
