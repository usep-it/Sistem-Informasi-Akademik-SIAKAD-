<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai - {{ $siswa->nama }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .page-container {
            width: 100%;
            box-sizing: border-box;
        }

        .kop {
            border-bottom: 4px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .kop table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop td { border: none; vertical-align: middle; }
        .logo { width: 75px; height: auto; }
        .kop-text { text-align: center; line-height: 1.2; }
        .kop-text h4 { margin: 0; font-size: 13pt; font-weight: normal; }
        .kop-text h3 { margin: 0; font-size: 14pt; font-weight: bold; }
        .kop-text h2 { margin: 0; font-size: 17pt; font-weight: bold; letter-spacing: 1px; }
        .kop-text p { margin: 4px 0 0; font-size: 9pt; }

        .report-header { text-align: center; margin-bottom: 20px; }
        .report-title { margin: 0; font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
        .report-number { margin-top: 4px; font-size: 10pt; font-weight: bold; font-family: Arial, sans-serif; }

        .identitas-box { border: 1px solid #000; padding: 10px 15px; margin-bottom: 15px; }
        .identitas-box table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .identitas-box td { padding: 4px 0; vertical-align: top; border: none;}
        .label { width: 140px; font-weight: bold; }
        .separator { width: 15px; text-align: center; }

        .table-nilai { width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 20px; }
        .table-nilai th, .table-nilai td { border: 1px solid #000; padding: 8px; }
        .table-nilai th { background: #e0e0e0; text-align: center; font-weight: bold; text-transform: uppercase; }
        .table-nilai td { vertical-align: middle; }
        .table-nilai tbody tr:nth-child(even) { background: #f9f9f9; }
        .table-nilai tfoot td { font-weight: bold; background: #e0e0e0; }

        .signature-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            text-align: center;
            font-size: 10.5pt;
            page-break-inside: avoid;
        }
        .signature-table td { border: none; vertical-align: top; padding: 0; }
        .signature-space { height: 75px; } 
        .signature-name { font-weight: bold; text-decoration: underline; }
        .nip-text { display: block; margin-top: 2px; }

        .legal-footer { margin-top: 30px; border-top: 1px dashed #aaa; padding-top: 8px; font-size: 8pt; line-height: 1.4; text-align: justify; color: #555; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

@php
    \Carbon\Carbon::setLocale('id');

    /** 1. LOGIKA PENOMORAN SURAT RESMI (400.3.5.1) */
    $noUrut = str_pad($siswa->id, 3, '0', STR_PAD_LEFT);
    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ][date('n')];
    $nomorSuratResmi = "B / $noUrut / 400.3.5.1 / $bulanRomawi / " . date('Y');

    /** 2. DATA WALI KELAS (OTOMATIS) */
    $namaWaliDatabase = $kelasHistoris->pegawai->nama ?? 'Wali Kelas';
    $nipWali = $kelasHistoris->pegawai->nip ?? '-'; 

    /** 3. DATA KEPALA SEKOLAH (OTOMATIS) */
    $kepalaSekolah = \App\Models\Pegawai::where('jabatan', 'Kepala Sekolah')->first();
    $namaKepsek = $kepalaSekolah->nama ?? 'NAMA KEPALA SEKOLAH';
    $nipKepsek = $kepalaSekolah->nip ?? '-';

    /** 4. LOGIKA VERIFIKASI & TANGGAL */
    $isVerified = count($laporan) > 0;
    $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');

    // SOLUSI DOMPDF: Mengubah gambar menjadi Base64 agar 100% terbaca di semua server
    $pathLogoPemda = public_path('update/logo_smd.png');
    $base64Pemda = file_exists($pathLogoPemda) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoPemda)) : '';

    $pathLogoSekolah = public_path('update/logo.png');
    $base64Sekolah = file_exists($pathLogoSekolah) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathLogoSekolah)) : '';
@endphp

<div class="page-container">
    
    <div class="kop">
        <table>
            <tr>
                <td width="15%" align="center"><img src="{{ $base64Pemda }}" class="logo" alt="Logo Pemda"></td>
                <td width="70%">
                    <div class="kop-text">
                        <h4>PEMERINTAH KABUPATEN SUMEDANG</h4>
                        <h3>DINAS PENDIDIKAN</h3>
                        <h2>SD NEGERI PASIRIPIS</h2>
                        <p>
                            Dusun Pasiripis, Desa Karangbungur, Kec. Buahdua - Sumedang 45392<br>
                            Email: sdnpasiripis20@gmail.com | Website: www.sdnpasiripis.sch.id
                        </p>
                    </div>
                </td>
                <td width="15%" align="center"><img src="{{ $base64Sekolah }}" class="logo" alt="Logo Sekolah"></td>
            </tr>
        </table>
    </div>

    <div class="report-header">
        <div class="report-title">LAPORAN HASIL BELAJAR (RAPOR)</div>
        <div class="report-number">Nomor: {{ $nomorSuratResmi }}</div>
    </div>

    <div class="identitas-box">
        <table>
            <tr>
                <td class="label">Nama Peserta Didik</td>
                <td class="separator">:</td>
                <td style="font-weight: bold;">{{ strtoupper($siswa->nama) }}</td>
                <td class="label" style="padding-left: 20px;">Kelas / Fase</td>
                <td class="separator">:</td>
                <td>{{ $kelasHistoris->kelas ?? '-' }} {{ $kelasHistoris->nama ? '/ Fase ' . $kelasHistoris->nama : '' }}</td>
            </tr>
            <tr>
                <td class="label">NIS / NISN</td>
                <td class="separator">:</td>
                <td>{{ $siswa->nis ?? '-' }} / {{ $siswa->nisn }}</td>
                <td class="label" style="padding-left: 20px;">Tahun Pelajaran</td>
                <td class="separator">:</td>
                <td>{{ $tahun->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="separator">:</td>
                <td>{{ in_array(strtoupper($siswa->jk), ['L', 'LAKI-LAKI', 'Laki-Laki']) ? 'Laki-Laki' : 'Perempuan' }}</td>
                <td class="label" style="padding-left: 20px;">Semester</td>
                <td class="separator">:</td>
                <td>{{ ucfirst($tahun->semester ?? '-') }}</td>
            </tr>
        </table>
    </div>

    <table class="table-nilai">
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="62%" style="text-align: left; padding-left:15px;">Mata Pelajaran</th>
                <th width="30%">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNilai = 0; $countMapel = 0; @endphp
            @forelse ($laporan as $mapelName => $grades)
                @php
                    $gradesCollection = collect($grades);
                    $harian = $gradesCollection->filter(fn($g) => in_array(strtolower($g->jenis), ['harian','nh']))->avg('nilai');
                    $pts = $gradesCollection->filter(fn($g) => in_array(strtolower($g->jenis), ['pts','uts']))->avg('nilai');
                    $pas = $gradesCollection->filter(fn($g) => in_array(strtolower($g->jenis), ['pas','uas']))->avg('nilai');
                    $avgUjian = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();
                    
                    $nA = ($harian && $avgUjian) ? ($harian * 0.6) + ($avgUjian * 0.4) : ($harian ?: $avgUjian);
                    $nABulat = !is_null($nA) ? round($nA) : null;
                    if(!is_null($nA)) { $totalNilai += $nA; $countMapel++; }
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td align="left" style="padding-left:15px;">{{ strtoupper($mapelName) }}</td>
                    <td class="text-center font-bold">{{ $nABulat !== null ? $nABulat : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center py-4">Data nilai akademik belum tersedia</td></tr>
            @endforelse
        </tbody>
        @if($countMapel > 0)
        <tfoot>
            <tr>
                <td colspan="2" class="text-right" style="padding-right: 20px;">RATA-RATA NILAI</td>
                <td class="text-center">{{ $countMapel > 0 && round($totalNilai / $countMapel) != 0 ? round($totalNilai / $countMapel) : '' }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <table class="signature-table">
        <tr>
            <td width="33%">
                Mengetahui,<br>Orang Tua / Wali
                <div class="signature-space"></div>
                <span style="display:inline-block; width:200px; border-bottom:1px solid #000;"></span>
            </td>
            <td width="33%"></td>
            <td width="34%">
                Pasiripis, {{ $tanggalCetak }}<br>Wali Kelas {{ $kelasHistoris->kelas ?? '' }}
                <div class="signature-space"></div>
                <span class="signature-name">{{ $namaWaliDatabase }}</span><br>
                <span class="nip-text">NIP. {{ $nipWali }}</span>
            </td>
        </tr>
    </table>

    <table class="signature-table" style="margin-top: 15px;">
        <tr>
            <td>
                Mengetahui,<br>Kepala Sekolah
                <div class="signature-space"></div>
                <span class="signature-name">{{ $namaKepsek }}</span><br>
                <span class="nip-text">NIP. {{ $nipKepsek }}</span>
            </td>
        </tr>
    </table>

    <div class="legal-footer">
        <strong>Catatan Keamanan:</strong> Dokumen cetak ini di-generate secara otomatis dari Pangkalan Data SIAKAD SDN Pasiripis. Sesuai dengan UU ITE Nomor 11 Tahun 2008, dokumen elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah.
    </div>
</div>

</body>
</html>