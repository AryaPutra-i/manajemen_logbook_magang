<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3" style="max-width: 768px;">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('akun.store') }}" class="d-flex flex-column">
            @csrf

            <i class="bi bi-person-circle my-4 align-self-center" style="font-size: 96px;"></i>

            <div>
                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nama">Nama</label>
                    <div class="col-lg-9">
                        <div class="mb-1 mb-lg-3 input-group">
                            <input value="{{ old('nama') }}" class="form-control" type="text" name="nama"
                                id="nama" placeholder="Masukkan nama">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="role">Role</label>
                    <div class="col-lg-9">
                        <div class="mb-1 mb-lg-3 input-group">
                            <select onchange="handleSelectRole()" class="form-select" name="role" id="role">
                                <option selected disabled>Masukkan role</option>
                                <option value="pembimbing_lapangan" @selected(old('role') == 'pembimbing_lapangan')>Pembimbing Lapangan
                                </option>
                                <option value="dokter_magang" @selected(old('role') == 'dokter_magang')>Dokter Magang</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="puskesmas">Puskesmas</label>
                    <div class="col-lg-9">
                        <input value="{{ old('namaPuskesmas') }}" class="mb-1 mb-lg-3 form-control"
                            list="namaPuskesmasDatalist" type="text" name="namaPuskesmas" id="namaPuskesmas"
                            placeholder="Pilih puskesmas">
                        <datalist id="namaPuskesmasDatalist">
                            @foreach ($daftarNamaPuskesmas as $namaPuskesmas)
                                <option value="{{ $namaPuskesmas }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nip">NIP (khusus pembimbing
                        lapangan)</label>
                    <div class="col-lg-9">
                        <div class="mb-1 mb-lg-3 input-group">
                            <input value="{{ old('nip') }}" class="form-control" disabled type="text"
                                name="nip" id="nip" placeholder="Masukkan NIP">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nik">NIK</label>
                    <div class="col-lg-9">
                        <div class="mb-1 mb-lg-3 input-group">
                            <input value="{{ old('nik') }}" class="form-control" disabled type="text"
                                name="nik" id="nik" placeholder="Masukkan NIK">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nim">NIM</label>
                    <div class="col-lg-9">
                        <div class="mb-1 mb-lg-3 input-group">
                            <input value="{{ old('nim') }}" class="form-control" disabled type="text"
                                name="nim" id="nim" placeholder="Masukkan NIM">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="alamat">Alamat</label>
                    <div class="col-lg-9">
                        <div class="mb-1 mb-lg-3 input-group">
                            <input value="{{ old('alamat') }}" class="form-control" disabled type="text"
                                name="alamat" id="alamat" placeholder="Masukkan alamat">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="universitas">Universitas</label>
                    <div class="col-lg-9">
                        <div class="mb-1 mb-lg-3 input-group">
                            <input value="{{ old('universitas') }}" class="form-control" disabled type="text"
                                name="universitas" id="universitas" placeholder="Masukkan universitas">
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-lg-row justify-content-lg-end">
                    <button class="btn btn-primary mt-3" type="submit">Tambah akun</button>
                </div>
            </div>
        </form>
    </section>

    @push('scripts')
        <script>
            handleSelectRole();

            function handleSelectRole() {
                const selectedRole = document.querySelector('#role').value;

                if (selectedRole === 'dokter_magang') {
                    document.querySelector('#nip').disabled = true;

                    document.querySelector('#nik').disabled = false;
                    document.querySelector('#nim').disabled = false;
                    document.querySelector('#alamat').disabled = false;
                    document.querySelector('#universitas').disabled = false;
                } else if (selectedRole === 'pembimbing_lapangan') {
                    document.querySelector('#nik').disabled = true;
                    document.querySelector('#nim').disabled = true;
                    document.querySelector('#alamat').disabled = true;
                    document.querySelector('#universitas').disabled = true;

                    document.querySelector('#nip').disabled = false;
                } else {
                    document.querySelector('#nik').disabled = true;
                    document.querySelector('#nim').disabled = true;
                    document.querySelector('#alamat').disabled = true;
                    document.querySelector('#universitas').disabled = true;
                    document.querySelector('#nip').disabled = true;
                }
            }
        </script>
    @endpush
</x-home-layout>
