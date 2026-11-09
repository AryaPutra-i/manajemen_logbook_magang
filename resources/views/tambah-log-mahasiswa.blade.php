<x-home-layout>
    @php
        $mahasiswa = auth()->user();
        $lokasiMagang = implode(' - ', array_filter([$mahasiswa->unitKerja?->nama, $mahasiswa->departemen?->nama]));
        $hasLokasiMagang = $mahasiswa->unit_kerja_id !== null;
    @endphp
    <section class="p-4 w-100 d-flex flex-column gap-3" style="max-width: 768px;">
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

        @unless ($hasLokasiMagang)
            <div class="alert alert-warning">
                Akun mahasiswa belum memiliki lokasi magang. Hubungi admin sebelum menambahkan log.
            </div>
        @endunless

        <form method="POST" action="{{ route('logbook.mahasiswa.store') }}" enctype="multipart/form-data"
            class="d-flex flex-column">
            @csrf
            <i class="bi bi-journal-text my-4 align-self-center" style="font-size: 96px;"></i>

            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="lokasiMagang">Lokasi magang</label>
                <div class="col-lg-9">
                    <input class="form-control" id="lokasiMagang" value="{{ $lokasiMagang ?? '-' }}" disabled>
                </div>
            </div>

            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="namaKegiatan">Nama kegiatan</label>
                <div class="col-lg-9">
                    <input class="form-control" value="{{ old('namaKegiatan') }}" type="text" name="namaKegiatan"
                        id="namaKegiatan" required>
                </div>
            </div>

            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="tanggal">Tanggal</label>
                <div class="col-lg-9">
                    <input class="form-control" value="{{ old('tanggal', today()->toDateString()) }}" type="date"
                        name="tanggal" id="tanggal" min="{{ today()->subDays(6)->toDateString() }}"
                        max="{{ today()->toDateString() }}" required>
                </div>
            </div>

            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="deskripsi">Deskripsi</label>
                <div class="col-lg-9">
                    <textarea class="form-control" name="deskripsi" id="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="dokumentasi">Dokumentasi</label>
                <div class="col-lg-9">
                    <input class="form-control" type="file" name="dokumentasi" id="dokumentasi"
                        accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                    <small class="form-text">PDF, JPG, JPEG, PNG, atau WEBP; maksimal 2 MB.</small>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-lg-end gap-2">
                <a href="{{ route('logbook.index') }}" class="btn btn-secondary">Kembali</a>
                <button class="btn btn-primary" type="submit" @disabled(!$hasLokasiMagang)>Tambah log</button>
            </div>
        </form>
    </section>
</x-home-layout>
