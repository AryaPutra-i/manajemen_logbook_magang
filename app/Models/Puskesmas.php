<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'alamat', 'kepala_upt', 'nomor_telepon', 'email', 'kode_puskesmas'])]
class Puskesmas extends Model
{
    public function users()
    {
        return $this->hasMany(User::class, 'puskesmas_id');
    }

    public function daftarLogDokterMagang()
    {
        return $this->hasMany(LogDokterMagang::class, 'puskesmas_id');
    }
}
