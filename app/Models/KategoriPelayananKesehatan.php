<?php

namespace App\Models;

use Database\Factories\KategoriPelayananKesehatanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPelayananKesehatan extends Model
{
    /** @use HasFactory<KategoriPelayananKesehatanFactory> */
    use HasFactory;

    protected $fillable = ['nama'];
}
