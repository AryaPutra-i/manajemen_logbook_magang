<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'jenis', 'alamat', 'kepala_upt', 'nomor_telepon', 'email', 'kode_puskesmas'])]
class UnitKerja extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class, 'unit_kerja_id');
    }

    public function departemens()
    {
        return $this->hasMany(Departemen::class, 'unit_kerja_id');
    }

    public function daftarLogDokterMagang()
    {
        return $this->hasMany(LogDokterMagang::class, 'unit_kerja_id');
    }

    public function daftarLogMahasiswaMagang()
    {
        return $this->hasMany(LogMahasiswaMagang::class, 'unit_kerja_id');
    }
}
