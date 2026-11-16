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
                                <option value="" @selected(!old('role')) disabled>Pilih role</option>
                                <option value="dokter_magang" @selected(old('role') === 'dokter_magang')>Dokter Magang</option>
                                <option value="pembimbing_lapangan" @selected(old('role') === 'pembimbing_lapangan')>Pembimbing Lapangan</option>
                                <option value="mahasiswa_magang" @selected(old('role') === 'mahasiswa_magang')>Mahasiswa Magang</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role'), ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true) ? '' : 'd-none' }}"
                    data-role-group="penempatan">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="jenisUnitKerja">Jenis Unit Kerja</label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="jenisUnitKerja" id="jenisUnitKerja">
                            <option value="Puskesmas" @selected(old('jenisUnitKerja', 'Puskesmas') === 'Puskesmas')>Puskesmas</option>
                        </select>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role'), ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang'], true) ? '' : 'd-none' }}"
                    data-role-group="unit-kerja">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="namaUnitKerja">
                        <span id="unitKerjaLabel">Puskesmas</span>
                    </label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="namaUnitKerja" id="namaUnitKerja">
                            <option value="" disabled @selected(!old('namaUnitKerja'))>Pilih puskesmas</option>
                            @foreach ($daftarUnitKerja as $unitKerja)
                                <option value="{{ $unitKerja->nama }}" data-unit-kerja-id="{{ $unitKerja->id }}"
                                    data-jenis="{{ $unitKerja->jenis }}"
                                    @selected(old('namaUnitKerja') === $unitKerja->nama)>
                                    {{ $unitKerja->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row align-items-center g-2 mb-3 {{ in_array(old('role'), ['pembimbing_lapangan', 'mahasiswa_magang'], true) ? '' : 'd-none' }}"
                    data-role-group="departemen">
                    <label class="col-lg-3 col-form-label mb-1 my-lg-auto" for="namaDepartemen">
                        <span id="departemenLabel">Departemen</span>
                    </label>
                    <div class="col-lg-9">
                        <select class="mb-1 mb-lg-3 form-select" name="namaDepartemen" id="namaDepartemen">
                            <option value="" @selected(!old('namaDepartemen'))>Pilih departemen</option>
                            @foreach ($daftarUnitKerja as $unitKerja)
                                @if ($unitKerja->jenis === 'Puskesmas' && $unitKerja->departemens->isNotEmpty())
                                    <optgroup label="{{ $unitKerja->nama }}" data-unit-kerja-id="{{ $unitKerja->id }}"
                                        data-jenis="{{ $unitKerja->jenis }}">
                                    @foreach ($unitKerja->departemens as $departemen)
                                        <option value="{{ $departemen->nama }}"
                                            @selected(old('namaUnitKerja') === $unitKerja->nama && old('namaDepartemen') === $departemen->nama)>
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
            function handleSelectRole() {
                const selectedRole = document.querySelector('#role').value;
                const isDoctor = selectedRole === 'dokter_magang';
                const isSupervisor = selectedRole === 'pembimbing_lapangan';
                const isStudent = selectedRole === 'mahasiswa_magang';
                const isIntern = isDoctor || isStudent;
                const requiresPlacement = isDoctor || isSupervisor || isStudent;
                const jenisUnitKerjaInput = document.querySelector('#jenisUnitKerja');
                const unitKerjaInput = document.querySelector('#namaUnitKerja');

                document.querySelector('[data-role-group="penempatan"]').classList.toggle('d-none', !requiresPlacement);
                document.querySelector('[data-role-group="departemen"]').classList.toggle('d-none', !isStudent && !isSupervisor);
                jenisUnitKerjaInput.disabled = !requiresPlacement;
                unitKerjaInput.required = requiresPlacement;

                jenisUnitKerjaInput.value = 'Puskesmas';
                updateUnitKerjaOptions();
                updateDepartemenOptions();

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
            }

            function updateUnitKerjaOptions() {
                const unitKerjaInput = document.querySelector('#namaUnitKerja');
                unitKerjaInput.querySelectorAll('option[data-jenis]').forEach((option) => {
                    option.hidden = false;
                    option.disabled = false;
                });

                const requiresUnitKerja = ['dokter_magang', 'pembimbing_lapangan', 'mahasiswa_magang']
                    .includes(document.querySelector('#role').value);
                unitKerjaInput.disabled = !requiresUnitKerja;
                unitKerjaInput.required = requiresUnitKerja;
                document.querySelector('[data-role-group="unit-kerja"]').classList.toggle('d-none', !requiresUnitKerja);
                document.querySelector('#unitKerjaLabel').textContent = 'Puskesmas';
            }

            function updateDepartemenOptions() {
                const role = document.querySelector('#role').value;
                const unitKerjaId = document.querySelector('#namaUnitKerja').selectedOptions[0]?.dataset
                    .unitKerjaId;
                const departmentInput = document.querySelector('#namaDepartemen');

                departmentInput.querySelectorAll('optgroup').forEach((group) => {
                    const isSelectedUnit = group.dataset.unitKerjaId === unitKerjaId;
                    group.hidden = !isSelectedUnit;
                    group.disabled = !isSelectedUnit;
                });

                const selectedGroup = departmentInput.selectedOptions[0]?.parentElement;
                if (!selectedGroup || selectedGroup.hidden) {
                    departmentInput.value = '';
                }
                const canChooseDepartment = ['mahasiswa_magang', 'pembimbing_lapangan'].includes(role);
                departmentInput.disabled = !canChooseDepartment || !unitKerjaId;
                departmentInput.required = role === 'mahasiswa_magang';
                document.querySelector('#departemenLabel').textContent = 'Departemen';
            }

            document.querySelector('#namaUnitKerja').addEventListener('change', function () {
                updateDepartemenOptions();
            });

            handleSelectRole();
        </script>
    @endpush
</x-home-layout>
