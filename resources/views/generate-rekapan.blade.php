<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekapan Logbook</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        .month-page {
            padding: 2rem;
            page-break-after: always;
        }

        .month-page:last-child {
            page-break-after: auto;
        }

        td,
        th {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        .rekapan-table {
            table-layout: auto;
        }

        .rekapan-table .day-col {
            width: 1.5rem;
            min-width: 1.5rem;
            max-width: 1.5rem;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
        }

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    @foreach ($grupBulan as $bulan)
        <section class="month-page">
            <header class="mx-auto flex items-end w-[640px] justify-between pb-2 border-b-4 border-b-black">
                <img class="w-20 aspect-auto" src="{{ asset('images/City_of_Surabaya_Logo.webp') }}" alt="">

                <div class="flex flex-col items-center text-center">
                    <p>PEMERINTAH KOTA SURABAYA</p>
                    <p class="font-bold text-lg">DINAS KESEHATAN</p>
                    <address class="text-xs not-italic">
                        <p>Jalan Jemursari No. 197
                        <p>Surabaya Telepon (031) 8439372</p>
                        <p>
                            Laman <a href="https://surabaya.go.id">surabaya.go.id</a>,
                            Pos-el: <a href="mailto:dinkes@surabaya.go.id">dinkes@surabaya.go.id</a>
                        </p>
                    </address>
                </div>

                <img class="w-20 aspect-auto" src="{{ asset('images/Logo_Puskesmas.jpeg') }}" alt="">
            </header>

            <main class="mt-4">
                <section>
                    <h1 class="text-center font-bold">LOGBOOK KEGIATAN HARIAN DOKTER</h1>
                    <h2 class="text-center font-bold mt-1">BULAN: {{ $bulan['label'] }}</h2>

                    <article class="[&_td]:px-2 [&_th]:px-2 flex mt-2 justify-center gap-16">
                        <table>
                            <tbody>
                                <tr>
                                    <td>NAMA</td>
                                    <td>:</td>
                                    <td>{{ $user->nama }}</td>
                                </tr>
                                <tr>
                                    <td>NIK</td>
                                    <td>:</td>
                                    <td>{{ $user->nik }}</td>
                                </tr>
                            </tbody>
                        </table>


                        <table>
                            <tbody>
                                <tr>
                                    <td>UNIT KERJA</td>
                                    <td>:</td>
                                    <td>{{ $user->puskesmas->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>{{ $tanggalLogMulai->toDateString() }} - {{ $tanggalLogAkhir->toDateString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </article>
                </section>

                <section class="text-xs mt-8">
                    <article class="overflow-x-auto">
                        <table
                            class="rekapan-table mx-auto [&_td]:border [&_td]:p-0.5 [&_th]:p-0.5 [&_th]:border [&_td]:border-black [&_th]:border-black">
                            <thead>
                                <tr>
                                    <th rowspan="2">NO</th>
                                    <th rowspan="2">PELAYANAN KESEHATAN</th>
                                    <th colspan="{{ count($bulan['days']) }}">TANGGAL</th>
                                    <th rowspan="2">TOTAL KEGIATAN</th>
                                    <th rowspan="2">KETERANGAN</th>
                                </tr>
                                <tr>
                                    @foreach ($bulan['days'] as $tanggal)
                                        @php($isOutOfRange = $tanggal->lt($tanggalLogMulai) || $tanggal->gt($tanggalLogAkhir))
                                        <th class="day-col {{ $isOutOfRange ? 'bg-gray-300' : '' }}">
                                            {{ $tanggal->day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekap as $kategori => $jumlahPerTanggal)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $kategori }}</td>
                                        @foreach ($bulan['days'] as $tanggal)
                                            @php($isOutOfRange = $tanggal->lt($tanggalLogMulai) || $tanggal->gt($tanggalLogAkhir))
                                            <td class="day-col text-center {{ $isOutOfRange ? 'bg-gray-300' : '' }}">
                                                {{ $isOutOfRange ? '' : $jumlahPerTanggal[$tanggal->toDateString()] ?? '' }}
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            {{ collect($bulan['days'])->sum(fn($d) => $jumlahPerTanggal[$d->toDateString()] ?? 0) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($bulan['days']) + 4 }}" class="text-center">Tidak ada
                                            data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (!empty($rekap))
                                <tfoot>
                                    <tr class="font-bold">
                                        <td colspan="2" class="text-center">TOTAL HARIAN</td>
                                        @foreach ($bulan['days'] as $tanggal)
                                            @php($isOutOfRange = $tanggal->lt($tanggalLogMulai) || $tanggal->gt($tanggalLogAkhir))
                                            <td class="day-col text-center {{ $isOutOfRange ? 'bg-gray-300' : '' }}">
                                                {{ $isOutOfRange ? '' : $totalPerTanggal[$tanggal->toDateString()] ?? 0 }}
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            {{ collect($bulan['days'])->sum(fn($d) => $totalPerTanggal[$d->toDateString()] ?? 0) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </article>
                </section>
            </main>
        </section>
    @endforeach

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
