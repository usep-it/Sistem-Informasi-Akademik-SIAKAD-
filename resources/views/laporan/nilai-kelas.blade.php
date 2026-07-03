<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Kelas {{ $kelas->kelas ?? '?' }}</title>
    
    <style>
        /* Ukuran kertas A4 Landscape untuk Rekap Kelas */
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            background-color: #fff;
            margin: 0;
        }

        .page-container {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        /* HEADER KANAN ATAS */
        .top-right-header {
            text-align: right;
            font-size: 9pt;
            color: #555;
            margin-bottom: 10px;
            font-style: italic;
        }

        /* KOP SURAT */
        .kop {
            text-align: center;
            line-height: 1.2;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .kop .logo {
            width: 75px;
            height: 75px;
        }
        .kop h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
        }
        .kop h3 {
            margin: 0;
            font-size: 13pt;
        }
        .kop p {
            font-size: 8.5pt;
            margin: 5px 0 0;
        }

        /* JUDUL LAPORAN */
        .report-title {
            text-align: center;
            margin: 15px 0 20px;
        }
        .report-title h4 {
            margin: 0 0 5px 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-title p {
            margin: 0;
            font-size: 10pt;
        }

        /* TABEL NILAI */
        table.table-nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-nilai th, .table-nilai td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }
        .table-nilai th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 8.5pt;
        }
        .table-nilai td { 
            font-size: 9.5pt; 
        }
        .table-nilai .text-left { text-align: left; padding-left: 8px; }
        .table-nilai .font-weight-bold { font-weight: bold; }
        .table-nilai tr { page-break-inside: avoid; }

        /* KETERANGAN MAPEL */
        .legend-section {
            margin-top: 10px;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }
        .legend-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            display: block;
        }
        .table-legend {
            width: 100%;
            border: none;
        }
        .table-legend td {
            border: none;
            padding: 2px 0;
            width: 25%;
            vertical-align: top;
        }

        /* TANDA TANGAN */
        .signature-section {
            width: 100%;
            margin-top: 30px;
            border: none;
            page-break-inside: avoid;
        }
        .signature-section td {
            border: none;
            text-align: center;
            vertical-align: top;
            font-size: 10.5pt;
            padding: 0;
        }
        .signature-section .signature-space {
            height: 60px;
        }
        .signature-section .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* FOOTER */
        .footer-info {
            margin-top: 20px;
            font-size: 9pt;
            color: #555;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="page-container">

    @php
        // Setting Lokal Indonesia
        \Carbon\Carbon::setLocale('id');
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        
        $mapels = $mapels ?? collect(); 
        $hasMapels = $mapels->count() > 0;
        $tahunAjaran = $tahunAjaran ?? \App\Models\Tahun::where('status', 'Aktif')->first();
        
        // 1. Kalkulasi Nilai Akhir & Rata-rata per Siswa
        $studentData = [];
        foreach ($kelas->siswas as $siswa) {
            $totalNilaiSiswa = 0;
            $countMapelSiswa = 0;
            $nilaiPerMapel = [];

            if ($hasMapels) {
                foreach ($mapels as $mapel) {
                    $grades = collect($siswa->nilai)->where('mapel_id', $mapel->id);
                    $harian = $grades->where('jenis', 'HARIAN')->avg('nilai');
                    $pts = $grades->where('jenis', 'PTS')->first()?->nilai;
                    $pas = $grades->where('jenis', 'PAS')->first()?->nilai;
                    
                    $fmtf = !is_null($harian) ? $harian : null;
                    $smtf = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();
                    
                    $nA = null;
                    if (!is_null($fmtf) && !is_null($smtf)) $nA = ($fmtf * 0.6) + ($smtf * 0.4);
                    elseif (!is_null($fmtf)) $nA = $fmtf;
                    elseif (!is_null($smtf)) $nA = $smtf;
                    
                    $nABulat = !is_null($nA) ? round($nA) : null;
                    $nilaiPerMapel[$mapel->id] = $nABulat;

                    // Gunakan nilai yang sudah dibulatkan untuk perhitungan total/rata
                    if (!is_null($nABulat)) {
                        $totalNilaiSiswa += $nABulat;
                        $countMapelSiswa++;
                    }
                }
            }

            $rataRata = $countMapelSiswa > 0 ? ($totalNilaiSiswa / $countMapelSiswa) : 0;
            // Bulatkan rata-rata sesuai pilihan: tampilkan dan ranking berdasarkan nilai bulat
            $rataBulat = $countMapelSiswa > 0 ? round($rataRata) : 0;
            $studentData[$siswa->id] = [
                'total' => $totalNilaiSiswa,
                'rata' => $rataBulat,
                'grades' => $nilaiPerMapel
            ];
        }

        // 2. Tentukan Ranking
        $rankedIds = collect($studentData)->filter(fn($s) => $s['rata'] > 0)->sortByDesc('rata')->keys()->flip();

        // 3. SOLUSI GAMBAR: Konversi Logo ke Base64 agar terbaca di cPanel/Hosting
        $pathLogoPemda = public_path('update/logo_smd.png');
        $base64Pemda = file_exists($pathLogoPemda) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoPemda)) : '';

        $pathLogoSekolah = public_path('update/logo.png');
        $base64Sekolah = file_exists($pathLogoSekolah) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoSekolah)) : '';
    @endphp

    <div class="top-right-header">
        Laporan Nilai Kelas &mdash; {{ $tahunAjaran->nama ?? '?' }}
    </div>

    <div class="kop">
        <table width="100%" style="border: none;">
            <tr>
                <!-- PERUBAIKAN: Menggunakan Base64 untuk Logo Pemda -->
                <td width="100" align="center" style="border: none;">
                    <img src="{{ $base64Pemda }}" class="logo" alt="Logo Pemda" width="75" height="75">
                </td>
                <td align="center" style="border: none;">
                    <h3>PEMERINTAH KABUPATEN SUMEDANG</h3>
                    <h3>DINAS PENDIDIKAN</h3>
                    <h2>SD NEGERI PASIRIPIS</h2>
                    <p>
                        Dusun Pasiripis, Desa Karangbungur, Kec. Buahdua - Sumedang, Jawa Barat 45392<br>
                        Email: sdnpasiripis20@gmail.com | Website: www.sdnpasiripis.sch.id
                    </p>
                </td>
                <!-- PERUBAIKAN: Menggunakan Base64 untuk Logo Sekolah -->
                <td width="100" align="center" style="border: none;">
                    <img src="{{ $base64Sekolah }}" class="logo" alt="Logo Sekolah" width="75" height="75">
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h4>REKAPITULASI NILAI AKHIR SEMESTER</h4>
        <p>Kelas | Fase : <strong>{{ $kelas->kelas ?? '?' }} | {{ $kelas->nama ?? '' }}</strong></P>
        <p>Tahun Ajaran: <strong>{{ $tahunAjaran->nama ?? '?' }}</strong> Semester <strong>{{ ucfirst($tahunAjaran->semester ?? '-') }}</strong></p>
    </div>

    <table class="table-nilai">
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2" style="width: 200px;">Nama Peserta Didik</th>
                <th colspan="{{ $hasMapels ? $mapels->count() : 1 }}">Nilai Akhir Mata Pelajaran</th>
                <th rowspan="2" style="width: 45px;">Jml</th>
                <th rowspan="2" style="width: 45px;">Rata²</th>
                <th rowspan="2" style="width: 45px;">Rank</th>
            </tr>
            <tr>
                @if($hasMapels)
                    @foreach ($mapels as $mapel)
                        <th style="font-size: 8pt;">{{ $mapel->singkatan ?? $mapel->nama }}</th>
                    @endforeach
                @else
                    <th>-</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($kelas->siswas as $siswa)
                @php 
                    $data = $studentData[$siswa->id]; 
                    $rank = isset($rankedIds[$siswa->id]) ? $rankedIds[$siswa->id] + 1 : '-';
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left font-weight-bold">{{ $siswa->nama }}</td>

                    @if($hasMapels)
                        @foreach ($mapels as $mapel)
                            <td>{{ $data['grades'][$mapel->id] ?? '-' }}</td>
                        @endforeach
                    @else
                        <td>-</td>
                    @endif

                    <td class="font-weight-bold">{{ $data['total'] > 0 ? number_format($data['total'], 0) : '-' }}</td>
                    <td class="font-weight-bold">{{ $data['rata'] > 0 ? number_format($data['rata'], 0) : '-' }}</td>
                    <td class="font-weight-bold">{{ $rank }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 5 + ($hasMapels ? $mapels->count() : 1) }}">Tidak ada data siswa aktif.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-light font-weight-bold">
                <td colspan="2" class="text-right">Rata-Rata Kelas</td>
                @if($hasMapels)
                    @foreach($mapels as $mapel)
                        <td>
                            @php
                                // Gunakan nilai per-mapel yang sudah dibulatkan untuk rata-rata kelas
                                $avgKelasMapel = collect($studentData)->map(fn($s) => $s['grades'][$mapel->id] ?? null)->filter()->avg();
                            @endphp
                            {{ $avgKelasMapel ? number_format($avgKelasMapel, 1) : '-' }}
                        </td>
                    @endforeach
                @else
                    <td>-</td>
                @endif
                @php
                    $classTotalSum = collect($studentData)->sum('total');
                    // Rata-rata kelas dihitung dari rata (yang sudah dibulatkan per siswa)
                    $classAvg = collect($studentData)->map(fn($s) => $s['rata'])->filter(fn($v) => $v > 0)->avg();
                    $classAvgRounded = $classAvg ? round($classAvg) : null;
                @endphp
                <td class="font-weight-bold">{{ $classTotalSum > 0 ? number_format($classTotalSum, 0) : '0' }}</td>
                <td class="font-weight-bold">{{ $classAvgRounded ? number_format($classAvgRounded, 0) : '-' }}</td>
                <td class="bg-light"></td>
            </tr>
        </tfoot>
    </table>

    {{-- KETERANGAN SINGKATAN MAPEL --}}
    @if($hasMapels)
    <div class="legend-section">
        <span class="legend-title">Keterangan Mata Pelajaran:</span>
        <table class="table-legend">
            <tr>
                @foreach($mapels as $index => $m)
                    <td><strong>{{ $m->singkatan ?? $m->nama }}</strong>: {{ $m->nama }}</td>
                    @if(($index + 1) % 4 == 0 && !$loop->last)
                        </tr><tr>
                    @endif
                @endforeach
            </tr>
        </table>
    </div>
    @endif

    <table class="signature-section">
        <tr>
            <td style="width: 33%;">&nbsp;</td>
            <td style="width: 33%;">&nbsp;</td>
            <td style="width: 34%;">
                Pasiripis, {{ $now->translatedFormat('d F Y') }}<br>
                Wali Kelas {{ $kelas->kelas ?? '' }},
                <div class="signature-space"></div>
                <span class="signature-name">{{ $kelas->pegawai->nama ?? '(____________________)' }}</span><br>
                NIP. {{ $kelas->pegawai->nip ?? '-' }}
            </td>
        </tr>
    </table>

    <div class="footer-info">
        Dicetak melalui SIAKAD SDN PASIRIPIS pada {{ $now->translatedFormat('l, d F Y | H:i') }} WIB
    </div>

</div>
</body>
</html>