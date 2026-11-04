@php
    $lokasiMagang = $user->puskesmas?->nama ?? $user->departemen?->nama ?? '-';
    $jenisPenempatan = $user->puskesmas_id ? 'Puskesmas' : ($user->departemen_id ? 'Departemen' : '-');
    $bulanRekapan = $daftarLog
        ->sortBy(fn ($log) => $log->tanggal->format('Y-m-d'))
        ->groupBy(fn ($log) => $log->tanggal->format('Y-m'));
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Logbook Mahasiswa</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            color: #111;
        }

        .month-page {
            padding: 1.5rem;
            page-break-after: always;
        }

        .month-page:last-child {
            page-break-after: auto;
        }

        .letterhead {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            width: min(100%, 900px);
            margin: 0 auto;
            padding-bottom: .5rem;
            border-bottom: 4px solid #111;
            text-align: center;
        }

        .letterhead img {
            width: 72px;
            height: auto;
        }

        .letterhead p {
            margin: .15rem 0;
        }

        .identity {
            display: flex;
            justify-content: center;
            gap: 4rem;
            margin: 1rem 0 2rem;
        }

        .identity table {
            border-collapse: collapse;
        }

        .identity td {
            padding: .2rem .4rem;
        }

        h1,
        h2 {
            margin: 1rem 0 .25rem;
            text-align: center;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .report-table th,
        .report-table td {
            padding: .4rem;
            border: 1px solid #111;
            vertical-align: top;
        }

        .report-table th {
            text-align: center;
        }

        .date-column,
        .number-column {
            white-space: nowrap;
            text-align: center;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            .month-page {
                padding: 1.5rem;
            }

            a {
                color: inherit;
                text-decoration: none;
            }
        }
    </style>
</head>

<body>
    @forelse ($bulanRekapan as $bulan => $daftarLogBulan)
        <section class="month-page">
            <header class="letterhead">
                <img src="{{ asset('images/City_of_Surabaya_Logo.webp') }}" alt="Logo Pemerintah Kota Surabaya">
                <div>
                    <p>PEMERINTAH KOTA SURABAYA</p>
                    <p><strong>DINAS KESEHATAN</strong></p>
                    <small>Jalan Jemursari No. 197, Surabaya Telepon (031) 8439372</small>
                </div>
                <img src="{{ asset('images/Logo_Puskesmas.jpeg') }}" alt="Logo Puskesmas">
            </header>

            <main>
                <h1>LOGBOOK KEGIATAN HARIAN MAHASISWA MAGANG</h1>
                <h2>BULAN: {{ $daftarLogBulan->first()->tanggal->locale('id')->isoFormat('MMMM YYYY') }}</h2>

                <div class="identity">
                    <table>
                        <tbody>
                            <tr>
                                <td>NAMA</td>
                                <td>:</td>
                                <td>{{ $user->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>NIM</td>
                                <td>:</td>
                                <td>{{ $user->nim ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>UNIVERSITAS</td>
                                <td>:</td>
                                <td>{{ $user->universitas ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table>
                        <tbody>
                            <tr>
                                <td>JENIS PENEMPATAN</td>
                                <td>:</td>
                                <td>{{ $jenisPenempatan }}</td>
                            </tr>
                            <tr>
                                <td>UNIT KERJA</td>
                                <td>:</td>
                                <td>{{ $lokasiMagang }}</td>
                            </tr>
                            <tr>
                                <td>PERIODE</td>
                                <td>:</td>
                                <td>{{ $tanggalLogMulai->toDateString() }} - {{ $tanggalLogAkhir->toDateString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <table class="report-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama kegiatan</th>
                            <th>Deskripsi kegiatan</th>
                            <th>Pembimbing</th>
                            <th>Dokumentasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarLogBulan as $logMahasiswa)
                            <tr>
                                <td class="number-column">{{ $loop->iteration }}</td>
                                <td class="date-column">{{ $logMahasiswa->tanggal->isoFormat('YYYY-MM-DD') }}</td>
                                <td>{{ $logMahasiswa->nama_kegiatan }}</td>
                                <td>{{ $logMahasiswa->deskripsi }}</td>
                                <td>{{ $logMahasiswa->pembimbingLapangan?->nama ?? '-' }}</td>
                                <td>
                                    @if ($logMahasiswa->dokumentasi)
                                        <a href="{{ asset('storage/' . $logMahasiswa->dokumentasi) }}">
                                            {{ basename($logMahasiswa->dokumentasi) }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </main>
        </section>
    @empty
        <section class="month-page">
            <header class="letterhead">
                <img src="{{ asset('images/City_of_Surabaya_Logo.webp') }}" alt="Logo Pemerintah Kota Surabaya">
                <div>
                    <p>PEMERINTAH KOTA SURABAYA</p>
                    <p><strong>DINAS KESEHATAN</strong></p>
                    <small>Jalan Jemursari No. 197, Surabaya Telepon (031) 8439372</small>
                </div>
                <img src="{{ asset('images/Logo_Puskesmas.jpeg') }}" alt="Logo Puskesmas">
            </header>
            <h1>LOGBOOK KEGIATAN HARIAN MAHASISWA MAGANG</h1>
            <h2>{{ $tanggalLogMulai->toDateString() }} - {{ $tanggalLogAkhir->toDateString() }}</h2>
            <p style="text-align: center;">Tidak ada log yang selesai pada periode ini.</p>
        </section>
    @endforelse

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>

</html>
