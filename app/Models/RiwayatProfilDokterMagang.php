<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('dokter_magang_id', 'nama', 'nik', 'nim', 'alamat', 'universitas')]
class RiwayatProfilDokterMagang extends Model
{
    public function dokterMagang()
    {
        return $this->belongsTo(User::class, 'dokter_magang_id');
    }
}
