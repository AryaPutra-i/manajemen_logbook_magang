<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3" style="max-width: 768px;">
        @session('status')
            <div class="alert alert-warning">{{ $value }}</div>
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

        <h2>Verifikasi Ubah Data Mahasiswa</h2>
        @unless ($dataBaru)
            <div class="alert alert-danger">
                Status mahasiswa masih pending, tetapi data pengajuannya tidak tersimpan. Perubahan ini belum dapat
                diverifikasi. Minta mahasiswa mengirim ulang data dari halaman Ubah Data, atau gunakan tombol
                “Tolak / Reset” untuk menghapus status pending agar mahasiswa dapat mengajukan kembali.
            </div>
        @endunless
        <div class="row align-items-center g-2 mb-3">
            <label class="col-lg-3 col-form-label">Username</label>
            <div class="col-lg-9"><input class="form-control" value="{{ $mahasiswaMagang->username }}" disabled></div>
        </div>
        <div class="row align-items-center g-2 mb-3">
            <label class="col-lg-3 col-form-label">Departemen</label>
            <div class="col-lg-9"><input class="form-control"
                    value="{{ $mahasiswaMagang->departemen?->nama ?? '-' }}" disabled></div>
        </div>

        @php
            $fields = [
                'Nama' => 'nama',
                'NIK' => 'nik',
                'NIM' => 'nim',
                'Alamat' => 'alamat',
                'Universitas' => 'universitas',
            ];
        @endphp
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Data</th><th>Saat ini</th><th>Pengajuan</th></tr>
                </thead>
                <tbody>
                    @foreach ($fields as $label => $field)
                        <tr>
                            <th>{{ $label }}</th>
                            <td>{{ $mahasiswaMagang->{$field} ?? '-' }}</td>
                            <td>{{ $dataBaru?->{$field} ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-lg-row justify-content-lg-end gap-2">
            <a href="{{ route('ubah-data-mahasiswa.queue') }}" class="btn btn-secondary">Kembali</a>
            <form method="POST"
                action="{{ route('ubah-data-mahasiswa.reject', ['mahasiswaMagang' => $mahasiswaMagang->id]) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-danger" type="submit">
                    {{ $dataBaru ? 'Tolak perubahan' : 'Tolak / Reset status' }}
                </button>
            </form>
            <form method="POST"
                action="{{ route('ubah-data-mahasiswa.verify', ['mahasiswaMagang' => $mahasiswaMagang->id]) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-success" type="submit" @disabled(!$dataBaru)>Verifikasi perubahan</button>
            </form>
        </div>
    </section>
</x-home-layout>
