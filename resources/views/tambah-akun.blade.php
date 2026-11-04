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
                            <select onchange="handleSelectRole()" class="form-select" name="role" id="role" required>
                                                <option value="" @selected(!old('role')) disabled>Masukkan role</option>
                                                <option value="pembimbing_lapangan_puskesmas" @selected(old('role') == 'pembimbing_lapangan_puskesmas')>Pembimbing Lapangan Puskesmas
                                </option>
                                <option value="dokter_magang" @selected(old('role') == 'dokter_magang')>Dokter Magang</option>
                                                <option value="pembimbing_lapangan_departemen" @selected(old('role') == 'pembimbing_lapangan_departemen')>Pembimbing Lapangan Departemen</option>
                                                <option value="mahasiswa_magang" @selected(old('role') == 'mahasiswa_magang')>Mahasiswa Magang</option>
                                            </select>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ old('role') === 'mahasiswa_magang' ? '' : 'd-none' }}"
                    data-role-group="jenis-penempatan">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="jenisPenempatan">Penempatan mahasiswa</label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="jenisPenempatan" id="jenisPenempatan">
                            <option value="" disabled @selected(!old('jenisPenempatan'))>Pilih penempatan</option>
                            <option value="puskesmas" @selected(old('jenisPenempatan') === 'puskesmas')>Puskesmas</option>
                            <option value="departemen" @selected(old('jenisPenempatan') === 'departemen')>Departemen</option>
                        </select>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role'), ['dokter_magang', 'pembimbing_lapangan_puskesmas'], true) ? '' : 'd-none' }}"
                    data-role-group="puskesmas">
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

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role'), ['mahasiswa_magang', 'pembimbing_lapangan_departemen'], true) ? '' : 'd-none' }}"
                    data-role-group="departemen">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="namaDepartemen">Departemen</label>
                    <div class="col-lg-9">
                        <input value="{{ old('namaDepartemen') }}" class="mb-1 mb-lg-3 form-control"
                            list="namaDepartemenDatalist" type="text" name="namaDepartemen" id="namaDepartemen"
                            placeholder="Pilih departemen">
                        <datalist id="namaDepartemenDatalist">
                            @foreach ($daftarNamaDepartemen as $namaDepartemen)
                                <option value="{{ $namaDepartemen }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nip">NIP (khusus pembimbing lapangan)</label>
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

                const isDoctor = selectedRole === 'dokter_magang';
                const isPuskesmasSupervisor = selectedRole === 'pembimbing_lapangan_puskesmas';
                const isStudent = selectedRole === 'mahasiswa_magang';
                const isDepartmentSupervisor = selectedRole === 'pembimbing_lapangan_departemen';

                const isIntern = isDoctor || isStudent;
                const isSupervisor = isPuskesmasSupervisor || isDepartmentSupervisor;
                const placement = document.querySelector('#jenisPenempatan');
                const isStudentAtPuskesmas = isStudent && placement.value === 'puskesmas';
                const isStudentAtDepartment = isStudent && placement.value === 'departemen';
                const puskesmasInput = document.querySelector('#namaPuskesmas');
                const departmentInput = document.querySelector('#namaDepartemen');

                document.querySelector('[data-role-group="jenis-penempatan"]').classList.toggle('d-none', !isStudent);
                placement.disabled = !isStudent;
                placement.required = isStudent;

                document.querySelector('[data-role-group="puskesmas"]').classList.toggle(
                    'd-none',
                    !isDoctor && !isPuskesmasSupervisor && !isStudentAtPuskesmas
                );
                document.querySelector('[data-role-group="departemen"]').classList.toggle(
                    'd-none',
                    !isDepartmentSupervisor && !isStudentAtDepartment
                );

                puskesmasInput.disabled = !isDoctor && !isPuskesmasSupervisor && !isStudent;
                departmentInput.disabled = !isDepartmentSupervisor && !isStudent;

                document.querySelector('#nip').disabled = !isSupervisor;
                document.querySelector('#nik').disabled = !isIntern;
                document.querySelector('#nim').disabled = !isIntern;
                document.querySelector('#alamat').disabled = !isIntern;
                document.querySelector('#universitas').disabled = !isIntern;

                document.querySelector('#nip').required = isSupervisor;
                document.querySelector('#nik').required = isIntern;
                document.querySelector('#nim').required = isIntern;
                document.querySelector('#alamat').required = isIntern;
                document.querySelector('#universitas').required = isIntern;

                puskesmasInput.required = isDoctor || isPuskesmasSupervisor || isStudentAtPuskesmas;
                departmentInput.required = isDepartmentSupervisor || isStudentAtDepartment;
            }

            document.querySelector('#jenisPenempatan').addEventListener('change', function () {
                if (this.value === 'puskesmas') {
                    document.querySelector('#namaDepartemen').value = '';
                } else if (this.value === 'departemen') {
                    document.querySelector('#namaPuskesmas').value = '';
                }

                handleSelectRole();
            });

        </script>
        @endpush
</x-home-layout>
