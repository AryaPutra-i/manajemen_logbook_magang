<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama'])]
class Departemen extends Model
{
    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id');
    }

    public function daftarLogMahasiswaMagang()
    {
        return $this->hasMany(LogMahasiswaMagang::class, 'departemen_id');
    }
}
