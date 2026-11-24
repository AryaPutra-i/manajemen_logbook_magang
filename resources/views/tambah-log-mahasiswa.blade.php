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

        <form id="formTambahLogMahasiswa" method="POST" action="{{ route('logbook.mahasiswa.store') }}" enctype="multipart/form-data"
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

            <div class="row align-items-start g-2 mb-3">
                <label class="col-lg-3 col-form-label" for="editor-deskripsi">Deskripsi</label>
                <div class="col-lg-9">
                    <div id="editor-deskripsi">{!! old('deskripsi') !!}</div>
                    <input type="hidden" name="deskripsi" id="deskripsi" value="{{ old('deskripsi') }}">
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

            const formTambahLog = document.querySelector('#formTambahLogMahasiswa');
            const deskripsiInput = document.querySelector('#deskripsi');

            formTambahLog.addEventListener('submit', function(e) {
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
</x-home-layout>
