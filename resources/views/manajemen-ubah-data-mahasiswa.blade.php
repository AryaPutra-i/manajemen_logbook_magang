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

        <h2>Manajemen Ubah Data Mahasiswa</h2>
        <div class="table-responsive">
            <table class="table" id="manajemen-ubah-data-mahasiswa">
                <thead>
                    <tr>
                        <th>Nama mahasiswa</th>
                        <th>Username</th>
                        <th>Departemen</th>
                        <th>Status perubahan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($daftarPerubahanData as $perubahanData)
                        @php($mahasiswaMagang = $perubahanData->mahasiswaMagang)
                        @if ($mahasiswaMagang)
                            <tr>
                                <td>{{ $mahasiswaMagang->nama }}</td>
                                <td>{{ $mahasiswaMagang->username }}</td>
                                <td>{{ $mahasiswaMagang->departemen?->nama ?? '-' }}</td>
                                <td>
                                    @if ($mahasiswaMagang->is_profile_change_pending)
                                        <span class="badge rounded-pill text-bg-warning">Pending</span>
                                    @else
                                        <span class="badge rounded-pill text-bg-success">Selesai</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('ubah-data-mahasiswa.show', ['mahasiswaMagang' => $mahasiswaMagang->id]) }}"
                                        class="btn btn-warning text-white">
                                        <i class="bi bi-eye text-white"></i> Detail
                                    </a>
                                    <form class="d-inline" method="POST"
                                        action="{{ route('ubah-data-mahasiswa.verify', ['mahasiswaMagang' => $mahasiswaMagang->id]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-success" type="submit"
                                            @disabled(!$mahasiswaMagang->is_profile_change_pending)>Verifikasi</button>
                                    </form>
                                    <form class="d-inline" method="POST"
                                        action="{{ route('ubah-data-mahasiswa.reject', ['mahasiswaMagang' => $mahasiswaMagang->id]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-danger" type="submit"
                                            @disabled(!$mahasiswaMagang->is_profile_change_pending)>Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @push('scripts')
        <script>
            new DataTable('#manajemen-ubah-data-mahasiswa', {
                searching: true,
                layout: { topEnd: null },
                language: { emptyTable: 'Belum ada pengajuan perubahan data.' },
                scrollX: true,
                order: [[0, 'asc']]
            });
        </script>
    @endpush
</x-home-layout>
