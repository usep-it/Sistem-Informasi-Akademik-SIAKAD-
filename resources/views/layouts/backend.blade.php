<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>SIAKAD &mdash; SDN PASIRIPIS</title>

    <link href="{{ asset('frontend/assets/img/logo.png') }}" rel="icon">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ url('update/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('update/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ url('update/modules/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ url('update/modules/weather-icon/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ url('update/modules/weather-icon/css/weather-icons-wind.min.css') }}">
    <link rel="stylesheet" href="{{ url('update/modules/summernote/summernote-bs4.css') }}">

    <!-- Datatable -->
    <link rel="stylesheet" href="{{ asset('update/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('update/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('update/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ url('update/css/style.css') }}">
    <link rel="stylesheet" href="{{ url('update/css/components.css') }}">

    <style>
        :root {
            --siakad-primary: #8252fa;
            --siakad-secondary: #eca2f1;
            --siakad-dark: #1e293b;
            --siakad-gradient: linear-gradient(135deg, #8252fa 0%, #eca2f1 100%);
        }

        .main-content { overflow: visible !important; }

        /* Sidebar User Profile */
        .sidebar-user {
            border-bottom: 1px solid rgba(61, 77, 252, 0.1);
            background-color: rgba(17, 41, 224, 0.1);
            color: #020202;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .sidebar-user img { 
            border: 2px solid rgba(0, 255, 60, 0.5); 
            flex-shrink: 0; /* Mencegah gambar tergencet saat sidebar mini */
        }
        .sidebar-user .user-info {
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.3s ease;
        }
        .sidebar-user .user-info h6 {
            color: #2508fb; 
            font-weight: 600; 
            margin-bottom: 2px;
            overflow: hidden; 
            text-overflow: ellipsis; 
            white-space: nowrap;
        }

        /* Clock Navbar */
        #realtime-clock-container {
            flex-grow: 1;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 500;
            margin: 0 15px;
            line-height: 1.3;
        }
        #clock-wrapper { display: flex; align-items: center; font-size: 0.9rem; }
        #realtime-clock { padding: 0 5px; letter-spacing: 0.5px; white-space: nowrap; font-weight: 600; }
        #greeting { white-space: nowrap; font-size: 0.8rem; margin-right: 5px; }

        /* =========================
           RESPONSIVE MOBILE FIX
        ========================= */
        @media (max-width: 991.98px) {
            .main-navbar {
                display: flex !important;
                padding: 0 10px !important;
                justify-content: space-between !important;
            }

            .navbar-nav.navbar-right {
                flex-direction: row !important;
                width: auto !important;
                margin-left: 0 !important;
            }

            /* Sembunyikan Nama & Sapaan di HP untuk menghemat ruang */
            #greeting, .d-none.d-lg-inline-block, #navbar-marquee-container {
                display: none !important;
            }

            /* Jam Ringkas di HP */
            #realtime-clock-container {
                margin: 0 5px !important;
                flex-grow: 0;
            }
            #realtime-clock {
                font-size: 0.75rem !important;
            }

            /* Dropdown User Position Fix */
            .navbar .nav-link.nav-link-user {
                padding: 0 5px !important;
            }

            .navbar .dropdown-menu {
                position: absolute !important;
                top: 60px !important;
                right: 5px !important;
                left: auto !important;
                width: 200px !important;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
                border-radius: 12px !important;
                display: none;
            }

            .navbar .dropdown-menu.show {
                display: block !important;
            }
        }

        /* =========================
           FOOTER PREMIUM FIX
        ========================= */
        .main-footer {
            background: var(--siakad-gradient);
            color: #ffffff !important;
            /* FIX: Kembalikan padding kiri (280px) agar tidak terhalang sidebar Stisla */
            padding: 25px 40px 25px 280px; 
            border: none;
            box-shadow: 0 -10px 30px rgba(130, 82, 250, 0.15);
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 30px 30px 0 0;
        }

        .main-footer a {
            color: #ffffff;
            font-weight: 800;
            text-decoration: underline;
            transition: all 0.3s ease;
        }
        .main-footer a:hover {
            color: #ffeb3b; /* Kuning emas saat di-hover */
        }
        .main-footer .bullet {
            background-color: rgba(255, 255, 255, 0.5); /* Sesuaikan warna bullet divider */
        }

        /* Responsive Footer untuk layar kecil (Sidebar tertutup) */
        @media (max-width: 1024px) {
            .main-footer {
                padding: 20px 25px; /* Hilangkan padding 280px di HP */
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            .main-footer .footer-right {
                margin-top: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="navbar-bg"></div>

    <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
            <ul class="navbar-nav mr-3">
                <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            </ul>
        </form>

        <ul class="navbar-nav navbar-right align-items-center">
            {{-- Widget Jam --}}
            <li id="realtime-clock-container" class="nav-item">
                <div id="clock-wrapper">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span id="greeting"></span>
                    <span id="realtime-clock">Memuat...</span>
                </div>
                <div id="navbar-marquee-container">
                    <marquee behavior="scroll" direction="left" scrollamount="3" id="navbar-marquee">
                        <span id="marquee-text">Selamat datang di SIAKAD SDN Pasiripis...</span>
                    </marquee>
                </div>
            </li>

            {{-- Dropdown User --}}
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user d-flex align-items-center">
                    @php
                        $fotoProfil = asset('update/img/avatar-1.png');
                        if (Auth::user()->role == 'Guru') {
                             if (Auth::user()->pegawai?->foto) {
                                 $fotoProfil = asset('foto_pegawai/' . Auth::user()->pegawai->foto);
                             } elseif (Auth::user()->foto) {
                                 $fotoProfil = asset('foto_user/' . Auth::user()->foto);
                             }
                        } elseif (Auth::user()->role == 'Siswa') {
                             if (Auth::user()->siswa?->foto) {
                                 $fotoProfil = asset('foto_siswa/' . Auth::user()->siswa->foto);
                             } elseif (Auth::user()->foto) {
                                 $fotoProfil = asset('foto_user/' . Auth::user()->foto);
                             }
                        } elseif (Auth::user()->foto) {
                             $fotoProfil = asset('foto_user/' . Auth::user()->foto);
                        }
                    @endphp
                    <img id="navbarFotoUser" src="{{ $fotoProfil }}" class="rounded-circle mr-2" width="40" height="40" style="object-fit: cover;">
                    <div class="d-none d-lg-inline-block">
                        @if (auth()->user()->role == 'Guru')
                            {{ Str::limit(Auth::user()->pegawai?->nama ?? Auth::user()->name, 15) }}
                        @elseif (auth()->user()->role == 'Siswa')
                             {{ Str::limit(Auth::user()->siswa?->nama ?? Auth::user()->name, 15) }}
                        @else
                             {{ Str::limit(Auth::user()->name, 15) }}
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a href="{{ route('profil.edit') }}" class="dropdown-item has-icon">
                        <i class="fas fa-user-cog"></i> Profil Saya
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </li>
        </ul>
    </nav>

    <div id="app">
        <div class="main-wrapper main-wrapper-1">

            {{-- ======================== SIDEBAR ======================== --}}
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">

                    <div class="sidebar-user d-flex align-items-center p-3 mb-3" style="border-bottom: 1px solid #e4e6ef;">
                        <img id="sidebarFotoUser" src="{{ $fotoProfil }}" alt="Foto Profil" class="rounded-circle mr-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                        @php
                            $user = Auth::user();
                            $pegawai = $user->pegawai;
                            $siswa   = $user->siswa;

                            if ($pegawai) {
                                $namaUser = $pegawai->nama;
                                $jabatan = $pegawai->jabatan; 
                            } elseif ($siswa) {
                                $namaUser = $siswa->nama;
                                $jabatan = "Siswa";
                            } else {
                                $namaUser = $user->name;
                                $jabatan = $user->role ?? "Pengguna";
                            }

                            // WARNA BADGE
                            $badgeColor = [
                                'Kepala Sekolah' => 'badge-warning',
                                'Guru Kelas'     => 'badge-success',
                                'Guru Mapel'     => 'badge-success',
                                'Tenaga Administrasi' => 'badge-secondary',
                                'Operator Sekolah' => 'badge-secondary',
                                'Penjaga Sekolah' => 'badge-secondary',
                                'Pustakawan'     => 'badge-secondary',
                                'Siswa'          => 'badge-info',
                                'Admin'          => 'badge-primary',
                            ][$jabatan] ?? 'badge-light';
                        @endphp

                        {{-- FIX: Menambahkan class 'hide-sidebar-mini' agar teks menghilang saat sidebar diperkecil --}}
                        <div class="user-info hide-sidebar-mini">
                            <h6 class="font-weight-bold mb-1" style="font-size: 14px;">{{ $namaUser }}</h6>
                            <span class="badge {{ $badgeColor }} mb-1">{{ $jabatan }}</span>
                            <div class="text-muted" style="font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $user->email }}</div>
                        </div>
                    </div>

                    <ul class="sidebar-menu">
                        <li class="menu-header">Dashboard</li>
                        <li class="{{ Request::is('home*') ? 'active' : '' }}"><a href="{{ route('home') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                        
                        {{-- ================= DEV ================= --}}
                        @if (auth()->user()->role === 'Dev')

                            <li class="menu-header">Data Siakad</li>
                            <li class="{{ Request::segment(1) == 'jadwal' ? 'active' : '' }}">
                                <a href="{{ url('jadwal') }}" class="nav-link"><i class="fas fa-calendar-alt"></i> <span>Jadwal</span></a>
                            </li>
                            <li class="{{ Request::is('migrasi*') ? 'active' : '' }}">
                                <a href="{{ url('migrasi') }}" class="nav-link"><i class="fas fa-rocket"></i> <span>Migrasi Data</span></a>
                            </li>
                
                            <li class="menu-header">Data Master</li>
                            <li class="nav-item dropdown {{ Request::is('pegawai*') ? 'active' : '' }}">
                                <a href="#" class="nav-link has-dropdown"><i class="fas fa-user-tie"></i> <span>GTK</span></a>
                                <ul class="dropdown-menu">
                                    <li class="{{ request('kategori') != 'tendik' && Request::is('pegawai*') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('pegawai.index') }}">Guru</a>
                                    </li>
                                    <li class="{{ request('kategori') == 'tendik' ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('pegawai.index', ['kategori' => 'tendik']) }}">Tendik</a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown {{ Request::is('siswa*') || Request::is('pd-keluar*') ? 'active' : '' }}">
                                <a href="#" class="nav-link has-dropdown">
                                    <i class="fas fa-user-graduate"></i> <span>Siswa</span>
                                </a>
                                <ul class="dropdown-menu">
                                    {{-- PESERTA DIDIK --}}
                                    <li class="{{ Request::is('siswa') && !Request::is('siswa/*') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('siswa.index') }}">
                                            Peserta Didik
                                        </a>
                                    </li>
                                    {{-- PD KELUAR --}}
                                    <li class="{{ Request::is('pd-keluar*') ? 'active text-danger' : '' }}">
                                        <a class="nav-link {{ Request::is('pd-keluar*') ? 'text-danger font-weight-bold' : '' }}" href="{{ route('pd-keluar.index') }}">
                                            PD Keluar
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="{{ Request::segment(1) == 'kelas' ? 'active' : '' }}">
                                <a href="{{ url('kelas') }}" class="nav-link"><i class="fas fa-school"></i> <span>Kelas</span></a>
                            </li>
                            <li class="{{ Request::segment(1) == 'mapel' ? 'active' : '' }}">
                                <a href="{{ url('mapel') }}" class="nav-link"><i class="fas fa-book"></i> <span>Mapel</span></a>
                            </li>
                            <li class="{{ Request::segment(1) == 'tahun' ? 'active' : '' }}">
                                <a href="{{ url('tahun') }}" class="nav-link"><i class="fas fa-calendar-check"></i> <span>Tahun Pelajaran</span></a>
                            </li>

                            <li class="menu-header">Publikasi</li>
                            <li class="{{ Request::segment(1) == 'informasi' ? 'active' : '' }}">
                                <a href="{{ url('informasi') }}" class="nav-link"><i class="fas fa-image"></i> <span>Informasi</span></a>
                            </li>
                            <li class="{{ Request::segment(1) == 'graduation-settings' ? 'active' : '' }}">
                                <a href="{{ route('graduation-settings.index') }}" class="nav-link"><i class="fas fa-graduation-cap"></i> <span>Atur Kelulusan</span></a>
                            </li>

                            <li class="menu-header">Manajemen User</li>
                            <li class="nav-item dropdown {{ Request::segment(1) == 'user' ? 'active' : '' }}">
                                <a href="#" class="nav-link has-dropdown"><i class="fas fa-key"></i> <span>User</span></a>
                                <ul class="dropdown-menu">
                                    <li class="{{ Request::segment(1) == 'user' && Request::segment(2) == 'guru' ? 'active' : '' }}"><a class="nav-link" href="{{ url('user/guru') }}">GTK</a></li>
                                    <li class="{{ Request::segment(1) == 'user' && Request::segment(2) == 'siswa' ? 'active' : '' }}"><a class="nav-link" href="{{ url('user/siswa') }}">Siswa</a></li>
                                    <li class="{{ Request::is('user/active-sessions') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('user/active-sessions') }}">
                                            <i class="fas fa-network-wired mr-2"></i> Riwayat Akses
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="menu-header">Laporan</li>
                            <li class="{{ Request::segment(1) == 'laporans' ? 'active' : '' }}">
                                <a href="{{ url('laporans') }}" class="nav-link"><i class="fas fa-file"></i> <span>Laporan Nilai</span></a>
                            </li>
                        @endif

                        {{-- ================= KEPALA SEKOLAH ================= --}}
                        @if (auth()->user()->role === 'Guru' && auth()->user()->pegawai && auth()->user()->pegawai->jabatan === 'Kepala Sekolah')
                            <li class="menu-header">Manajemen Data</li>
                            <li>
                                <a href="{{ url('siswa') }}" class="nav-link"><i class="fas fa-user-graduate"></i> <span>Data Siswa</span></a>
                            </li>
                            <li>
                                <a href="{{ url('pegawai') }}" class="nav-link"><i class="fas fa-user-tie"></i> <span>Data GTK</span></a>
                            </li>
                            <li>
                                <a href="{{ url('kelas') }}" class="nav-link"><i class="fas fa-school"></i> <span>Rombel/Kelas</span></a>
                            </li>
                            <li>
                                <a href="{{ url('jadwal') }}" class="nav-link"><i class="fas fa-calendar-alt"></i> <span>Jadwal</span></a>
                            </li>

                            <li class="menu-header">Rekap & Laporan</li>
                            <li>
                                <a href="{{ url('rekap-guru') }}" class="nav-link"><i class="fas fa-users"></i> <span>Rekap GTK</span></a>
                            </li>
                            <li>
                                <a href="{{ url('rekap-siswa') }}" class="nav-link"><i class="fas fa-user-graduate"></i> <span>Rekap Siswa</span></a>
                            </li>
                            <li>
                                <a href="{{ url('laporans') }}" class="nav-link"><i class="fas fa-file-alt"></i> <span>Laporan Nilai</span></a>
                            </li>

                            <li class="menu-header">Publikasi</li>
                            <li class="{{ Request::segment(1) == 'informasi' ? 'active' : '' }}">
                                <a href="{{ url('informasi') }}" class="nav-link"><i class="fas fa-image"></i> <span>Informasi</span></a>
                            </li>
                        @endif

                        {{-- ================= TENAGA KEPENDIDIKAN (TENDIK) ================= --}}
                        @if (auth()->user()->role === 'Guru' && auth()->user()->pegawai && in_array(auth()->user()->pegawai->jabatan, ['Tenaga Administrasi', 'Operator Sekolah', 'Pustakawan', 'Penjaga Sekolah']))
                            <li class="menu-header">Manajemen Data</li>
                            <li>
                                <a href="{{ url('siswa') }}" class="nav-link"><i class="fas fa-user-graduate"></i> <span>Data Siswa</span></a>
                            </li>
                            <li>
                                <a href="{{ url('pegawai') }}" class="nav-link"><i class="fas fa-user-tie"></i> <span>Data GTK</span></a>
                            </li>
                            <li>
                                <a href="{{ url('kelas') }}" class="nav-link"><i class="fas fa-school"></i> <span>Rombel/Kelas</span></a>
                            </li>
                            <li>
                                <a href="{{ url('jadwal') }}" class="nav-link"><i class="fas fa-calendar-alt"></i> <span>Jadwal</span></a>
                            </li>

                            <li class="menu-header">Laporan</li>
                            <li class="{{ Request::segment(1) == 'laporans' ? 'active' : '' }}">
                                <a href="{{ url('laporans') }}" class="nav-link"><i class="fas fa-file"></i> <span>Laporan Nilai</span></a>
                            </li>

                            <li class="menu-header">Publikasi</li>
                            <li class="{{ Request::segment(1) == 'informasi' ? 'active' : '' }}">
                                <a href="{{ url('informasi') }}" class="nav-link"><i class="fas fa-image"></i> <span>Informasi</span></a>
                            </li>
                        @endif

                        {{-- ================= GURU BIASA (PENDIDIK) ================= --}}
                        @if (auth()->user()->role === 'Guru' && auth()->user()->pegawai && in_array(auth()->user()->pegawai->jabatan, ['Guru Kelas', 'Guru Mapel']))
                            <li class="menu-header">Guru</li>
                            <li>
                                <a href="{{ url('jadwal') }}" class="nav-link"><i class="fas fa-calendar"></i> <span>Jadwal Mengajar</span></a>
                            </li>
                            <li>
                                <a href="{{ url('nilai') }}" class="nav-link"><i class="fas fa-check-circle"></i> <span>Input Nilai</span></a>
                            </li>
                            <li>
                                <a href="{{ url('siswa-saya') }}" class="nav-link"><i class="fas fa-users"></i> <span>Siswa Saya (Wali Kelas)</span></a>
                            </li>
                        @endif

                        {{-- ================= SISWA ================= --}}
                        @if (auth()->user()->role === 'Siswa')
                            <li class="menu-header">Akademik</li>
                            @if (auth()->user()->siswa?->status === 'Aktif')
                                <li>
                                    <a href="{{ url('jadwal') }}" class="nav-link"><i class="fas fa-calendar-alt"></i> <span>Jadwal Belajar</span></a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ url('nilai/saya') }}" class="nav-link"><i class="fas fa-poll"></i> <span>Lihat Nilai</span></a>
                            </li>
                        @endif
                    </ul>
                </aside>
            </div>

            {{-- Main Content --}}
            @yield('content')

            {{-- ======================== FOOTER DIPERBAIKI ======================== --}}
            {{-- FIX 1: Membuka Tag <footer class="main-footer"> yang sebelumnya ter-comment --}}
            <footer class="main-footer">
                <div class="footer-left">
                    Copyright &copy; {{ date('Y') }} <div class="bullet"></div> <strong>SDN PASIRIPIS</strong>
                </div>
                
                {{-- FIX 2: Teks sebelah kanan dengan pewarnaan link yang baik --}}
                <div class="footer-right">
                    SIAKAD v1.1 <div class="bullet"></div> Developed by <a href="#">Usep Suherman</a>
                </div>
            </footer>

        </div>
    </div>
    
    <script src="{{ asset('update/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('update/modules/popper.js') }}"></script>
    <script src="{{ asset('update/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('update/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('update/modules/moment.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="{{ asset('update/js/stisla.js') }}"></script>
    <script src="{{ asset('update/js/scripts.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateClock() {
            moment.locale('id');
            const now = moment();
            
            // Logika Ringkas untuk HP
            const isMobile = window.innerWidth < 992;
            const waktuFormatted = isMobile ? now.format('HH:mm:ss') : now.format('dddd, D MMMM YYYY | HH:mm:ss');
            
            let greetingText = 'Selamat Malam';
            const hour = now.hour();
            if (hour >= 4 && hour < 11) greetingText = 'Selamat Pagi';
            else if (hour >= 11 && hour < 15) greetingText = 'Selamat Siang';
            else if (hour >= 15 && hour < 18) greetingText = 'Selamat Sore';

            if (document.getElementById('greeting')) document.getElementById('greeting').textContent = greetingText + ',';
            if (document.getElementById('realtime-clock')) document.getElementById('realtime-clock').textContent = waktuFormatted + (isMobile ? '' : ' WIB');
        }

        updateClock();
        setInterval(updateClock, 1000);
    });
    </script>
    @stack('scripts')
</body>
</html>