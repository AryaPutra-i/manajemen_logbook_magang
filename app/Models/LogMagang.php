<?php

namespace App\Models;

use Database\Factories\LogMagangFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('jumlah_pasien_per_kategori_pelayanan_kesehatan', 'tanggal', 'puskesmas_id', 'dokter_magang_id', 'deskripsi', 'hambatan', 'dokumentasi', 'status_verifikasi')]
class LogMagang extends Model
{
    /** @use HasFactory<LogMagangFactory> */
    use HasFactory;

    protected $casts = [
        'jumlah_pasien_per_kategori_pelayanan_kesehatan' => 'array',
        'tanggal' => 'date',
    ];

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id');
    }

    public function dokterMagang()
    {
        return $this->belongsTo(User::class, 'dokter_magang_id');
    }

    public function pembimbingLapangan()
    {
        return $this->belongsTo(User::class, 'pembimbing_lapangan_id');
    }
}
