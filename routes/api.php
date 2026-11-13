<?php

use App\Http\Controllers\Api\UserStatisticController;
use App\Http\Middleware\AuthenticateOnceWithBasicAuth;
use Illuminate\Support\Facades\Route;

Route::middleware(AuthenticateOnceWithBasicAuth::class)->prefix('statistics/pemagang')->controller(UserStatisticController::class)->group(function () {
    Route::get('/', 'totalPemagang');
    Route::get('/dokter', 'totalDokterMagang');
    Route::get('/mahasiswa', 'totalMahasiswaMagang');
});
