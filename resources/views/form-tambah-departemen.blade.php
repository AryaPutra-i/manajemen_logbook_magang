
<x-home-layout>
    <section class="p-4">

        {{-- Judul --}}
        <div class="mb-4">
            <h3 class="fw-bold">
                {{ $departemen ? 'Edit Departemen' : 'Tambah Departemen' }}
            </h3>

            <p class="text-muted">
                {{ $departemen
                    ? 'Perbarui informasi departemen.'
                    : 'Tambahkan departemen baru pada Dinas Kesehatan atau Puskesmas.' }}
            </p>
        </div>

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

        {{-- Form --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <form
                    action="{{ $departemen
                        ? route('manajemen-departemen.update', $departemen->id)
                        : route('manajemen-departemen.store') }}"
                    method="POST">

                    @csrf

                    @if ($departemen)
                        @method('PUT')
                    @endif

                    {{-- Unit Kerja --}}
                    <div class="mb-4">
                        <label for="unit_kerja_id" class="form-label fw-bold">
                            Unit Kerja <span class="text-danger">*</span>
                        </label>

                        <select
                            name="unit_kerja_id"
                            id="unit_kerja_id"
                            class="form-select @error('unit_kerja_id') is-invalid @enderror"
                            required>

                            <option value="">Pilih Unit Kerja</option>

                            @foreach ($unitKerjas as $unit)
                                <option
                                    value="{{ $unit->id }}"
                                    {{ old('unit_kerja_id', $departemen?->unit_kerja_id) == $unit->id ? 'selected' : '' }}>

                                    {{ $unit->nama }}
                                    -
                                    {{ $unit->jenis === 'Dinas_Kesehatan'
                                        ? 'Dinas Kesehatan'
                                        : 'Puskesmas' }}

                                </option>
                            @endforeach

                        </select>

                        @error('unit_kerja_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Pilih Dinas Kesehatan atau Puskesmas
                            yang memiliki departemen ini.
                        </small>
                    </div>

                    {{-- Nama Departemen --}}
                    <div class="mb-4">
                        <label for="nama" class="form-label fw-bold">
                            Nama Departemen <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama"
                            id="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            placeholder="Contoh: Poli Umum, Sekretariat, Laboratorium"
                            value="{{ old('nama', $departemen?->nama) }}"
                            required>

                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <hr>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-end gap-2">

                        <a href="{{ route('manajemen-departemen.index') }}"
                            class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i>

                            {{ $departemen
                                ? 'Simpan Perubahan'
                                : 'Tambah Departemen' }}
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </section>
</x-home-layout>
