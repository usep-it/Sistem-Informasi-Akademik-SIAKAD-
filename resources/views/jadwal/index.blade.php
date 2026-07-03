@extends('layouts.backend')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --siakad-primary: #8252fa;
            --siakad-secondary: #eca2f1;
            --siakad-success: #27ae60;
            --siakad-info: #3498db;
            --siakad-dark: #1e293b;
            --siakad-soft: #f8faff;
        }

        .main-content { overflow: visible !important; }

        /* --- TAHUN AKTIF STATUS BAR (PREMIUM LOOK) --- */
        .ta-status-bar {
            background: #fff;
            border-radius: 16px;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
            animation: fadeInDown 0.8s ease-out;
        }

        .ta-status-bar::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(to bottom, var(--siakad-primary), var(--siakad-secondary));
        }

        .ta-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(130, 82, 250, 0.1) 0%, rgba(236, 162, 241, 0.1) 100%);
            color: var(--siakad-primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        /* --- PERBAIKAN UTAMA: MASONRY GRID SYSTEM (Tanpa Rongga Kosong) --- */
        .schedule-grid {
            column-count: 1; /* Default 1 kolom di mobile */
            column-gap: 20px;
            width: 100%;
        }

        @media (min-width: 992px) {
            .schedule-grid { column-count: 2; } /* 2 kolom di desktop */
        }

        .schedule-col {
            display: inline-block; /* Wajib agar masonry bekerja */
            width: 100%;
            margin-bottom: 20px;
            break-inside: avoid; /* Mencegah kartu terpotong di tengah kolom */
        }

        /* --- DAY CARD: NATURAL HEIGHT --- */
        .day-card {
            border: none;
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: auto; 
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .day-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(130, 82, 250, 0.1);
            border-color: rgba(130, 82, 250, 0.3);
        }

        .day-header {
            padding: 18px 25px;
            background: var(--siakad-soft);
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .day-title {
            font-weight: 800;
            color: var(--siakad-dark);
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .day-title i { color: var(--siakad-primary); font-size: 18px; }

        /* Sesi Item */
        .session-item {
            padding: 18px 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #f8fafc;
            transition: background 0.2s;
        }

        .session-item:last-child { border-bottom: none; }
        .session-item:hover { background: #fcfdff; }

        .time-box {
            min-width: 85px;
            padding: 10px;
            background: #fff;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #eef2ff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .time-start { display: block; font-weight: 800; color: var(--siakad-primary); font-size: 14px; }
        .time-end { display: block; font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-top: 2px; }

        .subject-info { flex: 1; }
        .subject-name { display: block; font-weight: 700; color: #334155; font-size: 14px; line-height: 1.4; }
        .meta-info { display: block; font-size: 11px; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 6px; }

        /* --- PROFILE WIDGET --- */
        .profile-card-modern {
            border: none;
            border-radius: 24px;
            background: #fff;
            padding: 40px 25px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
            margin-bottom: 30px;
            position: sticky;
            top: 100px;
        }

        .profile-img-container img {
            width: 115px;
            height: 115px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #f8faff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .meta-box-item {
            background: #f8fafc;
            border-radius: 15px;
            padding: 12px 18px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 767.98px) {
            .session-item { gap: 15px; padding: 15px; }
            .time-box { min-width: 75px; padding: 8px; }
            .profile-card-modern { position: static; }
        }
    </style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                @if (auth()->user()->role == 'Dev')
                    <h1><i class="fas fa-calendar-alt mr-2"></i> Manajemen Jadwal Pelajaran</h1>
                @elseif (auth()->user()->role == 'Guru')
                    <h1><i class="fas fa-chalkboard-teacher mr-2"></i> Jadwal Mengajar</h1>
                @else
                    <h1><i class="fas fa-book-reader mr-2"></i> Jadwal Belajar Saya</h1>
                @endif
            </div>

            {{-- 1. STATUS TAHUN AJARAN --}}
            <div class="ta-status-bar shadow-sm">
                <div class="ta-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div style="flex:1;">
                    <small class="text-muted d-block text-uppercase font-weight-bold" style="letter-spacing: 1px; font-size: 10px;">Periode Akademik Aktif</small>
                    @if ($tahunAktif)
                        <span class="font-weight-bold text-dark" style="font-size: 17px;">
                            {{ $tahunAktif->nama }} &mdash; 
                            <span class="text-primary">Semester {{ ucfirst($tahunAktif->semester) }}</span>
                        </span>
                    @else
                        <span class="text-danger font-weight-bold">Tahun Ajaran Belum Diaktifkan</span>
                    @endif
                </div>
                @if(auth()->user()->role == 'Dev')
                    <a href="{{ route('tahun.index') }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
                        <i class="fas fa-cog mr-1"></i> Pengaturan
                    </a>
                @endif
            </div>

            <div class="section-body">

                {{-- ====================================================== --}}
                {{-- ================ TAMPILAN UNTUK ADMIN/DEV ================ --}}
                {{-- ====================================================== --}}
                @if (auth()->user()->role == 'Dev')
                    <div class="row">
                        @php
                            $kelasFilter = $selectedTahun ? $kelas->where('tahun_id', $selectedTahun->id) : $kelas;
                        @endphp

                        @forelse ($kelasFilter as $k)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="day-card p-4 mb-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <div class="ta-icon" style="width: 45px; height: 45px; font-size: 18px; background: rgba(52, 152, 219, 0.1); color: var(--siakad-info);">
                                            <i class="fas fa-door-open"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 text-dark">Kelas {{ $k->kelas }}</h5>
                                            <small class="text-muted">{{ $k->tahun->nama ?? '-' }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-light p-3 rounded-xl mb-4" style="border-radius: 15px;">
                                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 9px; letter-spacing: 1px;">Wali Kelas</small>
                                        <span class="font-weight-bold text-dark small">{{ $k->pegawai->nama ?? 'Belum Ditentukan' }}</span>
                                    </div>

                                    <a href="{{ route('jadwal.kelas', $k->id) }}" class="btn btn-primary btn-block rounded-pill font-weight-bold shadow-sm">
                                        Kelola Jadwal <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="opacity-20 mb-3">
                                <h6 class="text-muted">Data Rombongan Belajar tidak ditemukan.</h6>
                            </div>
                        @endforelse
                    </div>
                @endif

                {{-- ====================================================== --}}
                {{-- ================== TAMPILAN UNTUK GURU ================= --}}
                {{-- ====================================================== --}}
                @if (auth()->user()->role == 'Guru')
                    @php 
                        $hasAnySchedule = isset($jaguru) && $jaguru->contains(fn($h) => $h->jadwal->isNotEmpty()); 
                    @endphp
                    
                    @if ($hasAnySchedule)
                        <div class="schedule-grid">
                            @foreach ($jaguru as $hari)
                                @if ($hari->jadwal->isNotEmpty())
                                    <div class="schedule-col">
                                        <div class="day-card">
                                            <div class="day-header">
                                                <div class="day-title"><i class="fas fa-calendar-alt"></i>{{ strtoupper($hari->nama) }}</div>
                                                <span class="badge badge-primary rounded-pill px-3 shadow-sm" style="font-size: 10px;">{{ $hari->jadwal->count() }} SESI</span>
                                            </div>
                                            <div class="session-list">
                                                @foreach ($hari->jadwal as $jd)
                                                    <div class="session-item">
                                                        <div class="time-box">
                                                            <span class="time-start">{{ \Carbon\Carbon::parse($jd->jam_mulai)->format('H:i') }}</span>
                                                            <span class="time-end">{{ \Carbon\Carbon::parse($jd->jam_selesai)->format('H:i') }}</span>
                                                        </div>
                                                        <div class="subject-info">
                                                            <span class="subject-name text-primary">{{ $jd->mapel?->nama }}</span>
                                                            <span class="meta-info font-weight-bold"><i class="fas fa-users text-info"></i> Kelas {{ $jd->kelas?->kelas }}</span>
                                                        </div>
                                                        <i class="fas fa-chevron-right text-muted opacity-20"></i>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="card border-0 shadow-sm rounded-xl">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted opacity-20 mb-3"></i>
                                <h6 class="text-muted font-weight-bold">Anda belum memiliki jadwal mengajar pada periode akademik ini.</h6>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- ====================================================== --}}
                {{-- ================= TAMPILAN UNTUK SISWA ================= --}}
                {{-- ====================================================== --}}
                @if (auth()->user()->role == 'Siswa')
                    <div class="row">
                        <div class="col-12 col-lg-4 col-xl-3">
                            <div class="profile-card-modern">
                                <div class="profile-img-container mb-4">
                                    <img src="{{ Auth::user()->foto ? asset('foto_user/' . Auth::user()->foto) : (Auth::user()->siswa?->foto ? asset('foto_siswa/' . Auth::user()->siswa->foto) : 'https://placehold.co/120x120/EFEFEF/AAAAAA?text=Foto') }}">
                                    <div class="status-badge-online shadow-sm" style="position:absolute; bottom:10px; right:10px; width:20px; height:20px; background:#2ecc71; border:4px solid #fff; border-radius:50%;"></div>
                                </div>
                                <h5 class="font-weight-bold text-dark mb-1">{{ Auth::user()->siswa?->nama ?? Auth::user()->name }}</h5>
                                <p class="text-muted small mb-4">NISN: {{ Auth::user()->siswa?->nisn ?? '-' }}</p>
                                
                                <div class="meta-box-item">
                                    <small class="text-muted font-weight-bold">KELAS</small>
                                    <span class="badge badge-primary rounded-pill">{{ Auth::user()->siswa?->kelas?->kelas ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8 col-xl-9">
                            <div class="schedule-grid">
                                @forelse ($jadwalSiswaGrouped as $hari => $jadwals)
                                    <div class="schedule-col">
                                        <div class="day-card">
                                            <div class="day-header">
                                                <div class="day-title text-info"><i class="fas fa-calendar-check"></i>{{ strtoupper($hari) }}</div>
                                            </div>
                                            <div class="session-list">
                                                @foreach($jadwals as $item)
                                                    <div class="session-item">
                                                        <div class="time-box" style="background: #f0f9ff; border-color: #e0f2fe;">
                                                            <span class="time-start text-info">{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}</span>
                                                            <span class="time-end">{{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</span>
                                                        </div>
                                                        <div class="subject-info">
                                                            <span class="subject-name">{{ $item->mapel?->nama }}</span>
                                                            <span class="meta-info font-weight-bold text-dark small"><i class="fas fa-chalkboard-teacher text-info"></i> {{ $item->pegawai?->nama }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="day-card py-5 text-center border-dashed" style="background: rgba(255,255,255,0.6);">
                                            <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" style="width: 80px; opacity: 0.3;">
                                            <h6 class="text-muted mt-4 font-weight-bold">Belum ada jadwal yang dirilis untuk kelas Anda pada periode ini.</h6>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Animasi muncul berurutan (Staggered Animation)
    $('.schedule-col').each(function(i) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(15px)',
            'transition': 'all 0.4s ease ' + (i * 0.08) + 's'
        });
        
        setTimeout(() => {
            $(this).css({
                'opacity': '1',
                'transform': 'translateY(0)'
            });
        }, 50);
    });

    // Auto-hide notifikasi
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush