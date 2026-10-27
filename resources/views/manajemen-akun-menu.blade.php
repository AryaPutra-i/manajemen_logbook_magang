<x-home-layout>
    <section class="p-4 w-100 row row-cols-lg-2 row-cols-1 g-3">
        @if ($user->role === 'pembimbing_lapangan')
            <a class="col" href="{{ route('ubah-data.queue') }}" style="text-decoration: none">
                <div class="card w-100">
                    <div class="row g-0">
                        <div class="col-xl-4">
                            <img src="{{ asset('images/Manajemen-Akun_Verifikasi-Ubah-Data.jpg') }}"
                                class="card-img-top object-fit-cover" style="height: 200px;" alt="...">
                        </div>
                        <div class="col-xl-8">
                            <div class="card-body py-4">
                                <h4 class="card-title">Manajemen Ubah Data</h4>
                                <p class="card-text">Manajemen permintaan ubah data dokter magang</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @endif

        <a class="col" href="{{ route('akun.ubah-data.edit') }}" style="text-decoration: none">
            <div class="card w-100">
                <div class="row g-0">
                    <div class="col-xl-4">
                        <img src="{{ asset('images/Manajemen-Akun_ubah-data.jpg') }}"
                            class="card-img-top object-fit-cover" style="height: 200px;" alt="...">
                    </div>
                    <div class="col-xl-8">
                        <div class="card-body py-4">
                            <h4 class="card-title">Ubah Data</h4>
                            <p class="card-text">Ubah data terkait diri</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <a class="col" href="{{ route('akun.password.index') }}" style="text-decoration: none;">
            <div class="card w-100">
                <div class="row g-0">
                    <div class="col-xl-4">
                        <img src="{{ asset('images/Manajemen-Akun_Ganti-Password.jpg') }}"
                            class="card-img-top object-fit-cover" style="height: 200px;" alt="...">
                    </div>
                    <div class="col-xl-8">
                        <div class="card-body py-4">
                            <h4 class="card-title">Ganti Password</h4>
                            <p class="card-text">Ganti password Anda disini</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </section>
</x-home-layout>
