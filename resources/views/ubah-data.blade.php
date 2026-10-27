@php
    $nama =
        $dataBaru && $dataBaru->nama !== $user->nama && $user->is_profile_change_pending
            ? $user->nama . ' -> ' . $dataBaru->nama
            : old('nama', $user->nama);
    $nik =
        $dataBaru && $dataBaru->nik !== $user->nik && $user->is_profile_change_pending
            ? $user->nik . ' -> ' . $dataBaru->nik
            : old('nik', $user->nik);
    $nim =
        $dataBaru && $dataBaru->nim !== $user->nim && $user->is_profile_change_pending
            ? $user->nim . ' -> ' . $dataBaru->nim
            : old('nim', $user->nim);
    $alamat =
        $dataBaru && $dataBaru->alamat !== $user->alamat && $user->is_profile_change_pending
            ? $user->alamat . ' -> ' . $dataBaru->alamat
            : old('alamat', $user->alamat);
    $universitas =
        $dataBaru && $dataBaru->universitas !== $user->universitas && $user->is_profile_change_pending
            ? $user->universitas . ' -> ' . $dataBaru->universitas
            : old('universitas', $user->universitas);

    $nip = $user->nip;
@endphp

<x-home-layout>
    <section class="p-4 d-flex w-100 flex-column gap-3" style="max-width: 768px;">
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

        <form id="editAkun" method="POST" action="{{ route('akun.ubah-data.update', ['user' => $user->id]) }}"
            class="d-flex flex-column">
            @csrf
            @method('PUT')

            <i class="bi bi-person-circle my-4 align-self-center" style="font-size: 96px;"></i>

            <div>
                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="username">Username</label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input required disabled value="{{ $user->username }}" class="form-control" type="text"
                                name="username" id="username" placeholder="Masukkan username">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="puskesmas">Puskesmas</label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input required disabled value="{{ $user->puskesmas?->nama }}" class="form-control"
                                type="text" name="puskesmas" id="puskesmas" placeholder="Masukkan puskesmas">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="nama">Nama</label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input @if ($user->is_profile_change_pending) disabled @endif required
                                value="{{ $nama }}" class="form-control" type="text" name="nama"
                                id="nama" placeholder="Masukkan nama">
                        </div>
                    </div>
                </div>

                @if ($user->role === 'pembimbing_lapangan')
                    <div class="row align-items-center g-2 mb-3">
                        <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="nip">NIP</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input required value="{{ $nip }}" class="form-control" type="text"
                                    name="nip" id="nip" placeholder="Masukkan nip">
                            </div>
                        </div>
                    </div>
                @elseif ($user->role === 'dokter_magang')
                    <div class="row align-items-center g-2 mb-3">
                        <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="nik">NIK</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input @if ($user->is_profile_change_pending) disabled @endif required
                                    value="{{ $nik }}" class="form-control" type="text" name="nik"
                                    id="nik" placeholder="Masukkan NIK">
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center g-2 mb-3">
                        <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="nim">NIM</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input @if ($user->is_profile_change_pending) disabled @endif required
                                    value="{{ $nim }}" class="form-control" type="text" name="nim"
                                    id="nim" placeholder="Masukkan NIM">
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center g-2 mb-3">
                        <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="alamat">Alamat</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input @if ($user->is_profile_change_pending) disabled @endif required
                                    value="{{ $alamat }}" class="form-control" type="text" name="alamat"
                                    id="alamat" placeholder="Masukkan alamat">
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center g-2 mb-3">
                        <label class="col-lg-3 col-form-label mb-1 mb-lg-0" for="universitas">Universitas</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input @if ($user->is_profile_change_pending) disabled @endif required
                                    value="{{ $universitas }}" class="form-control" type="text" name="universitas"
                                    id="universitas" placeholder="Masukkan universitas">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex flex-column align-items-stretch align-items-lg-end">
                    @if (!$user->is_profile_change_pending)
                        <button class='btn btn-primary mt-3' type="submit">Simpan Perubahan</button>
                    @else
                        <button class='btn btn-warning mt-3' disabled type="submit">Menunggu Verifikasi</button>
                    @endif
                </div>
            </div>
        </form>
    </section>
</x-home-layout>
