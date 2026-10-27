@php
    $nama =
        $dataBaru && $dataBaru->nama !== $dokterMagang->nama
            ? $dokterMagang->nama . ' -> ' . $dataBaru->nama
            : $dokterMagang->nama;
    $nik =
        $dataBaru && $dataBaru->nik !== $dokterMagang->nik
            ? $dokterMagang->nik . ' -> ' . $dataBaru->nik
            : $dokterMagang->nik;
    $nim =
        $dataBaru && $dataBaru->nim !== $dokterMagang->nim
            ? $dokterMagang->nim . ' -> ' . $dataBaru->nim
            : $dokterMagang->nim;
    $alamat =
        $dataBaru && $dataBaru->alamat !== $dokterMagang->alamat
            ? $dokterMagang->alamat . ' -> ' . $dataBaru->alamat
            : $dokterMagang->alamat;
    $universitas =
        $dataBaru && $dataBaru->universitas !== $dokterMagang->universitas
            ? $dokterMagang->universitas . ' -> ' . $dataBaru->universitas
            : $dokterMagang->universitas;
@endphp

<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3" style="max-width: 768px;">
        @session('status')
            <div class="alert alert-warning">
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

        <div class="d-flex flex-column">

            <i class="bi bi-person-circle my-4 align-self-center" style="font-size: 96px;"></i>

            <div>
                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="username">Username</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input required disabled value="{{ $dokterMagang->username }}" class="form-control"
                                type="text" name="username" id="username" placeholder="Masukkan username">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="puskesmas">Puskesmas</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input required disabled value="{{ $dokterMagang->puskesmas?->nama }}" class="form-control"
                                type="text" name="puskesmas" id="puskesmas" placeholder="Masukkan puskesmas">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="nama">Nama</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input @if ($dokterMagang->is_profile_change_pending) disabled @endif required
                                value="{{ $nama }}" class="form-control" type="text" name="nama"
                                id="nama" placeholder="Masukkan nama">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="nik">NIK</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input @if ($dokterMagang->is_profile_change_pending) disabled @endif required
                                value="{{ $nik }}" class="form-control" type="text" name="nik"
                                id="nik" placeholder="Masukkan NIK">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="nim">NIM</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input @if ($dokterMagang->is_profile_change_pending) disabled @endif required
                                value="{{ $nim }}" class="form-control" type="text" name="nim"
                                id="nim" placeholder="Masukkan NIM">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="alamat">Alamat</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input @if ($dokterMagang->is_profile_change_pending) disabled @endif required
                                value="{{ $alamat }}" class="form-control" type="text" name="alamat"
                                id="alamat" placeholder="Masukkan alamat">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="universitas">Universitas</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input @if ($dokterMagang->is_profile_change_pending) disabled @endif required
                                value="{{ $universitas }}" class="form-control" type="text" name="universitas"
                                id="universitas" placeholder="Masukkan universitas">
                        </div>
                    </div>
                </div>
        </div>

        <div class="ms-lg-auto w-auto">
            <div class="w-100 d-flex justify-content-lg-end align-items-stretch gap-3">
                <form class="flex-fill flex-lg-grow-0" method="POST"
                    action="{{ route('ubah-data.reject', ['dokterMagang' => $dokterMagang->id]) }}">
                    @method('PATCH')
                    @csrf
                    <button class='w-100 btn btn-danger h-100' type="submit">Tolak Ubah Data</button>
                </form>
                <form class="flex-fill flex-lg-grow-0" method="POST"
                    action="{{ route('ubah-data.verify', ['dokterMagang' => $dokterMagang->id]) }}">
                    @method('PATCH')
                    @csrf
                    <button class='w-100 btn btn-success h-100' type="submit">Verifikasi Ubah Data</button>
                </form>
            </div>
        </div>
    </section>
</x-home-layout>
