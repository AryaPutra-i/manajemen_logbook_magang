<x-home-layout>
    @php
        $authUser = request()->user();
        $isUnitAdmin = $authUser->role === 'admin_unit_kerja';
        $adminUnit = $isUnitAdmin ? $authUser->unitKerja : null;
        $currentJenis = $user->unitKerja?->jenis ?? ($user->departemen_id !== null && $user->departemen?->unit_kerja_id === null ? 'Dinas_Kesehatan' : null);
    @endphp
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
                            @if ($user->role === 'admin')
                                <input type="text" class="form-control" value="Admin" disabled readonly>
                                <input type="hidden" name="role" id="role" value="admin">
                            @else
                                <select onchange="handleSelectRole()" class="form-select" name="role" id="role">
                                    <option value="" disabled>Masukkan role</option>
                                    @if ($authUser->role === 'admin')
                                        <option value="admin_unit_kerja" @selected(old('role', $user->role) === 'admin_unit_kerja')>Admin Unit Kerja</option>
                                    @endif
                                    <option value="dokter_magang" @selected(old('role', $user->role) === 'dokter_magang')>Dokter Magang</option>
                                    <option value="pembimbing_lapangan" @selected(old('role', $user->role) === 'pembimbing_lapangan')>Pembimbing Lapangan</option>
                                    <option value="mahasiswa_magang" @selected(old('role', $user->role) === 'mahasiswa_magang')>Mahasiswa Magang</option>
                                </select>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Jenis Unit Kerja: disembunyikan & dikunci jika login sebagai admin_unit_kerja --}}
                <div class="row align-items-center g-2 mb-3 {{ ($isUnitAdmin || in_array(old('role', $user->role), ['admin_unit_kerja', 'dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true)) ? '' : 'd-none' }}"
                    data-role-group="penempatan">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="jenisUnitKerja">Jenis Unit Kerja</label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="jenisUnitKerja" id="jenisUnitKerja"
                            @if ($isUnitAdmin) disabled @endif>
                            <option value="" disabled @selected(!old('jenisUnitKerja', $currentJenis))>Pilih jenis unit kerja</option>
                            <option value="Puskesmas" @selected(old('jenisUnitKerja', $currentJenis) === 'Puskesmas')>Puskesmas</option>
                            @if (!$isUnitAdmin || $adminUnit?->jenis === 'Dinas_Kesehatan')
                                <option value="Dinas_Kesehatan" @selected(old('jenisUnitKerja', $currentJenis) === 'Dinas_Kesehatan')>Dinas Kesehatan</option>
                            @endif
                        </select>
                        @if ($isUnitAdmin && $adminUnit)
                            {{-- hidden input agar nilai tetap terkirim meski select disabled --}}
                            <input type="hidden" name="jenisUnitKerja" value="{{ $adminUnit->jenis }}">
                        @endif
                    </div>
                </div>

                {{-- Unit Kerja --}}
                <div class="row align-items-center g-2 mb-3 {{ (in_array(old('role', $user->role), ['admin_unit_kerja', 'dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true) && $currentJenis === 'Puskesmas') ? '' : 'd-none' }}"
                    data-role-group="unit-kerja">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="namaUnitKerja">
                        <span id="unitKerjaLabel">Unit Kerja</span>
                    </label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="namaUnitKerja" id="namaUnitKerja"
                            @if ($isUnitAdmin) disabled @endif>
                            <option value="" disabled @selected(!old('namaUnitKerja', $user->unitKerja?->nama))>Pilih unit kerja</option>
                            @foreach ($daftarUnitKerja as $unitKerja)
                                <option value="{{ $unitKerja->nama }}" data-unit-kerja-id="{{ $unitKerja->id }}"
                                    data-jenis="{{ $unitKerja->jenis }}"
                                    @selected(old('jenisUnitKerja', $currentJenis) === $unitKerja->jenis && old('namaUnitKerja', $user->unitKerja?->nama) === $unitKerja->nama)>
                                    {{ $unitKerja->nama }}
                                </option>
                            @endforeach
                        </select>
                        @if ($isUnitAdmin && $adminUnit)
                            {{-- hidden input agar nilai tetap terkirim meski select disabled --}}
                            <input type="hidden" name="namaUnitKerja" value="{{ $adminUnit->nama }}">
                        @endif
                    </div>
                </div>

                {{-- Departemen --}}
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
                                            @selected(old('jenisUnitKerja', $currentJenis) === 'Dinas_Kesehatan' && old('namaDepartemen', $user->departemen?->nama) === $departemen->nama)>
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
                                            @selected(old('jenisUnitKerja', $currentJenis) === $unitKerja->jenis && old('namaUnitKerja', $user->unitKerja?->nama) === $unitKerja->nama && old('namaDepartemen', $user->departemen?->nama) === $departemen->nama)>
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
            const IS_UNIT_ADMIN = {{ $isUnitAdmin ? 'true' : 'false' }};

            function handleSelectRole() {
                const roleEl = document.querySelector('#role');
                const selectedRole = roleEl ? roleEl.value : '';
                const isAdmin = selectedRole === 'admin';
                const isDoctor = selectedRole === 'dokter_magang';
                const isSupervisor = selectedRole === 'pembimbing_lapangan';
                const isStudent = selectedRole === 'mahasiswa_magang';
                const isUnitAdmin = selectedRole === 'admin_unit_kerja';
                const isIntern = isDoctor || isStudent;
                const requiresPlacement = isIntern || isSupervisor || isUnitAdmin;
                const jenisUnitKerjaInput = document.querySelector('#jenisUnitKerja');
                const unitKerjaInput = document.querySelector('#namaUnitKerja');

                const penempatanGroup = document.querySelector('[data-role-group="penempatan"]');
                if (penempatanGroup) {
                    penempatanGroup.classList.toggle('d-none', !requiresPlacement);
                }

                const departemenGroup = document.querySelector('[data-role-group="departemen"]');
                if (departemenGroup) {
                    departemenGroup.classList.toggle('d-none', !isStudent && !isSupervisor);
                }

                // Jika login sebagai admin_unit_kerja, kunci jenis & unit kerja — tidak bisa diubah
                if (!IS_UNIT_ADMIN) {
                    if (jenisUnitKerjaInput) {
                        jenisUnitKerjaInput.disabled = !requiresPlacement;

                        if (isDoctor || isUnitAdmin) {
                            jenisUnitKerjaInput.value = 'Puskesmas';
                        }

                        const dinasOption = jenisUnitKerjaInput.querySelector('option[value="Dinas_Kesehatan"]');
                        if (dinasOption) {
                            dinasOption.hidden = isDoctor || isUnitAdmin;
                            dinasOption.disabled = isDoctor || isUnitAdmin;
                        }
                    }
                }

                const nipInput = document.querySelector('#nip');
                if (nipInput) {
                    nipInput.disabled = !isSupervisor;
                    nipInput.required = isSupervisor;
                }

                const internFields = ['#nik', '#nim', '#alamat', '#universitas'];
                internFields.forEach((selector) => {
                    const el = document.querySelector(selector);
                    if (el) {
                        el.disabled = !isIntern;
                        el.required = isIntern;
                    }
                });

                updateUnitKerjaOptions();
                updateDepartemenOptions();
            }

            function updateUnitKerjaOptions() {
                const jenisUnitKerjaInput = document.querySelector('#jenisUnitKerja');
                const unitType = jenisUnitKerjaInput ? jenisUnitKerjaInput.value : '';
                const unitKerjaInput = document.querySelector('#namaUnitKerja');
                if (!unitKerjaInput) return;

                const hasUnitForType = [...unitKerjaInput.querySelectorAll('option[data-jenis]')]
                    .some((option) => option.dataset.jenis === unitType);

                unitKerjaInput.querySelectorAll('option[data-jenis]').forEach((option) => {
                    const isSelectedType = option.dataset.jenis === unitType;
                    option.hidden = !isSelectedType;
                    option.disabled = !isSelectedType;
                });

                const defaultOption = unitKerjaInput.querySelector('option:not([data-jenis])');
                if (defaultOption) {
                    defaultOption.textContent = unitType && !hasUnitForType
                        ? 'Belum ada unit kerja untuk jenis ini'
                        : 'Pilih unit kerja';
                }

                const selectedUnit = unitKerjaInput.selectedOptions[0];
                if (selectedUnit?.dataset.jenis && selectedUnit.dataset.jenis !== unitType) {
                    unitKerjaInput.value = '';
                }

                // Jika login sebagai admin_unit_kerja, unit kerja dikunci — tidak perlu update
                if (IS_UNIT_ADMIN) return;

                const roleEl = document.querySelector('#role');
                const role = roleEl ? roleEl.value : '';
                const requiresUnitKerja = unitType === 'Puskesmas'
                    && ['admin_unit_kerja', 'dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang']
                        .includes(role);
                unitKerjaInput.disabled = !requiresUnitKerja;
                unitKerjaInput.required = requiresUnitKerja;

                const unitKerjaGroup = document.querySelector('[data-role-group="unit-kerja"]');
                if (unitKerjaGroup) {
                    unitKerjaGroup.classList.toggle('d-none', !requiresUnitKerja);
                }

                const unitKerjaLabel = document.querySelector('#unitKerjaLabel');
                if (unitKerjaLabel) {
                    unitKerjaLabel.textContent = unitType === 'Puskesmas'
                        ? 'Puskesmas'
                        : 'Unit Kerja Dinas Kesehatan';
                }
            }

            function updateDepartemenOptions() {
                const jenisUnitKerjaInput = document.querySelector('#jenisUnitKerja');
                const unitType = jenisUnitKerjaInput ? jenisUnitKerjaInput.value : '';
                const roleEl = document.querySelector('#role');
                const role = roleEl ? roleEl.value : '';
                const unitKerjaInput = document.querySelector('#namaUnitKerja');
                const unitKerjaId = unitKerjaInput?.selectedOptions[0]?.dataset.unitKerjaId;
                const departmentInput = document.querySelector('#namaDepartemen');
                if (!departmentInput) return;

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

                const departemenLabel = document.querySelector('#departemenLabel');
                if (departemenLabel) {
                    departemenLabel.textContent = role === 'pembimbing_lapangan'
                        && unitType === 'Puskesmas'
                        ? 'Departemen (opsional)'
                        : 'Departemen';
                }
            }

            const jenisUnitKerjaEl = document.querySelector('#jenisUnitKerja');
            if (jenisUnitKerjaEl) {
                jenisUnitKerjaEl.addEventListener('change', function () {
                    handleSelectRole();
                });
            }

            const namaUnitKerjaEl = document.querySelector('#namaUnitKerja');
            if (namaUnitKerjaEl) {
                namaUnitKerjaEl.addEventListener('change', function () {
                    updateDepartemenOptions();
                });
            }

            handleSelectRole();
        </script>
    @endpush
</x-home-layout>
