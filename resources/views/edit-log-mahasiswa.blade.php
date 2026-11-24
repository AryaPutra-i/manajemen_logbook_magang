<x-home-layout>
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

        <i class="bi bi-journal-text my-4 align-self-center" style="font-size: 96px;"></i>

        <div class="row align-items-center g-2 mb-3">
            <label class="col-lg-3 col-form-label" for="unitMagang">Unit magang</label>
            <div class="col-lg-9">
                <input class="form-control" id="unitMagang"
                    value="{{ implode(' - ', array_filter([$logMahasiswa->unitKerja?->nama, $logMahasiswa->departemen?->nama])) ?: '-' }}"
                    disabled>
            </div>
        </div>
        <div class="row align-items-center g-2 mb-3">
            <label class="col-lg-3 col-form-label" for="pembimbing">Pembimbing</label>
            <div class="col-lg-9">
                <input class="form-control" id="pembimbing"
                    value="{{ $logMahasiswa->pembimbingLapangan?->username ?? '-' }}" disabled>
            </div>
        </div>
        <div class="row align-items-center g-2 mb-3">
            <label class="col-lg-3 col-form-label" for="mahasiswa">Mahasiswa</label>
            <div class="col-lg-9">
                <input class="form-control" id="mahasiswa"
                    value="{{ $logMahasiswa->mahasiswaMagang?->username ?? '-' }}" disabled>
            </div>
        </div>

        @if ($user->role === 'mahasiswa_magang')
            @php($isLogSelesai = $logMahasiswa->status_verifikasi === 'selesai')
            @if ($isLogSelesai)
                <div class="alert alert-warning">Log yang sudah selesai tidak dapat diubah atau dihapus.</div>
            @elseif ($logMahasiswa->status_verifikasi === 'revisi')
                <div class="alert alert-danger">
                    Log ini perlu diperbaiki sesuai revisi pembimbing. Simpan perubahan untuk mengirim ulang logbook.
                </div>
            @endif

            <form id="formEditLogMahasiswa" method="POST" action="{{ route('logbook.mahasiswa.update', ['id' => $logMahasiswa->id]) }}"
                enctype="multipart/form-data" class="d-flex flex-column">
                @csrf
                @method('PUT')

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label" for="tanggal">Tanggal</label>
                    <div class="col-lg-9">
                        <input class="form-control" value="{{ old('tanggal', $logMahasiswa->tanggal->toDateString()) }}"
                            type="date" name="tanggal" id="tanggal"
                            min="{{ today()->subDays(6)->toDateString() }}" max="{{ today()->toDateString() }}"
                            required @disabled($isLogSelesai)>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label" for="namaKegiatan">Nama kegiatan</label>
                    <div class="col-lg-9">
                        <input class="form-control" value="{{ old('namaKegiatan', $logMahasiswa->nama_kegiatan) }}"
                            type="text" name="namaKegiatan" id="namaKegiatan" required @disabled($isLogSelesai)>
                    </div>
                </div>

                <div class="row align-items-start g-2 mb-3">
                    <label class="col-lg-3 col-form-label" for="editor-deskripsi">Deskripsi</label>
                    <div class="col-lg-9">
                        @if ($isLogSelesai)
                            <div class="p-3 bg-white rounded border" style="min-height: 100px;">{!! $logMahasiswa->deskripsi !!}</div>
                        @else
                            <div id="editor-deskripsi">{!! old('deskripsi', $logMahasiswa->deskripsi) !!}</div>
                            <input type="hidden" name="deskripsi" id="deskripsi" value="{{ old('deskripsi', $logMahasiswa->deskripsi) }}">
                        @endif
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label" for="dokumentasi">Dokumentasi</label>
                    <div class="col-lg-9">
                        @if ($logMahasiswa->dokumentasi)
                            <p><a target="_blank" rel="noopener"
                                    href="{{ asset('storage/' . $logMahasiswa->dokumentasi) }}">Lihat dokumentasi saat ini</a>
                            </p>
                        @endif
                        <input class="form-control" type="file" name="dokumentasi" id="dokumentasi"
                            accept=".pdf,.jpg,.jpeg,.png,.webp" @disabled($isLogSelesai)>
                        <small class="form-text">Boleh dikosongkan jika dokumentasi tidak diganti. Maksimal 2 MB.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-lg-end gap-2">
                    <a href="{{ route('logbook.index') }}" class="btn btn-secondary">Kembali</a>
                    <button class="btn {{ $logMahasiswa->status_verifikasi === 'revisi' ? 'btn-danger' : 'btn-primary' }}"
                        type="submit" @disabled($isLogSelesai)>
                        {{ $logMahasiswa->status_verifikasi === 'revisi' ? 'Kirim ulang setelah revisi' : 'Simpan perubahan' }}
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('logbook.mahasiswa.destroy', ['id' => $logMahasiswa->id]) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit" @disabled($isLogSelesai)>Hapus log</button>
            </form>
        @else
            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="tanggal">Tanggal</label>
                <div class="col-lg-9">
                    <input class="form-control" id="tanggal" value="{{ $logMahasiswa->tanggal->toDateString() }}"
                        disabled>
                </div>
            </div>
            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="namaKegiatan">Nama kegiatan</label>
                <div class="col-lg-9">
                    <input class="form-control" id="namaKegiatan" value="{{ $logMahasiswa->nama_kegiatan }}" disabled>
                </div>
            </div>
            <div class="row align-items-start g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="deskripsi">Deskripsi</label>
                <div class="col-lg-9">
                    <div class="p-3 bg-white rounded border" style="min-height: 100px;">{!! $logMahasiswa->deskripsi !!}</div>
                </div>
            </div>
            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label">Dokumentasi</label>
                <div class="col-lg-9">
                    @if ($logMahasiswa->dokumentasi)
                        <a target="_blank" rel="noopener"
                            href="{{ asset('storage/' . $logMahasiswa->dokumentasi) }}">Lihat dokumentasi</a>
                    @else
                        <span>-</span>
                    @endif
                </div>
            </div>
            <div class="row align-items-center g-2 mb-3">
                <label class="col-lg-3 col-form-label">Status verifikasi</label>
                <div class="col-lg-9">
                    @switch($logMahasiswa->status_verifikasi)
                        @case('pending')
                            <span class="badge rounded-pill text-bg-warning">Pending</span>
                        @break
                        @case('revisi')
                            <span class="badge rounded-pill text-bg-danger">Revisi</span>
                        @break
                        @case('selesai')
                            <span class="badge rounded-pill text-bg-success">Selesai</span>
                        @break
                        @default
                            <span class="badge rounded-pill text-bg-secondary">{{ $logMahasiswa->status_verifikasi }}</span>
                    @endswitch
                </div>
            </div>
            <a href="{{ route('logbook.index') }}" class="btn btn-secondary align-self-lg-end">Kembali</a>
        @endif
    </section>

    @if ($user->role === 'mahasiswa_magang' && !$isLogSelesai)
        @push('styles')
            <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
            <style>
                .ql-toolbar.ql-snow {
                    background-color: #ffffff;
                    border-top-left-radius: 0.375rem;
                    border-top-right-radius: 0.375rem;
                    border-color: #dee2e6;
                }
                .ql-container.ql-snow {
                    background-color: #ffffff;
                    border-bottom-left-radius: 0.375rem;
                    border-bottom-right-radius: 0.375rem;
                    border-color: #dee2e6;
                    font-family: inherit;
                    font-size: 1rem;
                    min-height: 180px;
                }
            </style>
        @endpush

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
            <script>
                const quill = new Quill('#editor-deskripsi', {
                    theme: 'snow',
                    placeholder: 'Tuliskan deskripsi kegiatan...',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['blockquote', 'code-block'],
                            ['clean']
                        ]
                    }
                });

                const formEditLog = document.querySelector('#formEditLogMahasiswa');
                const deskripsiInput = document.querySelector('#deskripsi');

                formEditLog.addEventListener('submit', function(e) {
                    const text = quill.getText().trim();
                    const isEmpty = !text && (!quill.root.innerHTML || quill.root.innerHTML === '<p><br></p>');

                    if (isEmpty) {
                        deskripsiInput.value = '';
                        e.preventDefault();
                        quill.focus();
                        alert('Deskripsi wajib diisi.');
                        return false;
                    } else {
                        deskripsiInput.value = quill.root.innerHTML;
                    }
                });
            </script>
        @endpush
    @endif
</x-home-layout>
