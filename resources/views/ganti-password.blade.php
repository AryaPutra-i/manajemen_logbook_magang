<x-home-layout>
    <section class="p-4 container d-flex flex-column gap-3" style="max-width: 768px;">
        @session('status')
            <div class="alert alert-success">
                {{ $value }}
            </div>
        @endsession

        <form class="d-flex flex-column gap-3" method="POST"
            action="{{ request()->routeIs('akun.password.*') ? route('akun.password.update') : route('password.update') }}">
            @csrf
            <div class="d-flex flex-column gap-2">
                <!-- Password Lama -->
                <div class="row align-items-center g-2">
                    <label for="passwordLama" class="col-lg-3 col-form-label">Password Lama</label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input required placeholder="Masukkan password lama Anda"
                                class="form-control @error('passwordLama') is-invalid @enderror" type="password"
                                name="passwordLama" id="passwordLama">
                            <span class="input-group-text">
                                <i class="bi bi-eye" id="password-eye" onclick="handleOldPasswordEyeToggle()"></i>
                            </span>
                        </div>
                        @error('passwordLama')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password Baru -->
                <div class="row align-items-center g-2">
                    <label for="passwordBaru" class="col-lg-3 col-form-label">Password Baru</label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <input required placeholder="Masukkan password baru Anda"
                                class="form-control @error('passwordBaru') is-invalid @enderror" type="password"
                                name="passwordBaru" id="passwordBaru">
                            <span class="input-group-text">
                                <i class="bi bi-eye" id="password-eye" onclick="handleNewPasswordEyeToggle()"></i>
                            </span>
                        </div>
                        @error('passwordBaru')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <button class="btn btn-primary ms-lg-auto" type="submit">
                Ganti Password
            </button>
        </form>
    </section>

    @push('scripts')
        <script>
            function handleOldPasswordEyeToggle() {
                const passwordInput = document.querySelector('#passwordLama');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                } else {
                    passwordInput.type = 'password';
                }

                event.target.classList.toggle('bi-eye');
                event.target.classList.toggle('bi-eye-slash');
            }

            function handleNewPasswordEyeToggle() {
                const passwordInput = document.querySelector('#passwordBaru');

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
</x-home-layout>
