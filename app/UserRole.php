<?php

namespace App;

enum UserRole: string
{
    case Admin = 'admin';
    case AdminUnitKerja = 'admin_unit_kerja';
    case PembimbingLapangan = 'pembimbing_lapangan';
    case DokterMagang = 'dokter_magang';
    case MahasiswaMagang = 'mahasiswa_magang';
}
