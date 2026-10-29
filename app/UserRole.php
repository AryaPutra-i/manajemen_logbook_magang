<?php

namespace App;

enum UserRole: string
{
    case Admin = 'admin';
    case PembimbingLapangan = 'pembimbing_lapangan';
    case PembimbingLapanganMahasiswa = 'pembimbing_lapangan_mahasiswa';
    case DokterMagang = 'dokter_magang';
    case MahasiswaMagang = 'mahasiswa_magang';
}
