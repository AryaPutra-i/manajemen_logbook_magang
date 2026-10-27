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
            </div>
        @endif

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
                        <label style="color: #FEFAE0;" class="fw-bold form-label" for="tanggalLogMulai">
                            Tanggal Log
                        </label>
                        <div class="input-group">
                            <input class="form-control" type="text" name="tanggalLogMulai" id="tanggalLogMulai">
                            <input class="form-control ms-2" type="text" name="tanggalLogAkhir" id="tanggalLogAkhir">
                        </div>

                        @if ($user->role === 'admin')
                            <label style="color: #FEFAE0;" class="fw-bold mt-3" for="puskesmas">Puskesmas</label>
                            <input class="form-control" list="puskesmasDatalist" type="text" name="puskesmas"
                                id="puskesmas" placeholder="Pilih puskesmas">
                            <datalist id="puskesmasDatalist">
                                @foreach ($daftarLogMagang->pluck('puskesmas.nama')->unique() as $namaPuskesmas)
                                    <option value="{{ $namaPuskesmas }}">
                                @endforeach
                            </datalist>

                            <label style="color: #FEFAE0;" class="fw-bold mt-3" for="username">Username Dokter
                                Magang</label>
                            <input class="form-control" list="usernameDatalist" type="text" name="username"
                                id="username" placeholder="Pilih username">
                            <datalist id="usernameDatalist">
                                @foreach ($daftarLogMagang->pluck('dokterMagang.username')->unique() as $usernameDokterMagang)
                                    <option value="{{ $usernameDokterMagang }}">
                                @endforeach
                            </datalist>
                        @endif

                        <button class="btn btn-primary mt-4" id="filterButton">Terapkan Filter</button>
                    </div>
                </div>
            </div>

            <div class="col-xxl-9 d-flex flex-column gap-xl-3">
                <div class="d-flex justify-content-end justify-content-xxl-start gap-2">
                    @if ($user->role === 'admin')
                        <button target="_blank" onclick="handleGenerateRekapan()" class="btn btn-success">
                            <i class="bi bi-file-earmark-spreadsheet-fill text-white"></i>
                            Generate Rekapan
                        </button>

                        <a href="{{ route('logbook.kategori.index') }}" class="btn btn-primary">
                            <i class="bi bi-tags text-white"></i>
                            Kategori Pelayanan Kesehatan
                        </a>
                    @endif

                    @if ($user->role === 'dokter_magang')
                        <a href="{{ route('logbook.create') }}" type="button" class="btn btn-primary">
                            <i class="bi bi-plus-square text-white"></i>
                            Tambah Log
                        </a>
                    @endif
                </div>


                <table class="table" id="manajemen-logbook">
                    <thead>
                        <tr>
                            <th class="text-start">Tanggal</th>
                            <th>Puskesmas</th>
                            <th>Username</th>
                            <th>Deskripsi</th>
                            <th>Hambatan</th>
                            <th>Dokumentasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($daftarLogMagang as $logMagang)
                            <tr>
                                <td>{{ $logMagang->tanggal->isoFormat('YYYY-MM-DD') }}</td>
                                <td>{{ $logMagang->puskesmas?->nama }}</td>
                                <td>{{ $logMagang->dokterMagang?->username }}</td>
                                <td>{{ $logMagang->deskripsi }}</td>
                                <td>{{ $logMagang->hambatan }}</td>
                                <td><a target="_blank"
                                        href="{{ asset('storage/' . $logMagang->dokumentasi) }}">{{ last(explode('/', $logMagang->dokumentasi)) }}</a>
                                </td>
                                <td>
                                    @switch($logMagang->status_verifikasi)
                                        @case('pending')
                                            <span class="badge rounded-pill text-bg-warning">Pending</span>
                                        @break

                                        @case('revisi')
                                            <span class="badge rounded-pill text-bg-danger">Revisi</span>
                                        @break

                                        @case('selesai')
                                            <span class="badge rounded-pill text-bg-success">Selesai</span>
                                        @break

                                        @default
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('logbook.detail', ['logMagang' => $logMagang->id]) }}"
                                        class="btn btn-warning text-white"><i class="bi bi-eye text-white"></i>
                                        Detail Log</a>


                                    @switch($user->role)
                                        @case('dokter_magang')
                                            <form class="d-inline" method="POST"
                                                action="{{ route('logbook.destroy', ['id' => $logMagang->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">
                                                    <i class="bi bi-trash-fill text-white"></i> Hapus
                                                </button>
                                            </form>
                                        @break

                                        @case('pembimbing_lapangan')
                                            <form class="d-inline" method="POST"
                                                action="{{ route('logbook.verify', ['logMagang' => $logMagang->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button {{ $logMagang->status_verifikasi === 'selesai' ? 'disabled' : '' }}
                                                    class="btn btn-success" type="submit">
                                                    <i class="bi bi-trash-fill text-white"></i> Verifikasi
                                                </button>
                                            </form>
                                            <form class="d-inline" method="POST"
                                                action="{{ route('logbook.revise', ['logMagang' => $logMagang->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button {{ in_array($logMagang->status_verifikasi, ['selesai', 'revisi']) ? 'disabled' : '' }}
                                                    class="btn btn-danger" type="submit">
                                                    <i class="bi bi-trash-fill text-white"></i> Minta Revisi
                                                </button>
                                            </form>
                                        @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            let table = new DataTable('#manajemen-logbook', {
                searching: true,
                layout: {
                    topEnd: null
                },
                columnDefs: [{
                        targets: [0, 7],
                        className: 'dt-nowrap dt-left'
                    },
                    {
                        targets: [3, 4],
                        width: '256px',
                    },
                    {
                        targets: 5,
                        width: '128px',
                    },
                ],
                scrollX: true,
                order: [
                    [0, 'desc']
                ]
            });

            let minDate;
            let maxDate;

            const tanggalLogIndonesia = {
                format: 'YYYY-MM-DD',
                locale: 'id',
                i18n: {
                    clear: 'Hapus',
                    previous: 'Sebelumnya',
                    next: 'Berikutnya',
                    months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    weekdays: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    amPm: ['am', 'pm'],
                    hours: 'Jam',
                    minutes: 'Menit',
                    seconds: 'Detik',
                    unknown: '-',
                    today: 'Hari Ini',
                    selected: 'Terpilih',
                },
            };

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

            minDate = new DateTime('#tanggalLogMulai', tanggalLogIndonesia);
            maxDate = new DateTime('#tanggalLogAkhir', tanggalLogIndonesia);

            document.querySelector('#searchButton').addEventListener('click', function() {
                table.search(document.querySelector('#search').value).draw();
            });

            document.querySelector('#search').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    document.querySelector('#searchButton').click();
                }
            });

            document.querySelector('#filterButton').addEventListener('click', function() {
                @if ($user->role === 'admin')
                    table.column(1).search(document.querySelector('#puskesmas').value)
                    table.column(2).search(document.querySelector('#username').value)
                @endif

                table.draw();

                var filteredData = table.rows({
                    search: 'applied'
                }).data().toArray();
            });

            function handleGenerateRekapan() {
                const username = document.querySelector('#username').value;
                const tanggalLogMulai = document.querySelector('#tanggalLogMulai').value;
                const tanggalLogAkhir = document.querySelector('#tanggalLogAkhir').value;

                window.open(
                    `/manajemen-logbook/generate-rekapan?username=${username}&tanggalLogMulai=${tanggalLogMulai}&tanggalLogAkhir=${tanggalLogAkhir}`,
                    '_blank')
            }
        </script>
    @endpush
</x-home-layout>
