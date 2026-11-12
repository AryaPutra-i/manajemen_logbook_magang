<?php

use App\Http\Controllers\Api\UserStatisticController;
use Illuminate\Support\Facades\Route;

Route::prefix('statistics/pemagang')->controller(UserStatisticController::class)->group(function () {
    Route::get('/', 'totalPemagang');
    Route::get('/dokter', 'totalDokterMagang');
    Route::get('/mahasiswa', 'totalMahasiswaMagang');
});
