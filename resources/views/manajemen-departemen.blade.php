<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3">

        {{-- Notifikasi sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Notifikasi error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <p class="mb-0 p-2 fw-bold" style="background-color: #283618; color: #FEFAE0;">
                Filter
            </p>

            <form id="filterForm" style="background-color: #606C38;" class="p-3">
                <label style="color: #FEFAE0;" class="fw-bold form-label" for="tanggalMulaiPembuatanDepartemen">
                    Tanggal Pembuatan Departemen
                </label>

                <div class="input-group">
                    <input class="form-control"
                        type="date"
                        name="tanggalMulaiPembuatanDepartemen"
                        id="tanggalMulaiPembuatanDepartemen"
                        value="{{ request('tanggalMulaiPembuatanDepartemen') }}">

                    <input class="form-control ms-2"
                        type="date"
                        name="tanggalAkhirPembuatanDepartemen"
                        id="tanggalAkhirPembuatanDepartemen"
                        value="{{ request('tanggalAkhirPembuatanDepartemen') }}">
                </div>

                <label style="color: #FEFAE0;" class="fw-bold mt-3 form-label" for="filterUnitKerja">
                    Unit Kerja
                </label>

                <input class="form-control"
                    list="unitKerjaDatalist"
                    type="text"
                    name="unit_kerja"
                    id="filterUnitKerja"
                    placeholder="Pilih unit kerja"
                    value="{{ request('unit_kerja') ?? (is_array(request('unit_kerjas')) ? request('unit_kerjas')[0] : request('unit_kerjas')) }}">

                <datalist id="unitKerjaDatalist">
                    @foreach (($unitKerjas ?? []) as $unitkerja)
                        <option value="{{ $unitkerja->nama }}">
                            {{ $unitkerja->nama }}
                        </option>
                    @endforeach
                </datalist>

                <div class="d-flex align-items-center gap-2 mt-4">
                    <button class="btn btn-primary" type="submit" id="filterButton">
                        Terapkan Filter
                    </button>

                    <button class="btn btn-outline-light" type="button" id="resetFilterButton">
                        Reset Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Header dan tombol tambah --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Daftar Departemen</h4>
                <p class="text-muted mb-0">
                    Daftar departemen Dinas Kesehatan dan Puskesmas.
                </p>
            </div>

            <a href="{{ route('manajemen-departemen.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-square text-white"></i>
                Tambah Departemen
            </a>
        </div>

        {{-- Tabel departemen server side --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="manajemen-departemen">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Departemen</th>
                        <th>Unit Kerja</th>
                        <th>Jenis Unit Kerja</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </section>

    @push('scripts')
        <script>
            let departemenTable = new DataTable('#manajemen-departemen', {
                processing: true,
                serverSide: true,
                ajax: {
                    url: @json(route('manajemen-departemen.index')),
                    data: function(d) {
                        d.tanggalMulaiPembuatanDepartemen = document.querySelector('#tanggalMulaiPembuatanDepartemen')?.value;
                        d.tanggalAkhirPembuatanDepartemen = document.querySelector('#tanggalAkhirPembuatanDepartemen')?.value;
                        d.unit_kerja = document.querySelector('#filterUnitKerja')?.value;
                        d.departemen = document.querySelector('#filterDepartemen')?.value;
                    }
                },
                columns: [
                    { data: 'no', orderable: false, searchable: false },
                    { data: 'nama', name: 'nama' },
                    { data: 'unit_kerja', name: 'unit_kerja' },
                    { data: 'jenis_unit_kerja', name: 'jenis_unit_kerja', orderable: false },
                    { data: 'tanggal_dibuat', name: 'created_at' },
                    { data: 'aksi', orderable: false, searchable: false }
                ],
                columnDefs: [
                    { targets: [0, 5], className: 'dt-nowrap' }
                ],
                order: [
                    [4, 'desc']
                ],
                language: {
                    emptyTable: 'Belum ada data departemen.',
                    zeroRecords: 'Tidak ditemukan data departemen yang sesuai.',
                    processing: 'Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ departemen',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 departemen',
                    infoFiltered: '(disaring dari _MAX_ total departemen)',
                    paginate: {
                        next: '>',
                        previous: '<'
                    }
                }
            });

            document.querySelector('#filterForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                departemenTable.draw();
            });

            document.querySelector('#resetFilterButton')?.addEventListener('click', function() {
                document.querySelector('#tanggalMulaiPembuatanDepartemen').value = '';
                document.querySelector('#tanggalAkhirPembuatanDepartemen').value = '';
                document.querySelector('#filterUnitKerja').value = '';
                departemenTable.draw();
            });
        </script>
    @endpush
</x-home-layout>
