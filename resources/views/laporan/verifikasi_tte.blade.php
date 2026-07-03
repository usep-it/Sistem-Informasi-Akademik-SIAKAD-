<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - SIAKAD SDN Pasiripis</title>

    <link rel="icon" href="{{ asset('update/img/logo.png') }}">

    {{-- Bootstrap 4 & Font Awesome --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --siakad-primary: #8252fa;
            --siakad-secondary: #eca2f1;
            --siakad-dark: #0f172a;
            --siakad-success: #27ae60;
            --siakad-danger: #dc3545;
            --siakad-bg: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--siakad-bg);
            color: #334155;
            margin: 0;
            padding: 0;
        }

        /* ================= HEADER ================= */
        .header_area {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 12px 0;
        }
        .navbar-brand { display: flex; align-items: center; text-decoration: none !important; }
        .navbar-brand img { height: 48px; }

        /* ================= BANNER ================= */
        .banner_area {
            min-height: 280px;
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.9)),
                        url('{{ asset("update/img/sd.jpg") }}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: white;
        }
        .banner_content h2 { font-size: 38px; font-weight: 800; margin-bottom: 5px; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .page_link { font-size: 14px; opacity: 0.85; font-weight: 500; }
        .page_link a { color: white; text-decoration: none; }

        /* ================= STATUS BOX ================= */
        .status-box {
            margin-top: -45px;
            background: white;
            border-radius: 15px;
            padding: 20px 28px;
            display: flex;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            border: 1px solid #e2e8f0;
            position: relative;
            z-index: 10;
        }
        .status-box i {
            width: 45px;
            height: 45px;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .status-text { font-weight: 700; color: #1e293b; font-size: 1.1rem; }

        /* ================= CONTENT CARD ================= */
        .info-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            padding-top: 90px;
            box-shadow: 0 15px 45px rgba(0,0,0,.03);
            border: 1px solid #eef2f7;
            position: relative;
        }

        .head-surat {
            position: absolute;
            top: 25px;
            left: 25px;
            background: linear-gradient(135deg, var(--siakad-primary) 0%, var(--siakad-secondary) 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 6px 6px 40px 6px;
            box-shadow: 0 5px 15px rgba(130, 82, 250, .25);
        }
        .head-surat h3 { margin: 0; font-size: 17px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; }

        /* Penyelarasan Metadata */
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 9px 0; vertical-align: top; font-size: 15px; }
        .meta-label { width: 140px; color: var(--siakad-primary); font-weight: 800; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        .meta-sep { width: 20px; text-align: center; font-weight: 700; color: #cbd5e1; }
        .meta-value { font-weight: 700; color: #1e293b; line-height: 1.4; }

        /* ================= PREVIEW AREA ================= */
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .preview-title { font-weight: 800; font-size: 16px; color: #475569; text-transform: uppercase; letter-spacing: 1px; }
        
        .btn-unduh {
            background: var(--siakad-primary);
            color: white !important;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(130, 82, 250, 0.2);
        }
        .btn-unduh:hover { background: #6d3df0; transform: translateY(-2px); }

        .iframe-container {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            background: #cbd5e1;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
            height: 950px;
            position: relative;
        }
        .frame-surat { width: 100%; height: 100%; border: none; background: white; }

        /* ================= FOOTER ================= */
        .footer_area {
            background: var(--siakad-dark);
            color: #94a3b8;
            padding: 60px 0;
            margin-top: 80px;
        }
        .footer_area h3 { color: white; font-weight: 800; margin-bottom: 12px; font-size: 22px; }
        .footer-line { border-top: 1px solid rgba(255,255,255,.05); margin-top: 35px; padding-top: 25px; font-size: 13px; }

        @media (max-width: 991px) {
            .info-section { padding: 25px; padding-top: 85px; }
            .col-lg-5 { border-right: none !important; border-bottom: 1px solid #f1f5f9; margin-bottom: 30px; padding-bottom: 30px; }
            .iframe-container { height: 650px; }
            .btn-unduh { width: 100%; justify-content: center; }
            .preview-header { flex-direction: column; align-items: flex-start !important; gap: 15px; }
            .banner_content h2 { font-size: 28px; }
        }
    </style>
</head>

<body>

@php
    // 1. Logika Perhitungan Kelengkapan Nilai
    $isDataAda = $nilai->count() > 0;

    // 2. Logika Penomoran Surat Resmi (400.3.5.1)
    $noUrut = str_pad($siswa->id, 3, '0', STR_PAD_LEFT);
    $bulanRomawi = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ][date('n')];
    $nomorSuratResmi = "B / $noUrut / 400.3.5.1 / $bulanRomawi / " . date('Y');

    // 3. Nama Wali Kelas (Diambil otomatis dari database)
    $namaWali = $siswa->kelas->pegawai->nama ?? 'Wali Kelas';

    // 4. URL PDF Stream (Point to the correct binary stream method)
    $pdfUrl = url('/laporan/pdf/' . $siswa->id . '/' . $tahunAktif->id);
@endphp

{{-- Navbar --}}
<header class="header_area">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('update/img/logo.png') }}" alt="Logo">
            <span class="ml-3 font-weight-bold text-dark d-none d-sm-inline" style="font-size: 18px;">
                SIAKAD SDN PASIRIPIS
            </span>
        </a>
    </div>
</header>

{{-- Banner --}}
<section class="banner_area">
    <div class="container text-center text-md-left">
        <div class="banner_content">
            <div class="page_link mb-2">
                <a href="/">Beranda</a> / <span>Validasi Dokumen Digital</span>
            </div>
            <h2>Hasil Verifikasi Dokumen</h2>
        </div>
    </div>
</section>

{{-- Main Content --}}
<section class="pb-5">
    <div class="container">
        
        {{-- Status Box (Dinamis Berdasarkan Data) --}}
        <div class="status-box mb-5">
            <i class="fa {{ $isDataAda ? 'fa-check' : 'fa-triangle-exclamation' }}" 
               style="background: {{ $isDataAda ? 'var(--siakad-success)' : 'var(--siakad-danger)' }};"></i>
            <div class="status-text">
                {{ $isDataAda ? 'Dokumen terdaftar dan dinyatakan VALID pada sistem' : 'Data ditemukan, namun dokumen TIDAK TERVERIFIKASI' }}
            </div>
        </div>

        <div class="position-relative">
            
            {{-- Section Title Badge --}}
            <div class="head-surat">
                <i class="fa fa-file-shield mr-2"></i>
                <h3>Informasi Dokumen</h3>
            </div>

            <div class="info-section shadow-sm">
                <div class="row">
                    
                    {{-- SISI KIRI: METADATA --}}
                    <div class="col-lg-5 pr-lg-5 border-right">
                        <table class="meta-table">
                            <tr>
                                <td class="meta-label">Nomor Surat</td>
                                <td class="meta-sep">:</td>
                                <td class="meta-value text-break">{{ $nomorSuratResmi }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Jenis Dokumen</td>
                                <td class="meta-sep">:</td>
                                <td class="meta-value text-primary">TRANSKRIP NILAI PESERTA DIDIK</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Nama Siswa</td>
                                <td class="meta-sep">:</td>
                                <td class="meta-value">{{ strtoupper($siswa->nama) }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">NIS / NISN</td>
                                <td class="meta-sep">:</td>
                                <td class="meta-value">{{ $siswa->nis ?? '-' }} / {{ $siswa->nisn }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Tahun Pelajaran</td>
                                <td class="meta-sep">:</td>
                                <td class="meta-value">{{ $tahunAktif->nama }} ({{ ucfirst($tahunAktif->semester) }})</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Wali Kelas</td>
                                <td class="meta-sep">:</td>
                                <td class="meta-value">
                                    {{ $namaWali }}<br>
                                    <small class="text-muted font-weight-normal">NIP. {{ $siswa->kelas->pegawai->nip ?? '-' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="meta-label">Status Validasi</td>
                                <td class="meta-sep">:</td>
                                <td class="meta-value {{ $isDataAda ? 'text-success' : 'text-danger' }}">
                                    @if($isDataAda)
                                        <i class="fas fa-check-circle mr-1"></i> TERVERIFIKASI
                                    @else
                                        <i class="fas fa-times-circle mr-1"></i> TIDAK TERVERIFIKASI
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <div class="alert {{ $isDataAda ? 'alert-info' : 'alert-danger' }} mt-4 border-0 shadow-sm" style="border-radius: 12px; font-size: 13px; line-height: 1.6;">
                            @if($isDataAda)
                                <i class="fas fa-info-circle mr-2 text-primary"></i> 
                                Dokumen ini divalidasi melalui sistem pangkalan data akademik SDN Pasiripis dan dinyatakan sah secara hukum sesuai UU ITE No. 11 Thn 2008.
                            @else
                                <i class="fas fa-exclamation-triangle mr-2 text-danger"></i> 
                                <strong>PENTING:</strong> Dokumen ini dinyatakan tidak valid/tidak terverifikasi dikarenakan data nilai akademik pada periode ini belum diinput atau belum lengkap.
                            @endif
                        </div>
                    </div>

                    {{-- SISI KANAN: PRATINJAU DOKUMEN --}}
                    <div class="col-lg-7 pl-lg-4 mt-4 mt-lg-0">
                        <div class="preview-header no-print">
                            <div class="preview-title">
                                <i class="fas fa-file-pdf text-danger mr-2"></i> Pratinjau Dokumen Resmi
                            </div>
                            <a href="{{ $pdfUrl }}" target="_blank" class="btn-unduh">
                                <i class="fas fa-external-link-alt"></i> Buka / Unduh PDF
                            </a>
                        </div>

                        <div class="iframe-container shadow-sm">
                            {{-- Menggunakan URL PDF asli agar tidak looping HTML --}}
                            <iframe 
                                src="{{ $pdfUrl }}#toolbar=1&navpanes=0&view=FitH" 
                                class="frame-surat">
                                <div class="p-5 text-center">
                                    <p>Pratinjau tidak dapat ditampilkan langsung di browser Anda.</p>
                                    <a href="{{ $pdfUrl }}" class="btn btn-primary">Klik untuk Membuka Dokumen</a>
                                </div>
                            </iframe>
                        </div>

                        <div class="mt-4 small text-muted text-center" style="line-height: 1.6;">
                            <i class="fas fa-clock mr-1"></i> 
                            Verifikasi real-time dilakukan pada: {{ now('Asia/Jakarta')->translatedFormat('d F Y | H:i') }} WIB
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="footer_area">
    <div class="container text-center">
        <img src="{{ asset('update/img/logo.png') }}" width="65" class="mb-4">
        <h3>SD NEGERI PASIRIPIS</h3>
        <p class="mb-1">Kecamatan Buahdua, Kabupaten Sumedang - Jawa Barat</p>
        <p class="small">Sistem Informasi Akademik & Verifikasi Dokumen Digital</p>
        
        <div class="footer-line">
            &copy; {{ date('Y') }} SIAKAD SDN PASIRIPIS. All Rights Reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>