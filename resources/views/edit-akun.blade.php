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
                                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                                <option value="dokter_magang" @selected(old('role', $user->role) === 'dokter_magang')>Dokter Magang</option>
                                <option value="pembimbing_lapangan" @selected(old('role', $user->role) === 'pembimbing_lapangan')>Pembimbing Lapangan</option>
                                <option value="mahasiswa_magang" @selected(old('role', $user->role) === 'mahasiswa_magang')>Mahasiswa Magang</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role', $user->role), ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true) ? '' : 'd-none' }}"
                    data-role-group="penempatan">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="jenisUnitKerja">Jenis Unit Kerja</label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="jenisUnitKerja" id="jenisUnitKerja">
                            <option value="" disabled @selected(!old('jenisUnitKerja', $user->unitKerja?->jenis))>Pilih jenis unit kerja</option>
                            <option value="Puskesmas" @selected(old('jenisUnitKerja', $user->unitKerja?->jenis) === 'Puskesmas')>Puskesmas</option>
                            <option value="Dinas_Kesehatan" @selected(old('jenisUnitKerja', $user->unitKerja?->jenis) === 'Dinas_Kesehatan')>Dinas Kesehatan</option>
                        </select>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role', $user->role), ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true) ? '' : 'd-none' }}"
                    data-role-group="unit-kerja">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="namaUnitKerja">
                        <span id="unitKerjaLabel">Unit Kerja</span>
                    </label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="namaUnitKerja" id="namaUnitKerja">
                            <option value="" disabled @selected(!old('namaUnitKerja', $user->unitKerja?->nama))>Pilih unit kerja</option>
                            @foreach ($daftarUnitKerja as $unitKerja)
                                <option value="{{ $unitKerja->nama }}" data-unit-kerja-id="{{ $unitKerja->id }}"
                                    data-jenis="{{ $unitKerja->jenis }}"
                                    @selected(old('jenisUnitKerja', $user->unitKerja?->jenis) === $unitKerja->jenis && old('namaUnitKerja', $user->unitKerja?->nama) === $unitKerja->nama)>
                                    {{ $unitKerja->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role', $user->role), ['pembimbing_lapangan', 'mahasiswa_magang'], true) ? '' : 'd-none' }}"
                    data-role-group="departemen">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="namaDepartemen">
                        <span id="departemenLabel">Departemen</span>
                    </label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="namaDepartemen" id="namaDepartemen">
                            <option value="" @selected(!old('namaDepartemen', $user->departemen?->nama))>Pilih departemen</option>
                            @if ($daftarDepartemenDinasKesehatan->isNotEmpty())
                                <optgroup label="Dinas Kesehatan" data-jenis="Dinas_Kesehatan">
                                    @foreach ($daftarDepartemenDinasKesehatan as $departemen)
                                        <option value="{{ $departemen->nama }}"
                                            @selected(old('jenisUnitKerja', $user->unitKerja?->jenis) === 'Dinas_Kesehatan' && old('namaDepartemen', $user->departemen?->nama) === $departemen->nama)>
                                            {{ $departemen->nama }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @foreach ($daftarUnitKerja as $unitKerja)
                                @if ($unitKerja->jenis === 'Puskesmas' && $unitKerja->departemens->isNotEmpty())
                                    <optgroup label="{{ $unitKerja->nama }}" data-unit-kerja-id="{{ $unitKerja->id }}"
                                        data-jenis="{{ $unitKerja->jenis }}">
                                    @foreach ($unitKerja->departemens as $departemen)
                                        <option value="{{ $departemen->nama }}"
                                            @selected(old('jenisUnitKerja', $user->unitKerja?->jenis) === $unitKerja->jenis && old('namaUnitKerja', $user->unitKerja?->nama) === $unitKerja->nama && old('namaDepartemen', $user->departemen?->nama) === $departemen->nama)>
                                            {{ $departemen->nama }}
                                        </option>
                                    @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
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
            function handleSelectRole() {
                const selectedRole = document.querySelector('#role').value;
                const isDoctor = selectedRole === 'dokter_magang';
                const isSupervisor = selectedRole === 'pembimbing_lapangan';
                const isStudent = selectedRole === 'mahasiswa_magang';
                const isIntern = isDoctor || isStudent;
                const requiresPlacement = isIntern || isSupervisor;
                const isSupportedRole = requiresPlacement;
                const jenisUnitKerjaInput = document.querySelector('#jenisUnitKerja');
                const unitKerjaInput = document.querySelector('#namaUnitKerja');
                const departmentInput = document.querySelector('#namaDepartemen');

                document.querySelector('[data-role-group="penempatan"]').classList.toggle('d-none', !requiresPlacement);
                document.querySelector('[data-role-group="departemen"]').classList.toggle('d-none', !isStudent && !isSupervisor);
                jenisUnitKerjaInput.disabled = !requiresPlacement;
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

                if (isDoctor) {
                    jenisUnitKerjaInput.value = 'Puskesmas';
                }
                jenisUnitKerjaInput.querySelector('option[value="Dinas_Kesehatan"]').hidden = isDoctor;
                jenisUnitKerjaInput.querySelector('option[value="Dinas_Kesehatan"]').disabled = isDoctor;
                updateUnitKerjaOptions();
                updateDepartemenOptions();
            }

            function updateUnitKerjaOptions() {
                const unitType = document.querySelector('#jenisUnitKerja').value;
                const unitKerjaInput = document.querySelector('#namaUnitKerja');
                const hasUnitForType = [...unitKerjaInput.querySelectorAll('option[data-jenis]')]
                    .some((option) => option.dataset.jenis === unitType);

                unitKerjaInput.querySelectorAll('option[data-jenis]').forEach((option) => {
                    const isSelectedType = option.dataset.jenis === unitType;
                    option.hidden = !isSelectedType;
                    option.disabled = !isSelectedType;
                });
                unitKerjaInput.querySelector('option:not([data-jenis])').textContent = unitType && !hasUnitForType
                    ? 'Belum ada unit kerja untuk jenis ini'
                    : 'Pilih unit kerja';

                const selectedUnit = unitKerjaInput.selectedOptions[0];
                if (selectedUnit?.dataset.jenis && selectedUnit.dataset.jenis !== unitType) {
                    unitKerjaInput.value = '';
                }
                const requiresPuskesmas = unitType === 'Puskesmas'
                    && ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang']
                        .includes(document.querySelector('#role').value);
                unitKerjaInput.disabled = !requiresPuskesmas;
                unitKerjaInput.required = requiresPuskesmas;
                document.querySelector('[data-role-group="unit-kerja"]').classList.toggle('d-none', !requiresPuskesmas);
                document.querySelector('#unitKerjaLabel').textContent = unitType === 'Puskesmas'
                    ? 'Puskesmas'
                    : 'Unit Kerja Dinas Kesehatan';
            }

            function updateDepartemenOptions() {
                const unitType = document.querySelector('#jenisUnitKerja').value;
                const role = document.querySelector('#role').value;
                const unitKerjaId = document.querySelector('#namaUnitKerja').selectedOptions[0]?.dataset.unitKerjaId;
                const departmentInput = document.querySelector('#namaDepartemen');

                departmentInput.querySelectorAll('optgroup').forEach((group) => {
                    const isSelectedType = group.dataset.jenis === unitType;
                    const isSelectedUnit = unitType === 'Dinas_Kesehatan'
                        || group.dataset.unitKerjaId === unitKerjaId;
                    const isAvailable = isSelectedType && isSelectedUnit;
                    group.hidden = !isAvailable;
                    group.disabled = !isAvailable;
                });

                const selectedGroup = departmentInput.selectedOptions[0]?.parentElement;
                if (!selectedGroup || selectedGroup.hidden) {
                    departmentInput.value = '';
                }
                const canChooseDepartment = ['mahasiswa_magang', 'pembimbing_lapangan'].includes(role);
                departmentInput.disabled = !canChooseDepartment
                    || !unitType
                    || (unitType === 'Puskesmas' && !unitKerjaId);
                departmentInput.required = role === 'mahasiswa_magang'
                    || (role === 'pembimbing_lapangan' && unitType === 'Dinas_Kesehatan');
                document.querySelector('#departemenLabel').textContent = role === 'pembimbing_lapangan'
                    && unitType === 'Puskesmas'
                    ? 'Departemen (opsional)'
                    : 'Departemen';
            }

            document.querySelector('#jenisUnitKerja').addEventListener('change', function () {
                handleSelectRole();
            });

            document.querySelector('#namaUnitKerja').addEventListener('change', function () {
                updateDepartemenOptions();
            });

            handleSelectRole();
        </script>
    @endpush
</x-home-layout>
