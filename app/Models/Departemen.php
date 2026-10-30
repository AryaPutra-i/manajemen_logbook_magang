<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['nama'])]
class Departemen extends Model
{
    use HasFactory;
    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id');
    }

    public function daftarLogMahasiswaMagang()
    {
        return $this->hasMany(LogMahasiswaMagang::class, 'departemen_id');
    }
}
