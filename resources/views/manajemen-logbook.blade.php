<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3">
        @session('status')
            <div class="alert alert-success">{{ $value }}</div>
        @endsession

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-xxl-3 d-flex flex-column gap-3">
                <div class="input-group">
                    <input class="form-control" placeholder="Search..." type="text" id="search">
                    <button id="searchButton" class="btn btn-primary" type="button">
                        <i class="bi bi-search text-white"></i> Search
                    </button>
                </div>

                <div>
                    <p class="mb-0 p-2 fw-bold" style="background-color: #283618; color: #FEFAE0;">Filter</p>
                    <div style="background-color: #606C38;" class="p-3">
                        <label style="color: #FEFAE0;" class="fw-bold form-label" for="tanggalLogMulai">Tanggal log</label>
                        <div class="input-group">
                            <input class="form-control" type="text" id="tanggalLogMulai">
                            <input class="form-control ms-2" type="text" id="tanggalLogAkhir">
                        </div>

                        @if ($user->role === 'admin')
                            <label style="color: #FEFAE0;" class="fw-bold mt-3" for="puskesmas">Puskesmas dokter</label>
                            <input class="form-control" list="puskesmasDatalist" type="text" id="puskesmas"
                                placeholder="Pilih puskesmas">
                            <datalist id="puskesmasDatalist">
                                @foreach ($daftarLogDokterMagang->pluck('puskesmas.nama')->filter()->unique() as $namaPuskesmas)
                                    <option value="{{ $namaPuskesmas }}">
                                @endforeach
                            </datalist>

                            <label style="color: #FEFAE0;" class="fw-bold mt-3" for="usernameDokter">Username dokter</label>
                            <input class="form-control" list="usernameDokterDatalist" type="text" id="usernameDokter"
                                placeholder="Pilih username">
                            <datalist id="usernameDokterDatalist">
                                @foreach ($daftarLogDokterMagang->pluck('dokterMagang.username')->filter()->unique() as $usernameDokter)
                                    <option value="{{ $usernameDokter }}">
                                @endforeach
                            </datalist>

                            <label style="color: #FEFAE0;" class="fw-bold mt-3" for="unitMahasiswa">Unit mahasiswa</label>
                            <input class="form-control" list="unitMahasiswaDatalist" type="text" id="unitMahasiswa"
                                placeholder="Pilih puskesmas atau departemen">
                            <datalist id="unitMahasiswaDatalist">
                                @foreach ($daftarLogMahasiswaMagang->map(fn ($log) => $log->puskesmas?->nama ?? $log->departemen?->nama)->filter()->unique() as $unitMahasiswa)
                                    <option value="{{ $unitMahasiswa }}">
                                @endforeach
                            </datalist>

                            <label style="color: #FEFAE0;" class="fw-bold mt-3" for="usernameMahasiswa">Username mahasiswa</label>
                            <input class="form-control" list="usernameMahasiswaDatalist" type="text" id="usernameMahasiswa"
                                placeholder="Pilih username">
                            <datalist id="usernameMahasiswaDatalist">
                                @foreach ($daftarLogMahasiswaMagang->pluck('mahasiswaMagang.username')->filter()->unique() as $usernameMahasiswa)
                                    <option value="{{ $usernameMahasiswa }}">
                                @endforeach
                            </datalist>
                        @endif

                        <button class="btn btn-primary mt-4" id="filterButton" type="button">Terapkan Filter</button>
                    </div>
                </div>
            </div>

            <div class="col-xxl-9 d-flex flex-column gap-4">
                <div class="d-flex flex-wrap justify-content-end justify-content-xxl-start gap-2">
                    @if ($user->role === 'admin')
                        <button onclick="handleGenerateRekapanDokter()" class="btn btn-success" type="button">
                            <i class="bi bi-file-earmark-spreadsheet-fill text-white"></i> Rekap dokter
                        </button>
                        <button onclick="handleGenerateRekapanMahasiswa()" class="btn btn-success" type="button">
                            <i class="bi bi-file-earmark-spreadsheet-fill text-white"></i> Rekap mahasiswa
                        </button>
                        <a href="{{ route('logbook.kategori.index') }}" class="btn btn-primary">
                            <i class="bi bi-tags text-white"></i> Kategori Pelayanan Kesehatan
                        </a>
                    @elseif ($user->role === 'dokter_magang')
                        <a href="{{ route('logbook.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-square text-white"></i> Tambah Log
                        </a>
                    @elseif ($user->role === 'mahasiswa_magang')
                        <a href="{{ route('logbook.mahasiswa.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-square text-white"></i> Tambah Log
                        </a>
                    @endif
                </div>

                @if (in_array($user->role, ['admin', 'dokter_magang', 'pembimbing_lapangan_puskesmas'], true))
                    <div>
                        <h2 class="h4">Log Dokter Magang</h2>
                        <div class="table-responsive">
                            <table class="table" id="manajemen-logbook-dokter">
                                <thead>
                                    <tr>
                                        <th class="text-start">Tanggal</th>
                                        <th>Puskesmas</th>
                                        <th>Username dokter</th>
                                        <th>Deskripsi</th>
                                        <th>Hambatan</th>
                                        <th>Dokumentasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                    @foreach ($daftarLogDokterMagang as $logMagang)
                                        <tr>
                                            <td>{{ $logMagang->tanggal->isoFormat('YYYY-MM-DD') }}</td>
                                            <td>{{ $logMagang->puskesmas?->nama ?? '-' }}</td>
                                            <td>{{ $logMagang->dokterMagang?->username ?? '-' }}</td>
                                            <td>{{ $logMagang->deskripsi }}</td>
                                            <td>{{ $logMagang->hambatan }}</td>
                                            <td>
                                                @if ($logMagang->dokumentasi)
                                                    <a target="_blank" rel="noopener"
                                                        href="{{ asset('storage/' . $logMagang->dokumentasi) }}">
                                                        {{ basename($logMagang->dokumentasi) }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
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
                                                        <span class="badge rounded-pill text-bg-secondary">{{ $logMagang->status_verifikasi }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('logbook.detail', ['logMagang' => $logMagang->id]) }}"
                                                    class="btn btn-warning text-white">
                                                    <i class="bi bi-eye text-white"></i> Detail Log
                                                </a>
                                                @if (in_array($user->role, ['admin', 'dokter_magang'], true))
                                                    <form class="d-inline" method="POST"
                                                        action="{{ route('logbook.destroy', ['id' => $logMagang->id]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger" type="submit">
                                                            <i class="bi bi-trash-fill text-white"></i> Hapus
                                                        </button>
                                                    </form>
                                                @elseif ($user->role === 'pembimbing_lapangan_puskesmas')
                                                    <form class="d-inline" method="POST"
                                                        action="{{ route('logbook.verify', ['logMagang' => $logMagang->id]) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-success" type="submit"
                                                            @disabled($logMagang->status_verifikasi === 'selesai')>Verifikasi</button>
                                                    </form>
                                                    <form class="d-inline" method="POST"
                                                        action="{{ route('logbook.revise', ['logMagang' => $logMagang->id]) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-danger" type="submit"
                                                            @disabled(in_array($logMagang->status_verifikasi, ['selesai', 'revisi'], true))>Minta Revisi</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if (in_array($user->role, ['admin', 'mahasiswa_magang', 'pembimbing_lapangan_puskesmas', 'pembimbing_lapangan_departemen'], true))
                    <div>
                        <h2 class="h4">Log Mahasiswa Magang</h2>
                        <div class="table-responsive">
                            <table class="table" id="manajemen-logbook-mahasiswa">
                                <thead>
                                    <tr>
                                        <th class="text-start">Tanggal</th>
                                        <th>Unit mahasiswa</th>
                                        <th>Username mahasiswa</th>
                                        <th>Nama kegiatan</th>
                                        <th>Deskripsi</th>
                                        <th>Dokumentasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-group-divider">
                                    @foreach ($daftarLogMahasiswaMagang as $logMahasiswa)
                                        <tr>
                                            <td>{{ $logMahasiswa->tanggal->isoFormat('YYYY-MM-DD') }}</td>
                                            <td>{{ $logMahasiswa->puskesmas?->nama ?? $logMahasiswa->departemen?->nama ?? '-' }}</td>
                                            <td>{{ $logMahasiswa->mahasiswaMagang?->username ?? '-' }}</td>
                                            <td>{{ $logMahasiswa->nama_kegiatan }}</td>
                                            <td>{{ $logMahasiswa->deskripsi }}</td>
                                            <td>
                                                @if ($logMahasiswa->dokumentasi)
                                                    <a target="_blank" rel="noopener"
                                                        href="{{ asset('storage/' . $logMahasiswa->dokumentasi) }}">
                                                        {{ basename($logMahasiswa->dokumentasi) }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @switch($logMahasiswa->status_verifikasi)
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
                                                        <span class="badge rounded-pill text-bg-secondary">{{ $logMahasiswa->status_verifikasi }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('logbook.mahasiswa.detail', ['logMagang' => $logMahasiswa->id]) }}"
                                                    class="btn btn-warning text-white">
                                                    <i class="bi bi-eye text-white"></i> Detail Log
                                                </a>
                                                @if ($user->role === 'mahasiswa_magang' && $logMahasiswa->status_verifikasi !== 'selesai')
                                                    <a href="{{ route('logbook.mahasiswa.edit', ['logMagang' => $logMahasiswa->id]) }}"
                                                        class="btn {{ $logMahasiswa->status_verifikasi === 'revisi' ? 'btn-danger' : 'btn-primary' }}">
                                                        @if ($logMahasiswa->status_verifikasi === 'revisi')
                                                            <i class="bi bi-pencil-square"></i> Perbaiki revisi
                                                        @else
                                                            Edit
                                                        @endif
                                                    </a>
                                                @endif
                                                @if (in_array($user->role, ['admin', 'mahasiswa_magang'], true))
                                                    <form class="d-inline" method="POST"
                                                        action="{{ route('logbook.mahasiswa.destroy', ['id' => $logMahasiswa->id]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger" type="submit"
                                                            @disabled($logMahasiswa->status_verifikasi === 'selesai')>
                                                            <i class="bi bi-trash-fill text-white"></i> Hapus
                                                        </button>
                                                    </form>
                                                @elseif (in_array($user->role, ['pembimbing_lapangan_puskesmas', 'pembimbing_lapangan_departemen'], true))
                                                    <form class="d-inline" method="POST"
                                                        action="{{ route('logbook.mahasiswa.verify', ['logMagang' => $logMahasiswa->id]) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-success" type="submit"
                                                            @disabled($logMahasiswa->status_verifikasi === 'selesai')>Verifikasi</button>
                                                    </form>
                                                    <form class="d-inline" method="POST"
                                                        action="{{ route('logbook.mahasiswa.revise', ['logMagang' => $logMahasiswa->id]) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-danger" type="submit"
                                                            @disabled(in_array($logMahasiswa->status_verifikasi, ['selesai', 'revisi'], true))>Minta Revisi</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            const dokterTable = document.querySelector('#manajemen-logbook-dokter')
                ? new DataTable('#manajemen-logbook-dokter', {
                    searching: true,
                    layout: { topEnd: null },
                    language: { emptyTable: 'Belum ada log dokter.' },
                    columnDefs: [
                        { targets: [0, 7], className: 'dt-nowrap dt-left' },
                        { targets: [3, 4], width: '256px' },
                        { targets: 5, width: '128px' },
                    ],
                    scrollX: true,
                    order: [[0, 'desc']]
                })
                : null;
            const mahasiswaTable = document.querySelector('#manajemen-logbook-mahasiswa')
                ? new DataTable('#manajemen-logbook-mahasiswa', {
                    searching: true,
                    layout: { topEnd: null },
                    language: { emptyTable: 'Belum ada log mahasiswa.' },
                    columnDefs: [
                        { targets: [0, 7], className: 'dt-nowrap dt-left' },
                        { targets: [3, 4], width: '256px' },
                        { targets: 5, width: '128px' },
                    ],
                    scrollX: true,
                    order: [[0, 'desc']]
                })
                : null;

            const dateLocale = {
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
            const minDate = new DateTime('#tanggalLogMulai', dateLocale);
            const maxDate = new DateTime('#tanggalLogAkhir', dateLocale);

            DataTable.ext.search.push(function(settings, data) {
                if (!['manajemen-logbook-dokter', 'manajemen-logbook-mahasiswa'].includes(settings.nTable.id)) {
                    return true;
                }

                const min = minDate.val();
                const max = maxDate.val();
                const date = new Date(data[0]);

                return (min === null || min <= date) && (max === null || date <= max);
            });

            document.querySelector('#searchButton').addEventListener('click', function() {
                dokterTable?.search(document.querySelector('#search').value).draw();
                mahasiswaTable?.search(document.querySelector('#search').value).draw();
            });

            document.querySelector('#search').addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    document.querySelector('#searchButton').click();
                }
            });

            document.querySelector('#filterButton').addEventListener('click', function() {
                @if ($user->role === 'admin')
                    dokterTable?.column(1).search(document.querySelector('#puskesmas').value);
                    dokterTable?.column(2).search(document.querySelector('#usernameDokter').value);
                    mahasiswaTable?.column(1).search(document.querySelector('#unitMahasiswa').value);
                    mahasiswaTable?.column(2).search(document.querySelector('#usernameMahasiswa').value);
                @endif
                dokterTable?.draw();
                mahasiswaTable?.draw();
            });

            function openRecap(route, username) {
                const tanggalLogMulai = document.querySelector('#tanggalLogMulai').value;
                const tanggalLogAkhir = document.querySelector('#tanggalLogAkhir').value;

                if (!username || !tanggalLogMulai || !tanggalLogAkhir) {
                    alert('Pilih username dan rentang tanggal untuk membuat rekap.');
                    return;
                }

                const parameters = new URLSearchParams({ username, tanggalLogMulai, tanggalLogAkhir });
                window.open(`${route}?${parameters.toString()}`, '_blank', 'noopener');
            }

            function handleGenerateRekapanDokter() {
                openRecap(@json(route('logbook.generate-rekapan')), document.querySelector('#usernameDokter').value);
            }

            function handleGenerateRekapanMahasiswa() {
                openRecap(@json(route('logbook.mahasiswa.generate-rekapan')), document.querySelector('#usernameMahasiswa').value);
            }
        </script>
    @endpush
</x-home-layout>
