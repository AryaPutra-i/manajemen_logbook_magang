
<x-home-layout>
    <section class="p-4 w-100 d-flex flex-column gap-3">

        {{-- Notifikasi sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
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

        {{-- Header dan tombol tambah --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Daftar Departemen</h4>
                <p class="text-muted mb-0">
                    Daftar departemen Dinas Kesehatan dan Puskesmas.
                </p>
            </div>

            <a href="{{ route('manajemen-departemen.create') }}"
                class="btn btn-primary">
                <i class="bi bi-plus-square text-white"></i>
                Tambah Departemen
            </a>
        </div>

        {{-- Tabel departemen --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle"
                id="manajemen-departemen">

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

                <tbody class="table-group-divider">

                    @forelse ($departemens as $departemen)
                        <tr>
                            <td>
                                {{ $departemens->firstItem() + $loop->index }}
                            </td>

                            <td>{{ $departemen->nama }}</td>

                            <td>
                                {{ $departemen->unitKerja?->nama ?? '-' }}
                            </td>

                            <td>
                                @if ($departemen->unitKerja)
                                    {{ $departemen->unitKerja->jenis === 'Dinas_Kesehatan'
                                        ? 'Dinas Kesehatan'
                                        : 'Puskesmas' }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $departemen->created_at?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                <div class="d-flex gap-1">

                                    {{-- Edit --}}
                                    <a href="{{ route('manajemen-departemen.edit', $departemen->id) }}"
                                        class="btn btn-success btn-sm">
                                        <i class="bi bi-pencil-square text-white"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form
                                        action="{{ route('manajemen-departemen.destroy', $departemen->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus departemen ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash-fill text-white"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                Belum ada data departemen.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination Laravel --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div class="text-muted">
                Menampilkan {{ $departemens->firstItem() ?? 0 }}
                sampai {{ $departemens->lastItem() ?? 0 }}
                dari {{ $departemens->total() }} departemen
            </div>

            <div>
                {{ $departemens->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </section>
</x-home-layout>
