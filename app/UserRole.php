<?php

namespace App;

enum UserRole: string
{
    case Admin = 'admin';
    case PembimbingLapangan = 'pembimbing_lapangan';
    case DokterMagang = 'dokter_magang';
}
