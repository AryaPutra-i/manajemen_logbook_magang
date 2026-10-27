<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3">
        @session('status')
            <div class="alert alert-success">
                {{ $value }}
            </div>
        @endsession

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
        @endif
        </div>
        <div id="importAlertSlot"></div>

        <div class="row g-3">
            <div class="col-xxl-3 d-flex flex-column gap-3">

                <div class="input-group">
                    <input class="form-control" placeholder="Search..." type="text" name="search" id="search">
                    <button id="searchButton" class="btn btn-primary"><i class="bi bi-search text-white"></i>
                        Search</button>
                </div>

                <div>
                    <p class="mb-0 p-2 fw-bold" style="background-color: #283618; color: #FEFAE0;">Filter</p>

                    <div style="background-color: #606C38;" class="p-3">
                        <label style="color: #FEFAE0;" class="fw-bold form-label"
                            for="tanggalMulaiPembuatanAkun">Tanggal
                            Pembuatan Akun</label>
                        <div class="input-group">
                            <input class="form-control" type="text" name="tanggalMulaiPembuatanAkun"
                                id="tanggalMulaiPembuatanAkun">
                            <input class="form-control ms-2" type="text" name="tanggalAkhirPembuatanAkun"
                                id="tanggalAkhirPembuatanAkun">
                        </div>

                        <label style="color: #FEFAE0;" class="fw-bold mt-3" for="username">Username</label>
                        <input class="form-control" list="usernameDatalist" type="text" name="username"
                            id="username" placeholder="Pilih username">
                        <datalist id="usernameDatalist">
                            @foreach ($users as $user)
                                <option value="{{ $user->username }}">
                            @endforeach
                        </datalist>

                        <button class="btn btn-primary mt-4" id="filterButton">Terapkan Filter</button>
                    </div>
                </div>
            </div>

            <div class="col-xxl-9 d-flex flex-column gap-xl-3">
                <div class="d-flex justify-content-end justify-content-xxl-start gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#uploadAccountsDialog">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-white"></i>
                        Import Akun
                    </button>
                    <a href="{{ route('akun.create') }}" class="btn btn-primary" role="button">
                        <i class="bi bi-plus-square text-white"></i>
                        Tambah Akun
                    </a>
                </div>
                <x-upload-accounts-dialog></x-upload-accounts-dialog>


                <table class="table" id="manajemen-akun">
                    <thead>
                        <tr>
                            <th class="text-start">Tanggal Dibuat</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->created_at->isoFormat('YYYY-MM-DD') }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->nama }}</td>
                                <td>{{ $user->role }}</td>
                                <td>
                                    <form class="d-inline"
                                        action="{{ route('akun.reset-password', ['user' => $user->id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-warning" type="submit"><i class="bi bi-key-fill"></i>
                                            Reset
                                            Password</button>
                                    </form>
                                    <a href="{{ route('akun.edit', ['user' => $user->id]) }}" class="btn btn-success"><i
                                            class="bi bi-pencil-square text-white"></i>
                                        Edit</a>
                                    <form class="d-inline" method="POST"
                                        action="{{ route('akun.destroy', ['id' => $user->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" @if ($user->role === 'admin') disabled @endif
                                            type="submit"><i class="bi bi-trash-fill text-white"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </section>

    @push('scripts')
        <script>
            let table = new DataTable('#manajemen-akun', {
                searching: true,
                layout: {
                    topEnd: null
                },
                columnDefs: [{
                    targets: [0, 4],
                    className: 'dt-nowrap'
                }, {
                    targets: 0,
                    className: 'dt-left'
                }],
                scrollX: true,
                order: [
                    [0, 'desc']
                ]
            });

            let minDate;
            let maxDate;

            DataTable.ext.search.push(function(settings, data, dataIndex) {
                let min = minDate.val();
                let max = maxDate.val();
                let date = new Date(data[0]);

                if (
                    (min === null && max === null) ||
                    (min === null && date <= max) ||
                    (min <= date && max === null) ||
                    (min <= date && date <= max)
                ) {
                    return true;
                }

                return false;
            });


            minDate = new DateTime('#tanggalMulaiPembuatanAkun', {
                format: 'YYYY-MM-DD'
            });
            maxDate = new DateTime('#tanggalAkhirPembuatanAkun', {
                format: 'YYYY-MM-DD'
            });

            document.querySelector('#searchButton').addEventListener('click', function() {
                table.search(document.querySelector('#search').value).draw();
            });

            document.querySelector('#search').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    document.querySelector('#searchButton').click();
                }
            });

            document.querySelector('#filterButton').addEventListener('click', function() {
                table.search(document.querySelector('#username').value).draw();
            });
        </script>
    @endpush
</x-home-layout>
