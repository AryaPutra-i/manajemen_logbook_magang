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

        <form method="POST" action="{{ route('logbook.kategori.store') }}" class="input-group" style="max-width: 768px;">
            @csrf
            <input required class="form-control" type="text" name="nama" id="nama" maxlength="255"
                value="{{ old('nama') }}" placeholder="Nama kategori pelayanan kesehatan">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-plus-square text-white"></i> Tambah
            </button>
        </form>

        <table class="table" id="kategori-pelayanan-kesehatan">
            <thead>
                <tr>
                    <th class="text-start">Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($daftarKategoriPelayananKesehatan as $kategori)
                    <tr>
                        <td>{{ $kategori->nama }}</td>
                        <td>
                            <form class="d-inline" method="POST"
                                action="{{ route('logbook.kategori.destroy', ['kategoriPelayananKesehatan' => $kategori->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">
                                    <i class="bi bi-trash-fill text-white"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    @push('scripts')
        <script>
            new DataTable('#kategori-pelayanan-kesehatan', {
                searching: true,
                layout: {
                    topEnd: null
                },
                order: [
                    [0, 'asc']
                ]
            });
        </script>
    @endpush
</x-home-layout>
