<?php

namespace App;

enum UserRole: string
{
    case Admin = 'admin';
    case PembimbingLapanganPuskesmas = 'pembimbing_lapangan_puskesmas';
    case PembimbingLapanganDepartemen = 'pembimbing_lapangan_departemen';
    case DokterMagang = 'dokter_magang';
    case MahasiswaMagang = 'mahasiswa_magang';
}
