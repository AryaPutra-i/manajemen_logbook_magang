<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'unit_kerja_id'])]
class Departemen extends Model
{
    use HasFactory;

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id');
    }

    public function daftarLogMahasiswaMagang()
    {
        return $this->hasMany(LogMahasiswaMagang::class, 'departemen_id');
    }

    public function daftarLogDokterMagang()
    {
        return $this->hasMany(LogDokterMagang::class, 'departemen_id');
    }
}
