<x-base-layout>
    <div
        class='d-flex flex-column flex-lg-row w-100 min-vh-100 justify-content-end justify-content-lg-center align-items-lg-center gap-lg-4 my-lg-auto'>
        <div class="p-2 col-lg-4 d-flex flex-column align-items-center">
            <h1 class="fs-1 fw-bold text-center">
                Web Logbook <br> Dokter Magang
            </h1>

            <p class="fw-bold text-center">
                Dinas Kesehatan <br> Pemerintah Kota Surabaya
            </p>

            <img class="img-fluid" src="{{ asset('images/Login_image.png') }}" alt="">
        </div>

        <form class="col-lg-4 bg-white py-5 px-4 rounded-5 border-2 border-black d-flex flex-column gap-4" method="POST"
            action="{{ route('login.attempt') }}">
            @csrf
            <h2 class="fs-2 fw-bold text-center">Login</h2>

            <div class="d-flex flex-column gap-2">
                <div class="gap-2 d-flex flex-column">
                    <label for="username">
                        Username
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person fs-6"></i></span>
                        <input value="{{ old('username') }}"
                            class="form-control @error('username') is-invalid @enderror"
                            placeholder="Masukkan username Anda" type="text" name="username" id="username" required>
                    </div>
                    @error('username')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror

                    <label for="username">
                        Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock fs-6"></i></span>
                        <input class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan password Anda" type="password" name="password" id="password"
                            required>
                        <span class="input-group-text"><i class="bi bi-eye" id="password-eye"
                                onclick="handlePasswordEyeToggle()"></i></span>
                    </div>
                    @error('password')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <a class="text-black align-self-start" href="{{ route('lupa-password') }}">Lupa Password?</a>
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>
    </div>

    @push('scripts')
        <script>
            function handlePasswordEyeToggle() {
                const passwordInput = document.querySelector('#password');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                } else {
                    passwordInput.type = 'password';
                }

                event.target.classList.toggle('bi-eye');
                event.target.classList.toggle('bi-eye-slash');
            }
        </script>
    @endpush
</x-base-layout>
