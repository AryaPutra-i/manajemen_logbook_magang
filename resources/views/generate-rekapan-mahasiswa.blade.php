<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3">
        @session('status')
            <div class="alert alert-success">{{ $value }}</div>
        @endsession

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2">
            <h2>Rekapan Log Mahasiswa</h2>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer text-white"></i> Cetak rekapan
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Nama mahasiswa</th><td>{{ $user->nama ?? '-' }}</td></tr>
                    <tr><th>Username</th><td>{{ $user->username }}</td></tr>
                    <tr><th>NIM</th><td>{{ $user->nim ?? '-' }}</td></tr>
                    <tr><th>Universitas</th><td>{{ $user->universitas ?? '-' }}</td></tr>
                    <tr><th>Departemen</th><td>{{ $user->departemen?->nama ?? '-' }}</td></tr>
                    <tr><th>Periode</th><td>{{ $tanggalLogMulai->isoFormat('YYYY-MM-DD') }} — {{ $tanggalLogAkhir->isoFormat('YYYY-MM-DD') }}</td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table" id="rekapan-log-mahasiswa">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama kegiatan</th>
                        <th>Deskripsi</th>
                        <th>Dokumentasi</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @forelse ($daftarLog as $logMahasiswa)
                        <tr>
                            <td>{{ $logMahasiswa->tanggal->isoFormat('YYYY-MM-DD') }}</td>
                            <td>{{ $logMahasiswa->nama_kegiatan }}</td>
                            <td>{{ $logMahasiswa->deskripsi }}</td>
                            <td>
                                @if ($logMahasiswa->dokumentasi)
                                    <a target="_blank" rel="noopener"
                                        href="{{ asset('storage/' . $logMahasiswa->dokumentasi) }}">Lihat dokumentasi</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada log selesai pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-home-layout>
