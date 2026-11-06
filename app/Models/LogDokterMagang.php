<?php

namespace App\Models;

use Database\Factories\LogDokterMagangFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('jumlah_pasien_per_kategori_pelayanan_kesehatan', 'tanggal', 'unit_kerja_id', 'dokter_magang_id', 'deskripsi', 'hambatan', 'dokumentasi', 'status_verifikasi', 'pembimbing_lapangan_id', 'departemen_id')]
class LogDokterMagang extends Model
{
    /** @use HasFactory<LogDokterMagangFactory> */
    use HasFactory;

    protected $casts = [
        'jumlah_pasien_per_kategori_pelayanan_kesehatan' => 'array',
        'tanggal' => 'date',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function dokterMagang()
    {
        return $this->belongsTo(User::class, 'dokter_magang_id');
    }

    public function pembimbingLapangan()
    {
        return $this->belongsTo(User::class, 'pembimbing_lapangan_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
}
