<x-home-layout>
    <section class="row row-cols-lg-2 row-cols-1 g-3 p-4 w-100">
        <a class="col" href="{{ route('akun.menu') }}" style="text-decoration: none">
            <div class="card w-100">
                <div class="row g-0">
                    <div class="col-xl-4">
                        <img src="{{ asset('images/Home_manage-account.jpg') }}" class="card-img-top object-fit-cover"
                            style="height: 200px;" alt="...">
                    </div>
                    <div class="col-xl-8">
                        <div class="card-body py-4">
                            <h4 class="card-title">Manajemen Akun</h4>
                            <p class="card-text">Ubah data & ganti password</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <a class="col" href="{{ route('logbook.index') }}" style="text-decoration: none;">
            <div class="card w-100">
                <div class="row g-0">
                    <div class="col-xl-4">
                        <img src="{{ asset('images/Home_manage-logbook.jpg') }}" class="card-img-top object-fit-cover"
                            style="height: 200px;" alt="...">
                    </div>
                    <div class="col-xl-8">
                        <div class="card-body py-4">
                            <h4 class="card-title">Manajemen Logbook</h4>
                            <p class="card-text">Pantau logbook dokter magang</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        @if (request()->user()->role === 'admin')
            <a class="col" href="{{ route('password.index') }}" style="text-decoration: none">
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
        @endif
    </section>
</x-home-layout>
