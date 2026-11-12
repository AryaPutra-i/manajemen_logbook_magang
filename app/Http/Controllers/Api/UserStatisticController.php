<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserStatisticController extends Controller
{
    /**
     * get total dokter magang
     */
    public function totalDokterMagang():JsonResponse
    {
        $total = User::where('role', 'dokter_magang')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total dokter magang' => $total
            ]
        ]);
    }

    /**
     * get total mahasiswa magang
     */
    public function totalMahasiswaMagang(): JsonResponse
    {
        $total = User::where('role', 'mahasiswa_magang')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total mahasiswa magang' => $total
            ]
        ]);
    }

    /**
     * get total pemagang (mahasiswa + dokter magang)
     */
    public function totalPemagang(): JsonResponse
    {
        $total = User::whereIn('role', ['dokter_magang', 'mahasiswa_magang'])->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total pemagang' => $total
            ]
        ]);
    }
}
