<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'password', 'role', 'nama', 'nip', 'nik', 'nim', 'alamat', 'universitas', 'is_profile_change_pending', 'puskesmas_id', 'departemen_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'nama' => 'encrypted',
            'nip' => 'encrypted',
            'nik' => 'encrypted',
            'nim' => 'encrypted',
            'alamat' => 'encrypted',
            'universitas' => 'encrypted',
        ];
    }

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    public function daftarLogDokterMagang()
    {
        return $this->hasMany(LogDokterMagang::class, 'dokter_magang_id');
    }

    public function daftarLogMahasiswaMagang()
    {
        return $this->hasMany(LogMahasiswaMagang::class, 'mahasiswa_magang_id');
    }

    public function daftarLogDokterYangDiverifikasi()
    {
        return $this->hasMany(LogDokterMagang::class, 'pembimbing_lapangan_id');
    }

    public function daftarLogMahasiswaYangDiverifikasi()
    {
        return $this->hasMany(LogMahasiswaMagang::class, 'pembimbing_lapangan_mahasiswa_id');
    }

    public function daftarPerubahanDataDokterMagang()
    {
        return $this->hasMany(RiwayatProfilDokterMagang::class, 'dokter_magang_id');
    }

    public function daftarPerubahanDataMahasiswaMagang()
    {
        return $this->hasMany(RiwayatProfilMahasiswaMagang::class, 'mahasiswa_magang_id');
    }

    public static function generateUniqueUsername(string $nama): string
    {
        $nameParts = explode(' ', trim($nama));
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? end($nameParts) : '';
        $baseUsername = strtolower($firstName.($lastName ? '.'.$lastName : ''));

        $existing = static::where('username', $baseUsername)
            ->orWhere('username', 'like', $baseUsername.'%')
            ->pluck('username');

        if ($existing->isEmpty()) {
            return $baseUsername;
        }

        $max = 1;
        $baseExists = false;

        foreach ($existing as $value) {
            if ($value === $baseUsername) {
                $baseExists = true;

                continue;
            }

            if (preg_match('/^'.preg_quote($baseUsername, '/').'(\d+)$/', $value, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $baseExists ? $baseUsername.($max + 1) : $baseUsername;
    }
}
