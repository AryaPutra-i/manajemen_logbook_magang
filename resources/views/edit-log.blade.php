<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3">
        @session('status')
            <div class="alert alert-success">
                {{ $value }}
            </div>
        @endsession

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="editLog" method="POST" action="{{ route('logbook.update', ['id' => $logMagang->id]) }}"
            enctype="multipart/form-data" class="row">
            @csrf
            @method('PUT')

            <div class="px-0 col-lg-6 border-bottom border-end">
                <div class="row gx-3 pb-3">
                    <div @class([
                        'col-7 d-flex align-items-end',
                        'col-9' =>
                            $user->role !== 'dokter_magang' ||
                            $logMagang->status_verifikasi === 'selesai',
                    ])>
                        <label for="kategoriPelayananKesehatan-0">Kategori Pelayanan Kesehatan</label>
                    </div>
                    <div class="col-3">
                        <label for="jumlahPasien[]">Jumlah Pasien</label>
                    </div>
                    @if ($user->role === 'dokter_magang')
                        <div class="col-2">
                        </div>
                    @endif
                </div>

                <div class="row gx-3">
                    <div id="kategoriPelayananKesehatanColumn" @class([
                        'd-flex flex-column gap-3 col-7',
                        'col-9' =>
                            $user->role !== 'dokter_magang' ||
                            $logMagang->status_verifikasi === 'selesai',
                    ])>
                        @foreach (old('kategoriPelayananKesehatan', $daftarKategoriPelayananKesehatanTersimpan) as $index => $kategoriPelayananKesehatanTersimpan)
                            <select @if ($user->role !== 'dokter_magang' || $logMagang->status_verifikasi === 'selesai') disabled @endif required class="form-control"
                                name="kategoriPelayananKesehatan[]" id="kategoriPelayananKesehatan-{{ $index }}">
                                <option disabled value>-- Pilih Kategori Pelayanan Kesehatan --</option>
                                @foreach ($daftarKategoriPelayananKesehatan as $kategoriPelayananKesehatan)
                                    <option @selected($kategoriPelayananKesehatan->nama === $kategoriPelayananKesehatanTersimpan)
                                        value="{{ $kategoriPelayananKesehatan->nama }}">
                                        {{ $kategoriPelayananKesehatan->nama }}
                                    </option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>

                    <div id="jumlahPasienColumn" class="d-flex flex-column gap-3 col-3">
                        @foreach (old('jumlahPasien', $daftarJumlahPasienTersimpan) as $index => $jumlahPasienTersimpan)
                            <input @if ($user->role !== 'dokter_magang' || $logMagang->status_verifikasi === 'selesai') disabled @endif required class="form-control"
                                type="number" name="jumlahPasien[]" id="jumlahPasien-{{ $index }}"
                                value="{{ $jumlahPasienTersimpan }}" min="1">
                        @endforeach
                    </div>

                    @if ($user->role === 'dokter_magang' && $logMagang->status_verifikasi !== 'selesai')
                        <div id="deleteKategoriColumn"
                            class="col-2 d-flex flex-column justify-content-between align-items-center">
                            @for ($i = 0; $i < count(old('jumlahPasien', $daftarJumlahPasienTersimpan)); $i++)
                                <button class="btn btn-danger px-2 py-1" type="button"
                                    id="deleteKategori-{{ $i }}"><i class="bi bi-trash-fill"
                                        style="color: white;"
                                        onclick="handleRemoveCategory({{ $i }})"></i></button>
                            @endfor
                        </div>
                    @endif
                </div>

                @if ($user->role === 'dokter_magang' && $logMagang->status_verifikasi !== 'selesai')
                    <div class="d-flex justify-content-end">
                        <button type="button" onclick="handleAddCategory()" style="padding: 0;"
                            class="mx-1 my-2 btn btn-link">
                            Tambah Kategori
                            <i class="ms-2 align-items-center bi bi-plus-circle"></i>
                        </button>
                    </div>
                @endif
            </div>

            <div class="col-lg-6">
                <label class="mb-1" for="tanggal">Tanggal</label>
                <div class="mb-3 input-group">
                    <input @if ($user->role !== 'dokter_magang' || $logMagang->status_verifikasi === 'selesai') disabled @endif required class="form-control"
                        type="date" name="tanggal" id="tanggal" placeholder="Pilih tanggal kegiatan"
                        value="{{ old('tanggal', $logMagang->tanggal->format('Y-m-d')) }}" onclick="this.showPicker()">
                </div>

                <label class="mb-1" for="deskripsi">Deskripsi</label>
                <div class="mb-3 input-group">
                    <textarea @if ($user->role !== 'dokter_magang' || $logMagang->status_verifikasi === 'selesai') disabled @endif required class="form-control" name="deskripsi"
                        id="deskripsi" placeholder="Tuliskan deskripsi singkat mengenai kegiatan" style="height: 96px;">{{ old('deskripsi', $logMagang->deskripsi) }}</textarea>
                </div>

                <label class="mb-1" for="hambatan">Hambatan</label>
                <div class="mb-3 input-group">
                    <textarea @if ($user->role !== 'dokter_magang' || $logMagang->status_verifikasi === 'selesai') disabled @endif required class="form-control" name="hambatan" id="hambatan"
                        placeholder="Tuliskan hambatan yang dihadapi saat kegiatan" style="height: 96px;">{{ old('hambatan', $logMagang->hambatan) }}</textarea>
                </div>

                <label class="mb-1" for="dokumentasi">Dokumentasi</label>
                <div class="mb-3 input-group">
                    <input @if ($user->role !== 'dokter_magang' || $logMagang->status_verifikasi === 'selesai') disabled @endif class="form-control" type="file"
                        accept=".pdf,.jpg,.jpeg,.png,.webp" name="dokumentasi" id="dokumentasi">
                    <a id="linkDokumentasi" target="_blank" class="w-100 overflow-hidden"
                        href="{{ asset('storage/' . $logMagang->dokumentasi) }}">{{ last(explode('/', $logMagang->dokumentasi)) }}</a>
                </div>
            </div>
        </form>

        @if ($logMagang->status_verifikasi !== 'selesai')
            <div class="ms-lg-auto">
                <div class="w-100 d-flex gap-3">
                    @if ($user->role === 'dokter_magang')
                        <form class="w-100 m-0" method="POST"
                            action="{{ route('logbook.destroy', ['id' => $logMagang->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-nowrap w-100 btn btn-danger">
                                Hapus Log
                            </button>
                        </form>

                        <button class="text-nowrap w-100 btn btn-success" form="editLog" type="submit">Simpan
                            Perubahan</button>
                    @elseif ($user->role === 'pembimbing_lapangan')
                        <form class="w-100 m-0" method="POST"
                            action="{{ route('logbook.verify', ['logMagang' => $logMagang->id]) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-nowrap w-100 btn btn-success" type="submit">
                                Verifikasi
                            </button>
                        </form>
                        <form class="w-100 m-0" method="POST"
                            action="{{ route('logbook.revise', ['logMagang' => $logMagang->id]) }}">
                            @csrf
                            @method('PATCH')
                            <button {{ $logMagang->status_verifikasi === 'revisi' ? 'disabled' : '' }}
                                class="text-nowrap w-100 btn btn-danger" type="submit">
                                Minta Revisi
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </section>

    @push('scripts')
        <script>
            let count = {{ count(old('jumlahPasien', $daftarJumlahPasienTersimpan)) }};
            const kategoriPelayananKesehatanColumn = document.querySelector('#kategoriPelayananKesehatanColumn');
            const jumlahPasienColumn = document.querySelector('#jumlahPasienColumn');
            const deleteKategoriColumn = document.querySelector('#deleteKategoriColumn');

            function handleAddCategory() {
                kategoriPelayananKesehatanColumn.insertAdjacentHTML('beforeend', `
                    <select required class="form-control" name="kategoriPelayananKesehatan[]"
                        id="kategoriPelayananKesehatan-${count}">
                        <option selected disabled value>-- Pilih Kategori Pelayanan Kesehatan --</option>
                        @foreach ($daftarKategoriPelayananKesehatan as $kategoriPelayananKesehatan)
                            <option value="{{ $kategoriPelayananKesehatan->nama }}">
                                {{ $kategoriPelayananKesehatan->nama }}</option>
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

            document.querySelector('#dokumentasi').addEventListener('change', function() {
                document.querySelector('#linkDokumentasi').remove();
            });
        </script>
    @endpush
</x-home-layout>
