<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('mahasiswa_magang_id', 'nama', 'nik', 'nim', 'alamat', 'universitas')]
class RiwayatProfilMahasiswaMagang extends Model
{
    public function mahasiswaMagang()
    {
     return $this->belongsTo(User::class, 'mahasiswa_magang_id');
    }
}
