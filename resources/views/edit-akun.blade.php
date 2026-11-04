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

        <form id="editAkun" method="POST" action="{{ route('akun.update', ['user' => $user->id]) }}"
            class="d-flex flex-column">
            @csrf
            @method('PUT')

            <i class="bi bi-person-circle my-4 align-self-center" style="font-size: 96px;"></i>

            <div>
                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nama">Nama</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input value="{{ old('nama', $user->nama) }}" class="form-control" type="text"
                                name="nama" id="nama" placeholder="Masukkan nama">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="role">Role</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <select onchange="handleSelectRole()" class="form-select" name="role" id="role">
                                <option value="" disabled>Masukkan role</option>
                                <option value="pembimbing_lapangan_puskesmas" @selected(old('role', $user->role) == 'pembimbing_lapangan_puskesmas')>
                                    Pembimbing Lapangan Puskesmas
                                </option>
                                <option value="dokter_magang" @selected(old('role', $user->role) == 'dokter_magang')>Dokter Magang</option>
                                <option value="pembimbing_lapangan_departemen" @selected(old('role', $user->role) == 'pembimbing_lapangan_departemen')>Pembimbing Lapangan Departemen</option>
                                <option value="mahasiswa_magang" @selected(old('role', $user->role) == 'mahasiswa_magang')>Mahasiswa Magang</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ old('role', $user->role) === 'mahasiswa_magang' ? '' : 'd-none' }}"
                    data-role-group="jenis-penempatan">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="jenisPenempatan">Penempatan mahasiswa</label>
                    <div class="col-lg-9">
                        @php($jenisPenempatan = old('jenisPenempatan', $user->puskesmas_id ? 'puskesmas' : ($user->departemen_id ? 'departemen' : '')))
                        <select class="mb-1 mb-lg-3 form-select" name="jenisPenempatan" id="jenisPenempatan">
                            <option value="" disabled @selected(!$jenisPenempatan)>Pilih penempatan</option>
                            <option value="puskesmas" @selected($jenisPenempatan === 'puskesmas')>Puskesmas</option>
                            <option value="departemen" @selected($jenisPenempatan === 'departemen')>Departemen</option>
                        </select>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role', $user->role), ['dokter_magang', 'pembimbing_lapangan_puskesmas'], true) || (old('role', $user->role) === 'mahasiswa_magang' && old('jenisPenempatan', $user->puskesmas_id ? 'puskesmas' : '') === 'puskesmas') ? '' : 'd-none' }}"
                    data-role-group="puskesmas">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="puskesmas">Puskesmas</label>
                    <div class="col-lg-9"> <input class="mb-3 form-control"
                            value="{{ old('namaPuskesmas', $user->puskesmas?->nama) }}" list="namaPuskesmasDatalist"
                            type="text" name="namaPuskesmas" id="namaPuskesmas" placeholder="Pilih puskesmas">
                        <datalist id="namaPuskesmasDatalist">
                            @foreach ($daftarNamaPuskesmas as $namaPuskesmas)
                                <option value="{{ $namaPuskesmas }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ old('role', $user->role) === 'pembimbing_lapangan_departemen' || (old('role', $user->role) === 'mahasiswa_magang' && old('jenisPenempatan', $user->departemen_id ? 'departemen' : '') === 'departemen') ? '' : 'd-none' }}"
                    data-role-group="departemen">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="namaDepartemen">Departemen</label>
                    <div class="col-lg-9">
                        <input class="mb-3 form-control" value="{{ old('namaDepartemen', $user->departemen?->nama) }}"
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
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nip">NIP (khusus pembimbing
                        lapangan)</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input value="{{ old('nip', $user->nip) }}" class="form-control" disabled type="text"
                                name="nip" id="nip" placeholder="Masukkan NIP">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nik">NIK</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input value="{{ old('nik', $user->nik) }}" class="form-control" disabled type="text"
                                name="nik" id="nik" placeholder="Masukkan NIK">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="nim">NIM</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input value="{{ old('nim', $user->nim) }}" class="form-control" disabled type="text"
                                name="nim" id="nim" placeholder="Masukkan NIM">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="alamat">Alamat</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input value="{{ old('alamat', $user->alamat) }}" class="form-control" disabled
                                type="text" name="alamat" id="alamat" placeholder="Masukkan alamat">
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="universitas">Universitas</label>
                    <div class="col-lg-9">
                        <div class="mb-3 input-group">
                            <input value="{{ old('universitas', $user->universitas) }}" class="form-control" disabled
                                type="text" name="universitas" id="universitas" placeholder="Masukkan universitas">
                        </div>
                    </div>
                </div>
        </form>

        <div class="d-flex flex-column align-items-stretch align-items-lg-end">
            <div class="d-flex gap-3">
                <form class="w-100 m-0" method="POST" action="{{ route('akun.destroy', ['id' => $user->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-100 text-nowrap btn btn-danger"
                        @if ($user->role === 'admin') disabled @endif>
                        Hapus Akun
                    </button>
                </form>

                <button class="w-100 text-nowrap btn btn-success" form="editAkun" type="submit">Edit akun</button>
            </div>
        </div>
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
                const isSupportedRole = isIntern || isSupervisor;
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
                document.querySelector('#role').disabled = !isSupportedRole;

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
