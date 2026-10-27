@php
    $oldDaftarKategoriPelayananKesehatan = old('kategoriPelayananKesehatan');
    $oldDaftarJumlahPasien = old('jumlahPasien');
@endphp

<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('logbook.store') }}" enctype="multipart/form-data" class="row">
            @csrf

            <div class="px-0 col-lg-6 border-bottom border-end">
                <div class="row gx-3 pb-3">
                    <div class="col-7 d-flex align-items-end">
                        <label for="kategoriPelayananKesehatan-0">Kategori Pelayanan Kesehatan</label>
                    </div>
                    <div class="col-3">
                        <label for="jumlahPasien[]">Jumlah Pasien</label>
                    </div>
                    <div class="col-2">
                    </div>
                </div>

                <div class="row gx-3">
                    <div id="kategoriPelayananKesehatanColumn" class="d-flex flex-column gap-3 col-7">
                        @if ($oldDaftarKategoriPelayananKesehatan)
                            @foreach ($oldDaftarKategoriPelayananKesehatan as $i => $namaKategoriPelayananKesehatan)
                                <select required class="form-control" name="kategoriPelayananKesehatan[]"
                                    id="kategoriPelayananKesehatan-{{ $i }}">
                                    <option disabled value>-- Pilih Kategori Pelayanan Kesehatan --</option>
                                    @foreach ($daftarKategoriPelayananKesehatan as $kategoriPelayananKesehatan)
                                        <option @selected($namaKategoriPelayananKesehatan === $kategoriPelayananKesehatan->nama)
                                            value="{{ $kategoriPelayananKesehatan->nama }}">
                                            {{ $kategoriPelayananKesehatan->nama }}</option>
                                    @endforeach
                                </select>
                            @endforeach
                        @else
                            <select required class="form-control" name="kategoriPelayananKesehatan[]"
                                id="kategoriPelayananKesehatan-0">
                                <option selected disabled value>-- Pilih Kategori Pelayanan Kesehatan --</option>
                                @foreach ($daftarKategoriPelayananKesehatan as $kategoriPelayananKesehatan)
                                    <option value="{{ $kategoriPelayananKesehatan->nama }}">
                                        {{ $kategoriPelayananKesehatan->nama }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div id="jumlahPasienColumn" class="d-flex flex-column gap-3 col-3">
                        @if ($oldDaftarJumlahPasien)
                            @foreach ($oldDaftarJumlahPasien as $i => $jumlahPasien)
                                <input value="{{ $jumlahPasien }}" required class="form-control" type="number"
                                    min="1" name="jumlahPasien[]" id="jumlahPasien-{{ $i }}">
                            @endforeach
                        @else
                            <input required class="form-control" type="number" min="1" name="jumlahPasien[]"
                                id="jumlahPasien-0">
                        @endif

                    </div>

                    <div id="deleteKategoriColumn"
                        class="col-2 d-flex flex-column justify-content-between align-items-center">
                        @if ($oldDaftarJumlahPasien)
                            @for ($i = 0; $i < count($oldDaftarJumlahPasien); $i++)
                                <button class="btn btn-danger px-2 py-1" type="button"
                                    id="deleteKategori-{{ $i }}"><i class="bi bi-trash-fill"
                                        style="color: white;"
                                        onclick="handleRemoveCategory({{ $i }})"></i></button>
                            @endfor
                        @else
                            <button class="btn btn-danger px-2 py-1" type="button" id="deleteKategori-0"><i
                                    class="bi bi-trash-fill" style="color: white;"
                                    onclick="handleRemoveCategory(0)"></i></button>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" onclick="handleAddCategory()" class="btn btn-link">
                        Tambah Kategori
                        <i class="ms-2 align-items-center bi bi-plus-circle"></i>
                    </button>
                </div>
            </div>

            <div class="col-lg-6">
                <label class="mb-1" for="tanggal">Tanggal</label>
                <div class="mb-3 input-group">
                    <input value="{{ old('tanggal') }}" required class="form-control" type="date"
                        onclick="this.showPicker()" name="tanggal" id="tanggal" placeholder="Pilih tanggal kegiatan">
                </div>

                <label class="mb-1" for="deskripsi">Deskripsi</label>
                <div class="mb-3 input-group">
                    <textarea required class="form-control" name="deskripsi" id="deskripsi"
                        placeholder="Tuliskan deskripsi singkat mengenai kegiatan" style="height: 96px;">{{ @old('deskripsi') }}</textarea>
                </div>

                <label class="mb-1" for="hambatan">Hambatan</label>
                <div class="mb-3 input-group">
                    <textarea required class="form-control" name="hambatan" id="hambatan"
                        placeholder="Tuliskan hambatan yang dihadapi saat kegiatan" style="height: 96px;">{{ @old('hambatan') }}</textarea>
                </div>

                <label class="mb-1" for="dokumentasi">Dokumentasi</label>
                <div class="mb-3 input-group">
                    <input required class="form-control" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp"
                        name="dokumentasi" id="dokumentasi">
                </div>


                <button class="btn btn-primary w-100 mt-3" type="submit">Tambah</button>
            </div>
        </form>
    </section>

    @push('scripts')
        <script>
            let count = {{ $oldDaftarJumlahPasien ? count($oldDaftarJumlahPasien) : 1 }};
            const kategoriPelayananKesehatanColumn = document.querySelector('#kategoriPelayananKesehatanColumn');
            const jumlahPasienColumn = document.querySelector('#jumlahPasienColumn');
            const deleteKategoriColumn = document.querySelector('#deleteKategoriColumn');

            function handleAddCategory() {
                kategoriPelayananKesehatanColumn.insertAdjacentHTML('beforeend', `
                    <select value="{{ $namaKategoriPelayananKesehatan }}" required class="form-control"
                        name="kategoriPelayananKesehatan[]" id="kategoriPelayananKesehatan-${count}">
                        <option selected disabled value>-- Pilih Kategori Pelayanan Kesehatan --</option>
                        @foreach ($daftarKategoriPelayananKesehatan as $kategoriPelayananKesehatan)
                            <option value="{{ $kategoriPelayananKesehatan->nama }}">{{ $kategoriPelayananKesehatan->nama }}</option>
                        @endforeach
                    </select>
                `);

                jumlahPasienColumn.insertAdjacentHTML('beforeend', `
                    <input required class="form-control" type="number" min="1" name="jumlahPasien[]" id="jumlahPasien-${count}">
                `);

                deleteKategoriColumn.insertAdjacentHTML('beforeend', `
                    <button class="btn btn-danger px-2 py-1" type="button" id="deleteKategori-${count}">
                        <i class="bi bi-trash-fill" style="color: white;" onclick="handleRemoveCategory(${count})"></i>
                    </button>
                `);

                count += 1;
            }

            function handleRemoveCategory(index) {
                document.querySelector(`#kategoriPelayananKesehatan-${index}`).remove();
                document.querySelector(`#jumlahPasien-${index}`).remove();
                document.querySelector(`#deleteKategori-${index}`).remove();
            }
        </script>
    @endpush
</x-home-layout>
