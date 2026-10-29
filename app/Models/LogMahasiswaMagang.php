<?php

namespace App\Models;

use Database\Factories\LogMahasiswaMagangFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable('nama_kegiatan', 'tanggal', 'deskripsi', 'mahasiswa_magang_id', 'departemen_id', 'dokumentasi', 'status_verifikasi')]
class LogMahasiswaMagang extends Model
{
    /** @use HasFactory<LogMahasiswaMagangFactory> */
    use HasFactory;

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function mahasiswaMagang() {
        return $this->belongsTo(User::class, 'mahasiswa_magang_id');
    }

    public function pembimbingLapanganMahasiswa()
    {
        return $this->belongsTo(User::class, 'pembimbing_lapangan_mahasiswa_id');
    }
}
