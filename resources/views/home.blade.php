@extends('layouts.backend')

@push('styles')
<style>
    /* ===== STYLING UNTUK DEV / ADMIN CARDS (NEW) ===== */
    .dev-stat-card {
        border-radius: 20px;
        padding: 25px 25px 30px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 20px rgba(0,0,0,0.06);
        display: block;
        text-decoration: none !important;
        z-index: 1;
    }
    .dev-stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 35px rgba(0,0,0,0.15);
        color: #fff;
    }
    .dev-stat-card.bg-blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .dev-stat-card.bg-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .dev-stat-card.bg-green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .dev-stat-card.bg-orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

    .dev-stat-card .inner { position: relative; z-index: 2; }
    .dev-stat-card .inner h3 { font-size: 38px; font-weight: 800; margin-bottom: 5px; letter-spacing: -1px; }
    .dev-stat-card .inner p { font-size: 15px; font-weight: 600; margin: 0; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }

    .dev-stat-card .icon {
        position: absolute;
        right: 15px;
        bottom: -15px;
        font-size: 90px;
        color: rgba(255, 255, 255, 0.2);
        z-index: 1;
        transition: all 0.4s ease;
    }
    .dev-stat-card:hover .icon {
        transform: scale(1.15) rotate(-10deg);
        color: rgba(255, 255, 255, 0.3);
    }

    /* Info Box Premium untuk Baris ke-2 Dev */
    .premium-info-box {
        background: #fff;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        text-decoration: none !important;
        color: inherit;
    }
    .premium-info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
        border-color: #e2e8f0;
    }
    .premium-info-icon {
        width: 65px;
        height: 65px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-right: 20px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    .premium-info-box:hover .premium-info-icon {
        transform: scale(1.05);
    }
    .premium-info-icon.bg-light-blue { background: #e0f2fe; color: #0ea5e9; }
    .premium-info-icon.bg-light-pink { background: #fce7f3; color: #e11d48; }
    .premium-info-icon.bg-light-purple { background: #f3e8ff; color: #7c3aed; }
    .premium-info-icon.bg-light-red { background: #fee2e2; color: #dc2626; }

    .premium-info-content { flex-grow: 1; }
    .premium-info-content h4 { font-size: 24px; font-weight: 800; color: #1e293b; margin: 0; line-height: 1.2; }
    .premium-info-content p { font-size: 12px; font-weight: 700; color: #64748b; margin: 0 0 5px 0; text-transform: uppercase; letter-spacing: 0.5px; }

    /* ===== STYLING UNTUK SISWA & GURU ===== */
    .profile-widget {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .profile-widget:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1) !important;
    }
    .profile-widget-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px 20px !important;
    }
    .profile-widget-picture {
        border: 5px solid #fff !important;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    .profile-widget-description {
        background: #fff;
        padding: 25px !important;
    }
    .profile-widget-name h4 {
        color: #2c3e50;
        font-size: 22px;
        font-weight: 700;
        line-height: 1.2;
    }
    .profile-widget-name .text-muted {
        color: #8fa3b8 !important;
        font-size: 14px;
    }
    .list-group-item {
        border: none;
        border-bottom: 1px solid #f0f2f5;
        padding: 12px 0 !important;
        background: transparent;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .list-group-item span {
        font-size: 13px;
    }
    .list-group-item .badge {
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .btn-outline-primary {
        border: 2px solid #667eea !important;
        color: #667eea !important;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 10px;
    }
    .btn-outline-primary:hover {
        background: #667eea !important;
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(102, 126, 234, 0.3);
    }

    /* ===== RINGKASAN TUGAS GURU ===== */
    .ringkasan-tugas-card {
        border: none;
        border-radius: 20px;
        background: #fff;
        transition: all 0.3s ease;
    }
    .ringkasan-tugas-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        padding: 20px !important;
        border-radius: 20px 20px 0 0 !important;
    }
    .ringkasan-tugas-card .card-header h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
    }
    .ringkasan-tugas-card .card-body {
        padding: 25px !important;
    }

    /* Panel Layout */
    .panel-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    .panel-left,
    .panel-right {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.06);
        padding: 1.75rem;
    }
    .panel-left {
        flex: 1 1 320px;
        max-width: 420px;
    }
    .panel-right {
        flex: 2 1 520px;
        min-width: 320px;
    }
    .panel-identity-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .panel-identity-avatar {
        width: 100px;
        height: 100px;
        border-radius: 18px;
        object-fit: cover;
        border: 3px solid #667eea;
    }
    .panel-identity-title {
        margin-bottom: 0.25rem;
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
    }
    .panel-identity-subtitle {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    .panel-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .panel-info-list li {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.9rem 0;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .panel-info-list li:last-child {
        border-bottom: none;
    }
    .panel-info-list .label {
        color: #64748b;
        font-size: 0.92rem;
    }
    .panel-info-list .value {
        font-weight: 700;
        color: #0f172a;
    }
    .panel-section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
    }
    .panel-highlight {
        background: #eef2ff;
        border-left: 4px solid #6366f1;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        color: #1e293b;
    }
    .action-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .action-grid a {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.95rem 1rem;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #0f172a;
        text-decoration: none;
        transition: all 0.25s ease;
        font-weight: 600;
    }
    .action-grid a:hover {
        background: #eef2ff;
        border-color: #c7d2fe;
        transform: translateY(-1px);
    }
    .progress-block {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .progress-block .progress {
        height: 10px;
        border-radius: 999px;
        background: #e2e8f0;
    }
    .progress-block .progress-bar {
        background: #4f46e5;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .status-pill.success { background: #dcfce7; color: #166534; }
    .status-pill.warning { background: #fef3c7; color: #92400e; }

    @media (max-width: 991px) {
        .panel-grid { flex-direction: column; }
        .panel-left,
        .panel-right { max-width: none; }
        .action-grid { grid-template-columns: 1fr; }
    }

</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <h1>SISTEM INFORMASI AKADEMIK - SD NEGERI PASIRIPIS (20233962)</h1>
                {{-- Menampilkan Tahun Ajaran Aktif di Header --}}
                @php
                    $tahunAktif = \App\Models\Tahun::where('status', 'Aktif')->first();
                @endphp
                @if ($tahunAktif)
                    <span class="badge badge-success">
                         Aktif: {{ $tahunAktif->nama }} - {{ ucfirst($tahunAktif->semester) }}
                    </span>
                @else
                     <span class="badge badge-warning">Tahun Ajaran Belum Aktif</span>
                @endif
            </div>

            {{-- Notifikasi --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm" role="alert">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
             {{-- PERBAIKAN: Gunakan alert dismissible --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <p><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- ================= DEV / ADMIN ================= --}}
            @if (auth()->user()->role == 'Dev')
                {{-- Baris Pertama: Kartu Statistik Utama --}}
                <div class="row">
                    {{-- Total Pengguna --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('user/guru') }}" class="dev-stat-card bg-blue h-100">
                            <div class="inner">
                                <h3>{{ $totalUsers ?? 0 }}</h3>
                                <p>Total Pengguna</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                        </a>
                    </div>
                    
                    {{-- Total GTK --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('pegawai') }}" class="dev-stat-card bg-purple h-100">
                            <div class="inner">
                                <h3>{{ $pegawai ?? 0 }}</h3>
                                <p>Data GTK</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </a>
                    </div>
                    
                    {{-- Total Siswa Aktif --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('siswa') }}" class="dev-stat-card bg-green h-100">
                            <div class="inner">
                                <h3>{{ $siswa ?? 0 }}</h3>
                                <p>Siswa Aktif</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </a>
                    </div>
                    
                    {{-- Total Rombel Aktif --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('kelas') }}" class="dev-stat-card bg-orange h-100">
                            <div class="inner">
                                <h3>{{ $totalKelasAktif ?? 0 }}</h3>
                                <p>Rombel Aktif</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-school"></i>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Baris Kedua: Detail Tambahan --}}
                <div class="row">
                    {{-- Mata Pelajaran --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                        <a href="{{ url('mapel') }}" class="premium-info-box h-100">
                            <div class="premium-info-icon bg-light-purple">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="premium-info-content">
                                <p>Mata Pelajaran</p>
                                <h4>{{ $totalMapel ?? 0 }}</h4>
                            </div>
                        </a>
                    </div>
                    
                    {{-- Siswa Laki-laki --}}
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                         <a href="{{ url('siswa') }}" class="premium-info-box h-100">
                             <div class="premium-info-icon bg-light-blue">
                                 <i class="fas fa-male"></i>
                             </div>
                             <div class="premium-info-content">
                                 <p>Siswa Laki-laki Aktif</p>
                                 <h4>{{ $siswaLaki ?? 0 }}</h4>
                             </div>
                         </a>
                    </div>
                    
                    {{-- Siswa Perempuan --}}
                     <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                         <a href="{{ url('siswa') }}" class="premium-info-box h-100">
                             <div class="premium-info-icon bg-light-pink">
                                 <i class="fas fa-female"></i>
                             </div>
                             <div class="premium-info-content">
                                 <p>Siswa Perempuan Aktif</p>
                                 <h4>{{ $siswaPerempuan ?? 0 }}</h4>
                             </div>
                         </a>
                    </div>
                    
                    {{-- Jadwal Aktif --}}
                     <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-4">
                         <a href="{{ url('jadwal') }}" class="premium-info-box h-100">
                             <div class="premium-info-icon bg-light-red">
                                 <i class="fas fa-calendar-check"></i>
                             </div>
                             <div class="premium-info-content">
                                 <p>Jadwal Aktif</p>
                                 <h4>{{ $jadwal ?? 0 }}</h4>
                             </div>
                         </a>
                    </div>
                </div>

                {{-- Baris Ketiga: Grafik Distribusi Siswa --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm" style="border-radius: 20px; border: 1px solid #f1f5f9;">
                            <div class="card-header bg-white" style="border-radius: 20px 20px 0 0; padding: 25px 25px 15px;">
                                <h4 class="text-dark font-weight-bold" style="font-size: 1.25rem;"><i class="fas fa-chart-bar text-primary mr-2"></i>Distribusi Siswa Aktif per Rombel</h4>
                            </div>
                            <div class="card-body p-4">
                                {{-- Cek apakah data grafik ada --}}
                                @if(!empty($distribusiSiswaLabels) && count(json_decode($distribusiSiswaLabels)) > 0)
                                    <div style="height: 350px;">
                                        <canvas id="distribusiSiswaChart"></canvas>
                                    </div>
                                @else
                                    <div class="alert alert-light text-center border" style="border-radius: 12px; padding: 40px;">
                                        <i class="fas fa-chart-pie fa-3x text-muted mb-3 opacity-50"></i>
                                        <h5 class="text-muted font-weight-bold">Data Tidak Tersedia</h5>
                                        <p class="mb-0">Belum ada data distribusi siswa aktif di tahun ajaran ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @endif

            {{-- ================= SISWA ================= --}}
            @if (auth()->user()->role == 'Siswa')
                @php $siswa = Auth::user()->siswa; @endphp
                <div class="section-body">
                    <div class="mb-5">
                        <h2 class="section-title" style="margin-bottom: 5px;">Selamat datang, {{ $siswa?->nama ?? Auth::user()->name }}! 👋</h2>
                        @if ($siswa?->status !== 'Aktif')
                            <p class="section-lead text-muted">Portal alumni / siswa keluar. Akses terbatas untuk melihat nilai dan transkrip.</p>
                        @else
                            <p class="section-lead text-muted">Sistem Informasi Akademik SDN Pasiripis - Portal Siswa Aktif</p>
                        @endif
                    </div>

                    <div class="panel-grid">
                        <div class="panel-left">
                            <div class="panel-identity-header">
                                <img id="fotoProfilSiswa" src="{{ Auth::user()->foto ? asset('foto_user/' . Auth::user()->foto) : ($siswa?->foto ? asset('foto_siswa/' . $siswa->foto) : 'https://placehold.co/140x140/EFEFEF/AAAAAA?text=Foto') }}" alt="Foto {{ $siswa?->nama ?? 'Siswa' }}" class="panel-identity-avatar">
                                <div>
                                    <h3 class="panel-identity-title">{{ $siswa?->nama ?? Auth::user()->name }}</h3>
                                    <div class="panel-identity-subtitle">{{ $siswa?->kelas?->kelas ?? '?' }} {{ $siswa?->kelas?->nama ? '- Fase '.$siswa->kelas->nama : '' }}</div>
                                </div>
                            </div>
                            <ul class="panel-info-list">
                                <li><span class="label">NISN</span><span class="value">{{ $siswa?->nisn ?? '-' }}</span></li>
                                <li><span class="label">NIS</span><span class="value">{{ $siswa?->nis ?? '-' }}</span></li>
                                <li><span class="label">Username</span><span class="value">{{ Auth::user()->username }}</span></li>
                                @if ($tahunAktif)
                                    <li><span class="label">Tahun Ajaran</span><span class="value">{{ $tahunAktif->nama }}</span></li>
                                @endif
                            </ul>
                            <div class="panel-highlight">
                                <strong>Status:</strong> {{ $siswa?->status ?? 'Tidak diketahui' }}
                            </div>
                            <div class="action-grid">
                                <a href="{{ route('nilai.saya') }}"><i class="fas fa-chart-line"></i> Lihat Nilai</a>
                                @if ($tahunAktif)
                                    <a href="{{ route('laporan.pdf', $tahunAktif->id) }}"><i class="fas fa-print"></i> Cetak Laporan</a>
                                @else
                                    <a href="#" class="disabled"><i class="fas fa-print"></i> Cetak Laporan</a>
                                @endif
                            </div>
                        </div>

                        <div class="panel-right">
                            <div class="panel-section-title">
                                <span>Informasi Akun</span>
                                <span class="status-pill {{ $siswa?->status !== 'Aktif' ? 'warning' : 'success' }}">
                                    {{ $siswa?->status === 'Aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="panel-info-list">
                                <li><span class="label">Nama Lengkap</span><span class="value">{{ $siswa?->nama ?? '-' }}</span></li>
                                <li><span class="label">Jenis Kelamin</span><span class="value">{{ $siswa?->jk == 'L' ? 'Laki-laki' : ($siswa?->jk == 'P' ? 'Perempuan' : '-') }}</span></li>
                                <li><span class="label">Kelas</span><span class="value">{{ $siswa?->kelas?->kelas ?? '-' }}</span></li>
                                <li><span class="label">Sekolah</span><span class="value">SD Negeri Pasiripis</span></li>
                            </div>

                            @if ($siswa?->status !== 'Aktif')
                                <div class="panel-highlight" style="background: #fef3c7; border-left-color: #f59e0b;">
                                    <strong>Perhatian:</strong> Akun Anda dalam mode alumni. Hanya melihat nilai dan cetak laporan.
                                </div>
                            @else
                                <div class="panel-highlight">
                                    <strong>Selamat!</strong> Anda dapat mengakses nilai dan mencetak laporan akademik.
                                </div>
                            @endif

                            <a href="{{ route('profil.edit') }}" class="btn btn-outline-primary btn-block" style="border-radius: 12px; padding: 12px;">
                                <i class="fas fa-user-edit mr-1"></i> Ubah Profil / Password
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================= GURU ================= --}}
            @if (auth()->user()->role == 'Guru')
                @php 
                    $pegawai = Auth::user()->pegawai;
                    $isGuru = $isGuru ?? false;
                    $userType = $userType ?? strtolower($pegawai?->jabatan ?? 'guru');
                    $jabatanLabel = $pegawai?->jabatan ? ucfirst($pegawai->jabatan) : 'Guru';
                @endphp
                <div class="section-body">
                    <div class="mb-5">
                        <h2 class="section-title" style="margin-bottom: 5px;">Selamat datang, {{ $pegawai?->nama ?? Auth::user()->name }}! 👋</h2>
                        @if($isGuru)
                            <p class="section-lead text-muted">Kelola pembelajaran dan kegiatan kelas secara profesional.</p>
                        @else
                            <p class="section-lead text-muted">Akses manajemen administrasi sekolah dengan tampilan bersih dan mudah.</p>
                        @endif
                    </div>

                    <div class="panel-grid">
                        <div class="panel-left">
                            <div class="panel-identity-header">
                                <img id="fotoProfilPegawai" src="{{ Auth::user()->foto ? asset('foto_user/' . Auth::user()->foto) : ($pegawai?->foto ? asset('foto_pegawai/' . $pegawai->foto) : 'https://placehold.co/140x140/EFEFEF/AAAAAA?text=Guru') }}" alt="Foto {{ $pegawai?->nama ?? 'Guru' }}" class="panel-identity-avatar">
                                <div>
                                    <h3 class="panel-identity-title">{{ $pegawai?->nama ?? Auth::user()->name }}</h3>
                                    <div class="panel-identity-subtitle">{{ $jabatanLabel }}</div>
                                </div>
                            </div>
                            <ul class="panel-info-list">
                                <li><span class="label">NIP / NIK</span><span class="value">{{ $pegawai?->nip ?? $pegawai?->nik ?? '-' }}</span></li>
                                <li><span class="label">Username</span><span class="value">{{ Auth::user()->username }}</span></li>
                                @if ($tahunAktif)
                                    <li><span class="label">Tahun Ajaran</span><span class="value">{{ $tahunAktif->nama }}</span></li>
                                @endif
                            </ul>
                            <div class="panel-highlight">
                                <strong>Peran:</strong> {{ $jabatanLabel }}
                            </div>
                            <a href="{{ route('profil.edit') }}" class="btn btn-outline-primary btn-block" style="border-radius: 12px; padding: 12px;">
                                <i class="fas fa-user-edit mr-1"></i> Ubah Profil
                            </a>
                        </div>

                        <div class="panel-right">
                            <div class="panel-section-title">
                                <span>Ringkasan Tugas</span>
                                <span class="status-pill {{ $isGuru ? 'success' : 'warning' }}">
                                    {{ $isGuru ? 'Guru Aktif' : ucfirst($userType) }}
                                </span>
                            </div>

                            @if($isGuru)
                                @if($kelasWali)
                                    <div class="progress-block">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <small class="text-muted">Wali Kelas</small>
                                                <div class="font-weight-bold">{{ $kelasWali->kelas }} {{ $kelasWali->nama }}</div>
                                            </div>
                                            <span class="status-pill {{ ($progressWali ?? 0) === 100 ? 'success' : 'warning' }}">{{ $progressWali ?? 0 }}%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ min(max($progressWali ?? 0, 0), 100) }}%" aria-valuenow="{{ min(max($progressWali ?? 0, 0), 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="mt-3 text-muted mb-0">{{ $inputSiswaWali ?? 0 }} dari {{ $totalSiswaWali ?? 0 }} siswa telah diinput nilai.</p>
                                    </div>
                                @endif

                                <div class="progress-block">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <small class="text-muted">Guru Mapel</small>
                                            <div class="font-weight-bold">{{ $jadwalMengajarCount ?? 0 }} jadwal aktif</div>
                                        </div>
                                        <span class="status-pill {{ ($progressGuruMapel ?? 0) === 100 ? 'success' : 'warning' }}">{{ $progressGuruMapel ?? 0 }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(max($progressGuruMapel ?? 0, 0), 100) }}%" aria-valuenow="{{ min(max($progressGuruMapel ?? 0, 0), 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <p class="mt-3 text-muted mb-0">{{ $inputSiswaDiampu ?? 0 }} dari {{ $totalSiswaDiampu ?? 0 }} siswa input nilai.</p>
                                </div>

                                <div class="action-grid">
                                    <a href="{{ url('jadwal') }}"><i class="fas fa-calendar-check"></i> Jadwal</a>
                                    <a href="{{ url('nilai') }}"><i class="fas fa-edit"></i> Input Nilai</a>
                                </div>
                            @else
                                <div class="panel-highlight" style="background:#fef3c7; border-left-color:#f59e0b;">
                                    <strong>Info:</strong> Tampilan ini disesuaikan untuk peran administrasi dan manajemen sekolah.
                                </div>
                                <p class="text-muted">Akses menu backend sesuai peran Anda tersedia di sidebar. Data siswa dan GTK hanya dapat dilihat jika diberi izin khusus.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
</main>

@push('scripts')
<script>
    window.addEventListener('fotoProfilUpdated', function(e) {
        const newFotoUrl = e.detail.url;
        const navFoto = document.getElementById('navbarFotoUser');
        if (navFoto && newFotoUrl) {
            navFoto.src = newFotoUrl + '?t=' + new Date().getTime();
        }
         const profileFotoSiswa = document.getElementById('fotoProfilSiswa');
         if (profileFotoSiswa && newFotoUrl) profileFotoSiswa.src = newFotoUrl + '?t=' + new Date().getTime();
         const profileFotoPegawai = document.getElementById('fotoProfilPegawai');
         if (profileFotoPegawai && newFotoUrl) profileFotoPegawai.src = newFotoUrl + '?t=' + new Date().getTime();
    });
     // Script autohide
     $(document).ready(function() {
        $('.alert').delay(5000).fadeOut(300);
     });
</script>
@endpush

@push('scripts')
@if (auth()->user()->role == 'Dev' && isset($distribusiSiswaLabels) && count(json_decode($distribusiSiswaLabels)) > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const ctx = document.getElementById('distribusiSiswaChart').getContext('2d');
            const labels = {!! $distribusiSiswaLabels !!};
            const dataCounts = {!! $distribusiSiswaData !!};
            
            // Warna yang lebih premium untuk grafik
            const baseBackgroundColors = [
                'rgba(79, 70, 229, 0.7)',   // Indigo
                'rgba(16, 185, 129, 0.7)',  // Emerald
                'rgba(245, 158, 11, 0.7)',  // Amber
                'rgba(236, 72, 153, 0.7)',  // Pink
                'rgba(14, 165, 233, 0.7)',  // Light Blue
                'rgba(139, 92, 246, 0.7)'   // Violet
            ];
            const baseBorderColors = [
                'rgba(79, 70, 229, 1)', 
                'rgba(16, 185, 129, 1)', 
                'rgba(245, 158, 11, 1)', 
                'rgba(236, 72, 153, 1)', 
                'rgba(14, 165, 233, 1)', 
                'rgba(139, 92, 246, 1)'
            ];

            let backgroundColors = [];
            let borderColors = [];
            for (let i = 0; i < labels.length; i++) {
                backgroundColors.push(baseBackgroundColors[i % baseBackgroundColors.length]);
                borderColors.push(baseBorderColors[i % baseBorderColors.length]);
            }

            const distribusiSiswaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Siswa Aktif',
                        data: dataCounts,
                        backgroundColor: backgroundColors, 
                        borderColor: borderColors,       
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, 
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.8)',
                                drawBorder: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                precision: 0,
                                stepSize: 1,
                                color: '#64748b',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    weight: '500'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#475569',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    weight: '600'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false 
                        },
                        tooltip: { 
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { family: "'Poppins', sans-serif", size: 14, weight: '700' },
                            bodyFont: { family: "'Poppins', sans-serif", size: 13 },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y + ' Siswa';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif
@endpush
@endsection