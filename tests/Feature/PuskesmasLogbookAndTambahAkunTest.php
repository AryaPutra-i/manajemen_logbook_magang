<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\LogDokterMagang;
use App\Models\LogMahasiswaMagang;
use App\Models\UnitKerja;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PuskesmasLogbookAndTambahAkunTest extends TestCase
{
    use RefreshDatabase;

    private UnitKerja $puskesmasA;
    private UnitKerja $puskesmasB;
    private UnitKerja $dinkes;
    private Departemen $departemenA;
    private User $admin;
    private User $adminUnitA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->puskesmasA = UnitKerja::create([
            'nama' => 'Puskesmas Melati',
            'jenis' => 'Puskesmas',
            'alamat' => 'Jl. Melati No. 1',
            'kepala_upt' => 'Dr. Melati',
            'nomor_telepon' => '081234567890',
            'email' => 'melati@puskesmas.id',
            'kode_puskesmas' => 'P001',
        ]);

        $this->puskesmasB = UnitKerja::create([
            'nama' => 'Puskesmas Mawar',
            'jenis' => 'Puskesmas',
            'alamat' => 'Jl. Mawar No. 2',
            'kepala_upt' => 'Dr. Mawar',
            'nomor_telepon' => '081234567891',
            'email' => 'mawar@puskesmas.id',
            'kode_puskesmas' => 'P002',
        ]);

        $this->dinkes = UnitKerja::create([
            'nama' => 'Dinas Kesehatan Kota',
            'jenis' => 'Dinas_Kesehatan',
            'alamat' => 'Jl. Kesehatan No. 3',
            'kepala_upt' => 'Dr. Dinkes',
            'nomor_telepon' => '081234567892',
            'email' => 'dinkes@kota.go.id',
            'kode_puskesmas' => 'D001',
        ]);

        $this->departemenA = Departemen::create([
            'nama' => 'Poli Umum',
            'unit_kerja_id' => $this->puskesmasA->id,
        ]);

        $this->admin = User::create([
            'username' => 'superadmin',
            'password' => bcrypt('password123'),
            'role' => UserRole::Admin->value,
            'nama' => 'Super Admin',
        ]);

        $this->adminUnitA = User::create([
            'username' => 'admin.melati',
            'password' => bcrypt('password123'),
            'role' => UserRole::AdminUnitKerja->value,
            'nama' => 'Admin Puskesmas Melati',
            'unit_kerja_id' => $this->puskesmasA->id,
        ]);
    }

    public function test_tambah_akun_page_only_provides_puskesmas_unit_kerja(): void
    {
        $response = $this->actingAs($this->admin)->get(route('akun.create'));
        $response->assertStatus(200);
        $response->assertSee('Puskesmas Melati');
        $response->assertSee('Puskesmas Mawar');
        $response->assertDontSee('Dinas Kesehatan Kota');
        $response->assertDontSee('Admin Unit Kerja</option>', false);
        $response->assertSee('Dokter Magang</option>', false);
        $response->assertSee('Pembimbing Lapangan</option>', false);
        $response->assertSee('Mahasiswa Magang</option>', false);
    }

    public function test_tambah_akun_fails_when_role_is_not_allowed(): void
    {
        $response = $this->actingAs($this->admin)->post(route('akun.store'), [
            'role' => UserRole::AdminUnitKerja->value,
            'nama' => 'Admin Baru',
            'jenisUnitKerja' => 'Puskesmas',
            'namaUnitKerja' => $this->puskesmasA->nama,
        ]);

        $response->assertSessionHasErrors(['role']);

        $responseAdmin = $this->actingAs($this->admin)->post(route('akun.store'), [
            'role' => UserRole::Admin->value,
            'nama' => 'Admin Super Baru',
            'jenisUnitKerja' => 'Puskesmas',
            'namaUnitKerja' => $this->puskesmasA->nama,
        ]);

        $responseAdmin->assertSessionHasErrors(['role']);
    }

    public function test_tambah_akun_fails_when_jenis_unit_kerja_is_not_puskesmas(): void
    {
        $response = $this->actingAs($this->admin)->post(route('akun.store'), [
            'role' => UserRole::DokterMagang->value,
            'nama' => 'Dokter Baru',
            'jenisUnitKerja' => 'Dinas_Kesehatan',
            'namaUnitKerja' => $this->dinkes->nama,
        ]);

        $response->assertSessionHasErrors(['jenisUnitKerja']);
    }

    public function test_tambah_akun_succeeds_for_dokter_magang_in_puskesmas(): void
    {
        $response = $this->actingAs($this->admin)->post(route('akun.store'), [
            'role' => UserRole::DokterMagang->value,
            'nama' => 'Dokter Budi Santoso',
            'jenisUnitKerja' => 'Puskesmas',
            'namaUnitKerja' => $this->puskesmasA->nama,
        ]);

        $response->assertRedirect(route('akun.menu'));
        $this->assertDatabaseHas('users', [
            'role' => UserRole::DokterMagang->value,
            'unit_kerja_id' => $this->puskesmasA->id,
        ]);
    }

    public function test_tambah_akun_succeeds_for_pembimbing_lapangan_in_puskesmas(): void
    {
        $response = $this->actingAs($this->admin)->post(route('akun.store'), [
            'role' => UserRole::PembimbingLapangan->value,
            'nama' => 'Pembimbing Siti',
            'jenisUnitKerja' => 'Puskesmas',
            'namaUnitKerja' => $this->puskesmasA->nama,
            'nip' => '198501012010011001',
        ]);

        $response->assertRedirect(route('akun.menu'));
        $this->assertDatabaseHas('users', [
            'role' => UserRole::PembimbingLapangan->value,
            'unit_kerja_id' => $this->puskesmasA->id,
        ]);
    }

    public function test_tambah_akun_succeeds_for_mahasiswa_magang_in_puskesmas(): void
    {
        $response = $this->actingAs($this->admin)->post(route('akun.store'), [
            'role' => UserRole::MahasiswaMagang->value,
            'nama' => 'Mahasiswa Andi',
            'jenisUnitKerja' => 'Puskesmas',
            'namaUnitKerja' => $this->puskesmasA->nama,
            'namaDepartemen' => $this->departemenA->nama,
        ]);

        $response->assertRedirect(route('akun.menu'));
        $this->assertDatabaseHas('users', [
            'role' => UserRole::MahasiswaMagang->value,
            'unit_kerja_id' => $this->puskesmasA->id,
            'departemen_id' => $this->departemenA->id,
        ]);
    }

    public function test_admin_unit_kerja_can_access_manajemen_logbook_and_see_own_puskesmas_logs(): void
    {
        $dokterA = User::create([
            'username' => 'dokter.a',
            'password' => bcrypt('password123'),
            'role' => UserRole::DokterMagang->value,
            'nama' => 'Dokter Melati',
            'unit_kerja_id' => $this->puskesmasA->id,
        ]);

        $dokterB = User::create([
            'username' => 'dokter.b',
            'password' => bcrypt('password123'),
            'role' => UserRole::DokterMagang->value,
            'nama' => 'Dokter Mawar',
            'unit_kerja_id' => $this->puskesmasB->id,
        ]);

        $mahasiswaA = User::create([
            'username' => 'mhs.a',
            'password' => bcrypt('password123'),
            'role' => UserRole::MahasiswaMagang->value,
            'nama' => 'Mahasiswa Melati',
            'unit_kerja_id' => $this->puskesmasA->id,
            'departemen_id' => $this->departemenA->id,
        ]);

        $logDokterA = LogDokterMagang::create([
            'tanggal' => Carbon::today(),
            'jumlah_pasien_per_kategori_pelayanan_kesehatan' => ['Umum' => 5],
            'deskripsi' => 'Pemeriksaan pasien di Puskesmas Melati',
            'hambatan' => 'Tidak ada',
            'dokumentasi' => 'uploads/test.png',
            'status_verifikasi' => 'selesai',
            'unit_kerja_id' => $this->puskesmasA->id,
            'dokter_magang_id' => $dokterA->id,
        ]);

        $logDokterB = LogDokterMagang::create([
            'tanggal' => Carbon::today(),
            'jumlah_pasien_per_kategori_pelayanan_kesehatan' => ['Umum' => 10],
            'deskripsi' => 'Pemeriksaan pasien di Puskesmas Mawar',
            'hambatan' => 'Tidak ada',
            'dokumentasi' => 'uploads/test.png',
            'status_verifikasi' => 'selesai',
            'unit_kerja_id' => $this->puskesmasB->id,
            'dokter_magang_id' => $dokterB->id,
        ]);

        $logMahasiswaA = LogMahasiswaMagang::create([
            'tanggal' => Carbon::today(),
            'nama_kegiatan' => 'Kegiatan Magang Melati',
            'deskripsi' => 'Melakukan pencatatan rekam medis Melati',
            'dokumentasi' => 'uploads/test.png',
            'status_verifikasi' => 'selesai',
            'unit_kerja_id' => $this->puskesmasA->id,
            'departemen_id' => $this->departemenA->id,
            'mahasiswa_magang_id' => $mahasiswaA->id,
        ]);

        $response = $this->actingAs($this->adminUnitA)->get(route('logbook.index'));

        $response->assertStatus(200);
        $response->assertSee('Log Dokter Magang');
        $response->assertSee('Log Mahasiswa Magang');
        $response->assertSee('Rekap dokter');
        $response->assertSee('Rekap mahasiswa');
        $response->assertSee('Pemeriksaan pasien di Puskesmas Melati');
        $response->assertDontSee('Pemeriksaan pasien di Puskesmas Mawar');
        $response->assertSee('Kegiatan Magang Melati');
    }
}
