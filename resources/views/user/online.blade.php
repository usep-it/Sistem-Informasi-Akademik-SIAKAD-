@extends('layouts.backend')

@push('styles')
<style>
    :root {
        --siakad-primary: #8252fa;
        --siakad-secondary: #eca2f1;
        --siakad-gradient: linear-gradient(135deg, #8252fa 0%, #eca2f1 100%);
    }

    .main-content { overflow: visible !important; }
    
    /* --- CARD STATISTIK PREMIUM --- */
    .stat-card {
        border: none;
        border-radius: 24px;
        padding: 25px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(130, 82, 250, 0.12); }
    
    .stat-icon {
        width: 55px; height: 55px;
        background: #f5f3ff;
        color: var(--siakad-primary);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin-bottom: 20px;
    }

    /* --- CHART BOX STYLING --- */
    .chart-container-box {
        background: #fff;
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        margin-bottom: 30px;
        border: 1px solid #f1f5f9;
        animation: fadeInUp 0.8s ease-out;
    }

    /* --- FILTER NAV PILLS --- */
    .nav-pills-premium .nav-link {
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 700;
        color: #64748b;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-right: 10px;
        margin-bottom: 10px;
        transition: all 0.3s;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nav-pills-premium .nav-link.active {
        background: var(--siakad-gradient);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(130, 82, 250, 0.3);
    }
    .nav-pills-premium .nav-link:hover:not(.active) {
        background-color: #f1f5f9;
        color: var(--siakad-primary);
    }

    /* --- STATUS INDICATOR --- */
    .status-badge-online {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        background: #dcfce7;
        color: #16a34a;
        display: inline-flex; align-items: center; gap: 8px;
        border: 1px solid #bbf7d0;
    }
    .dot-pulse {
        width: 8px; height: 8px; background: #22c55e; border-radius: 50%;
        animation: pulse-green 1.5s infinite;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .ip-badge {
        font-family: 'Courier New', monospace;
        background: #f8fafc;
        padding: 5px 12px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
    }

    /* Override gaya default length menu DataTables agar serasi dengan Bootstrap */
    .dataTables_length select {
        border-radius: 8px;
        padding: 4px 8px;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        outline: none;
        margin: 0 6px;
    }
    .dataTables_length label {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header justify-content-between">
                <h1><i class="fas fa-chart-line mr-2 text-primary"></i> Monitoring Aktivitas Sistem</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Log Akses</div>
                </div>
            </div>

            <div class="section-body">
                
                {{-- 1. HEADER STATISTIK --}}
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card" style="border-top: 4px solid var(--siakad-primary);">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <small class="text-muted font-weight-bold text-uppercase" style="letter-spacing: 1px;">Total Akses User</small>
                            <h3 class="mb-0 font-weight-bold mt-1 text-dark">{{ number_format($stats['total_akses']) }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card" style="border-top: 4px solid #3498db;">
                            <div class="stat-icon" style="background: #e0f2fe; color: #3498db;"><i class="fas fa-user-graduate"></i></div>
                            <small class="text-muted font-weight-bold text-uppercase" style="letter-spacing: 1px;">Jumlah Siswa Login </small>
                            <h3 class="mb-0 font-weight-bold mt-1 text-dark">{{ number_format($stats['siswa_login']) }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card" style="border-top: 4px solid #27ae60;">
                            <div class="stat-icon" style="background: #ecfdf5; color: #27ae60;"><i class="fas fa-chalkboard-teacher"></i></div>
                            <small class="text-muted font-weight-bold text-uppercase" style="letter-spacing: 1px;">Jumlah Guru Login</small>
                            <h3 class="mb-0 font-weight-bold mt-1 text-dark">{{ number_format($stats['guru_login']) }}</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card" style="border-top: 4px solid #e67e22;">
                            <div class="stat-icon" style="background: #fff7ed; color: #e67e22;"><i class="fas fa-network-wired"></i></div>
                            <small class="text-muted font-weight-bold text-uppercase" style="letter-spacing: 1px;">IP Unik</small>
                            <h3 class="mb-0 font-weight-bold mt-1 text-dark">{{ number_format($stats['ip_unik']) }}</h3>
                        </div>
                    </div>
                </div>

                {{-- 2. GRAFIK TREN --}}
                <div class="row">
                    <div class="col-12">
                        <div class="chart-container-box shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                <div>
                                    <h5 class="text-dark font-weight-bold mb-1">Visualisasi Tren Aktivitas Akses</h5>
                                    <p class="text-muted small mb-0">Grafik perkembangan login pengguna dalam 7 hari terakhir.</p>
                                </div>
                                <div class="badge badge-light px-3 py-2 border text-muted" style="border-radius: 12px;">
                                    <i class="fas fa-history mr-2"></i> Update terakhir: {{ date('H:i') }} WIB
                                </div>
                            </div>
                            <div style="position: relative; height: 350px;">
                                <canvas id="loginTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. TABEL DETAIL SESSION DENGAN FILTER ROLE --}}
                <div class="card shadow-sm border-0" style="border-radius: 24px; overflow: hidden;">
                    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h4 class="text-dark font-weight-bold">Log Riwayat Akses Real-time</h4>
                        
                        {{-- FILTER ROLE TABS --}}
                        <ul class="nav nav-pills nav-pills-premium" id="pills-tab-role">
                            <li class="nav-item">
                                <a class="nav-link active filter-role" href="javascript:void(0)" data-role="all">
                                    <i class="fas fa-border-all mr-1"></i> Semua
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-role" href="javascript:void(0)" data-role="Admin">
                                    <i class="fas fa-user-shield mr-1"></i> Admin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-role" href="javascript:void(0)" data-role="Guru">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i> Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-role" href="javascript:void(0)" data-role="Siswa">
                                    <i class="fas fa-user-graduate mr-1"></i> Siswa
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table-active-sessions" style="width: 100%;">
                                <thead>
                                    <tr class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 1px; background: #fafafa;">
                                        <th width="5%" class="pl-4">No</th>
                                        <th>Identitas Pengguna</th>
                                        <th>Role</th>
                                        <th>Alamat IP</th>
                                        <th>Terakhir Akses</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $index => $u)
                                        @php
                                            $lastSeen = \Carbon\Carbon::parse($u->last_seen_at);
                                            $isOnline = $lastSeen->diffInMinutes(now('Asia/Jakarta')) <= 5;
                                            $displayName = $u->name;
                                            
                                            $userPhoto = 'https://ui-avatars.com/api/?name='.urlencode($displayName).'&background=8252fa&color=fff&bold=true';
                                            if ($u->foto) {
                                                $userPhoto = asset('foto_user/' . $u->foto);
                                            } elseif ($u->role == 'Guru' && $u->pegawai?->foto) {
                                                $userPhoto = asset('foto_pegawai/' . $u->pegawai->foto);
                                            } elseif ($u->role == 'Siswa' && $u->siswa?->foto) {
                                                $userPhoto = asset('foto_siswa/' . $u->siswa->foto);
                                            }

                                            if($u->role === 'Guru' && $u->pegawai) $displayName = $u->pegawai->nama;
                                            elseif($u->role === 'Siswa' && $u->siswa) $displayName = $u->siswa->nama;

                                            $labelFilter = ($u->role === 'Dev') ? 'Admin' : $u->role;
                                        @endphp
                                        <tr class="align-middle" data-role-group="{{ $labelFilter }}">
                                            <td class="pl-4">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center py-1">
                                                    <img src="{{ $userPhoto }}" class="rounded-circle shadow-sm mr-3" width="38" height="38" style="object-fit: cover; border: 2px solid #fff;">
                                                    <div>
                                                        <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ $displayName }}</div>
                                                        <small class="text-muted" style="font-size: 11px;">ID: {{ $u->username }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border text-uppercase font-weight-bold" style="font-size: 9px; padding: 4px 8px;">{{ $labelFilter }}</span>
                                            </td>
                                            <td><span class="ip-badge"><i class="fas fa-fingerprint mr-1"></i> {{ $u->ip_address ?? '127.0.0.1' }}</span></td>
                                            <td>
                                                <div class="font-weight-bold" style="font-size: 13px;">{{ $lastSeen->isoFormat('D MMM Y | HH:mm') }}</div>
                                                <small class="text-primary font-weight-bold" style="font-size: 11px;">{{ $lastSeen->locale('id')->diffForHumans() }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($isOnline)
                                                    <div class="status-badge-online"><span class="dot-pulse"></span> ONLINE</div>
                                                @else
                                                    <span class="badge badge-secondary py-1 px-3 rounded-pill" style="font-size: 10px; opacity: 0.6;">OFFLINE</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('update/modules/datatables/datatables.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Init DataTable dengan Menampilkan Kembali LengthMenu
        let dataTable;
        if ($.fn.DataTable) {
            dataTable = $('#table-active-sessions').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
                pageLength: 15,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                // FIX: Menambahkan karakter 'l' di bagian dom agar dropdown pilihan entri muncul
                dom: '<"d-flex justify-content-between align-items-center mb-3"l>rtip', 
                search: {
                    smart: true
                }
            });

            // LOGIKA FILTER ROLE (Nav Pills)
            $('.filter-role').on('click', function(e) {
                e.preventDefault();
                
                // Ubah tampilan tab aktif
                $('.filter-role').removeClass('active');
                $(this).addClass('active');

                let role = $(this).attr('data-role');

                if(role === 'all') {
                    dataTable.column(2).search('').draw();
                } else {
                    dataTable.column(2).search(role).draw();
                }
            });
        }

        // 2. Init Chart.js
        const canvas = document.getElementById('loginTrendChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(130, 82, 250, 0.5)');
            gradient.addColorStop(1, 'rgba(236, 162, 241, 0.01)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Login User',
                        data: {!! json_encode($chartCounts) !!},
                        borderColor: '#8252fa',
                        borderWidth: 4,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#8252fa',
                        pointBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            ticks: { stepSize: 1, color: '#94a3b8' },
                            grid: { color: 'rgba(0,0,0,0.03)' }
                        },
                        x: { 
                            ticks: { color: '#64748b', font: { weight: 'bold' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush