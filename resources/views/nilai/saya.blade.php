@extends('layouts.backend')

@push('styles')
<style>
    :root {
        --siakad-primary: #8252fa;
        --siakad-primary-dark: #6d3df0;
        --siakad-bg: #f1f5f9;
        --siakad-dark: #1e293b;
        --siakad-success: #27ae60;
        --siakad-danger: #dc3545;
    }

    .main-content {
        overflow: visible !important;
    }

    /* =========================================
       FILTER BOX
       ========================================= */
    .selection-box {
        background: #fff;
        border-radius: 22px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 35px rgba(0,0,0,.05);
        border-top: 5px solid var(--siakad-primary);
    }

    .selection-header {
        padding: 35px 30px 15px;
        text-align: center;
    }

    .selection-header i {
        font-size: 55px;
        color: var(--siakad-primary);
        opacity: .2;
        margin-bottom: 18px;
    }

    .selection-header h4 {
        font-weight: 800;
        color: var(--siakad-dark);
    }

    /* =========================================
       CARD & LAYOUT
       ========================================= */
    .student-card {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 5px 25px rgba(0,0,0,.04);
        margin-bottom: 25px;
    }

    .student-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 25px;
    }

    /* =========================================
       IDENTITAS
       ========================================= */
    .meta-table {
        width: 100%;
        border-collapse: collapse;
    }

    .meta-table td {
        padding: 8px 0;
        font-size: 14.5px;
        vertical-align: top;
    }

    .meta-label {
        width: 140px;
        color: var(--siakad-primary);
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .5px;
    }

    .meta-sep {
        width: 20px;
        text-align: center;
        font-weight: 700;
        color: #cbd5e1;
    }

    .meta-value {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.5;
    }

    /* =========================================
       TABEL NILAI
       ========================================= */
    .table-nilai-siakad {
        width: 100%;
        border-collapse: collapse;
        border: 1.5px solid #444;
        font-size: 14px;
        background: #fff;
    }

    .table-nilai-siakad th {
        background: #ececec;
        border: 1px solid #555;
        padding: 12px 8px;
        text-align: center;
        font-weight: 800;
        text-transform: uppercase;
        color: #333;
    }

    .table-nilai-siakad td {
        border: 1px solid #555;
        padding: 12px 15px;
        vertical-align: middle;
    }

    .table-nilai-siakad tfoot td {
        background: #f3f4f6;
        font-weight: 800;
        border-top: 2px solid #444;
    }

    /* =========================================
       MOTIVASI CARD (ANIMASI)
       ========================================= */
    .motivation-card {
        border-radius: 18px;
        padding: 25px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
        animation: slideUpFade 1s ease-out;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .motivation-card.top-rank {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .motivation-card.standard-rank {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }

    .motivation-icon {
        position: absolute;
        right: -10px;
        bottom: -20px;
        font-size: 120px;
        opacity: 0.15;
        transform: rotate(-15deg);
    }

    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .rank-badge {
        padding: 18px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        background: linear-gradient(135deg, var(--siakad-primary) 0%, #eca2f1 100%);
        color: white;
        margin-top: 15px;
        box-shadow: 0 4px 15px rgba(130, 82, 250, 0.2);
    }

    .btn-download-rapor {
        background: var(--siakad-primary);
        color: white !important;
        font-weight: 700;
        border-radius: 12px;
        padding: 14px 25px;
        transition: 0.3s;
        box-shadow: 0 6px 20px rgba(130, 82, 250, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        width: 100%;
    }

    .btn-download-rapor:hover {
        background: var(--siakad-primary-dark);
        transform: translateY(-2px);
    }

    .chart-container-wrapper {
        position: relative;
        height: 350px;
        width: 100%;
    }

    @media (max-width: 991px) {
        .col-lg-4 { border-bottom: 1px solid #f1f5f9; margin-bottom: 30px; padding-bottom: 25px; }
        .meta-label { width: 120px; }
        .chart-container-wrapper { height: 300px; }
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Hasil Belajar Saya</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Nilai Akademik</div>
                </div>
            </div>

            <div class="section-body">
                
                {{-- 1. KOTAK VALIDASI / PILIH PERIODE --}}
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="card selection-box mb-5">
                            <div class="selection-header">
                                <i class="fas fa-file-invoice"></i>
                                <h4>Pilih Periode Laporan</h4>
                                <p>Silakan pilih tahun pelajaran untuk melihat rekapitulasi nilai Anda.</p>
                            </div>
                            <div class="card-body px-4 pb-5">
                                <form action="{{ route('nilai.saya') }}" method="GET">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tahun Pelajaran & Semester</label>
                                        <select name="tahun_id" class="form-control select2" required>
                                            <option value="" disabled {{ !request()->filled('tahun_id') ? 'selected' : '' }}>-- Pilih Periode --</option>
                                            @foreach ($daftarTahun as $tahun)
                                                <option value="{{ $tahun->id }}" {{ request('tahun_id') == $tahun->id ? 'selected' : '' }}>
                                                    TA {{ $tahun->nama }} - Semester {{ ucfirst($tahun->semester) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block shadow-sm py-2 font-weight-bold" style="background: var(--siakad-primary); border:none;">
                                        <i class="fas fa-check-circle mr-2"></i> Tampilkan Laporan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. TAMPILAN DATA (Hanya jika periode dipilih) --}}
                @if(request()->filled('tahun_id') && isset($tahunAktif))
                    @php
                        // Ambil Nama Wali Kelas Langsung dari Database
                        $namaWali = $kelasSiswaPadaTahunItu->pegawai->nama ?? 'Wali Kelas';

                        // Cek kelengkapan nilai
                        $countMapelValid = $nilaisiswa->count();
                        
                        // Menentukan konten motivasi
                        $isTopThree = ($rankSiswa !== '-' && (int)$rankSiswa <= 3);
                        $motivationClass = $isTopThree ? 'top-rank' : 'standard-rank';
                        $motivationIcon = $isTopThree ? 'fa-trophy' : 'fa-rocket';
                        
                        if ($isTopThree) {
                            $motivationTitle = "Luar Biasa, " . explode(' ', Auth::user()->siswa->nama)[0] . "!";
                            $motivationText = "Selamat atas pencapaian gemilangmu sebagai salah satu bintang di kelas. Pertahankan semangat belajarmu dan teruslah menjadi inspirasi bagi teman-teman yang lain!";
                        } else {
                            $motivationTitle = "Tetap Semangat, " . explode(' ', Auth::user()->siswa->nama)[0] . "!";
                            $motivationText = "Setiap langkah kecil dalam belajar adalah progres menuju kesuksesan. Teruslah asah kemampuanmu, karena usaha tidak akan pernah mengkhianati hasil!";
                        }
                    @endphp

                    <div class="row">
                        
                        {{-- Identitas & Download --}}
                        <div class="col-12 col-lg-4">
                            {{-- MOTIVASI BOX --}}
                            <div class="motivation-card {{ $motivationClass }} shadow-sm">
                                <i class="fas {{ $motivationIcon }} motivation-icon"></i>
                                <h5 class="font-weight-bold mb-2">{{ $motivationTitle }}</h5>
                                <p class="mb-0 small" style="line-height: 1.6;">{{ $motivationText }}</p>
                            </div>

                            <div class="card student-card mb-4">
                                <div class="card-header">
                                    <h5 class="text-primary font-weight-bold mb-0">Identitas Peserta Didik</h5>
                                </div>
                                <div class="card-body">
                                    <table class="meta-table">
                                        <tr>
                                            <td class="meta-label">Nama Siswa</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-value">{{ strtoupper(Auth::user()->siswa->nama) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="meta-label">NISN / NIS</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-value">{{ Auth::user()->siswa->nisn }} / {{ Auth::user()->siswa->nis ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="meta-label">Kelas / Fase</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-value">{{ $kelasSiswaPadaTahunItu->kelas ?? '-' }} / {{ $kelasSiswaPadaTahunItu->nama ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="meta-label">Wali Kelas</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-value">{{ $namaWali }}</td>
                                        </tr>
                                        <tr>
                                            <td class="meta-label">Periode</td>
                                            <td class="meta-sep">:</td>
                                            <td class="meta-value">{{ $tahunAktif->nama }} ({{ ucfirst($tahunAktif->semester) }})</td>
                                        </tr>
                                    </table>

                                    @php
                                        $rankStyle = '';
                                        if($rankSiswa == 1) $rankStyle = 'background: linear-gradient(135deg,#facc15,#f59e0b); color:#111;';
                                        elseif($rankSiswa == 2) $rankStyle = 'background: linear-gradient(135deg,#e2e8f0,#cbd5e1); color:#111;';
                                        elseif($rankSiswa == 3) $rankStyle = 'background: linear-gradient(135deg,#92400e,#78350f); color:#fff;';
                                    @endphp
                                    
                                    {{-- <div class="rank-badge shadow-sm" style="{{ $rankStyle }}">
                                        <i class="fas fa-medal fa-2x"></i>
                                        <div>
                                            <small class="d-block text-uppercase font-weight-bold opacity-75" style="font-size: 10px;">Peringkat Kelas</small>
                                            <h4 class="mb-0 font-weight-bold">{{ $rankSiswa != '-' ? 'Juara ' . $rankSiswa : 'N/A' }}</h4>
                                        </div>
                                    </div> --}}
                                </div> 
                                <div class="card-footer bg-whitesmoke">
                                    <a href="{{ route('laporan.pdf', ['tahun' => $tahunAktif->id]) }}" 
                                       target="_blank" class="btn-download-rapor">
                                        <i class="fas fa-file-pdf mr-2"></i> UNDUH TRANSKRIP (PDF)
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Nilai --}}
                        <div class="col-12 col-lg-8">
                            <div class="card student-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="text-primary font-weight-bold mb-0">Rincian Hasil Belajar</h5>
                                    <span class="badge badge-light border text-uppercase" style="font-size: 10px">Akademik Terintegrasi</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table-nilai-siakad shadow-sm">
                                            <thead>
                                                <tr>
                                                    <th width="8%">No</th>
                                                    <th class="text-left">Mata Pelajaran</th>
                                                    <th width="25%">Nilai Akhir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalNilai = 0; $countMapel = 0; @endphp
                                                @forelse ($nilaisiswa as $mapel => $data)
                                                    @php
                                                        $h = $data->where('jenis','HARIAN')->avg('nilai');
                                                        $p = $data->where('jenis','PTS')->first()?->nilai;
                                                        $s = $data->where('jenis','PAS')->first()?->nilai;
                                                        $avgU = collect([$p, $s])->filter(fn($v) => !is_null($v))->avg();
                                                        
                                                        $nA = null;
                                                        if($h && $avgU) $nA = ($h * 0.6) + ($avgU * 0.4);
                                                        elseif($h) $nA = $h;
                                                        elseif($avgU) $nA = $avgU;

                                                        $nABulat = !is_null($nA) ? round($nA) : null;
                                                        if(!is_null($nA)) { $totalNilai += $nA; $countMapel++; }
                                                    @endphp
                                                    <tr class="text-center">
                                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                                        <td class="text-left font-weight-bold text-dark">{{ strtoupper($mapel) }}</td>
                                                        <td class="font-weight-bold text-primary" style="font-size: 1.1rem;">
                                                            {{ $nABulat ?? '' }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="3" class="text-center py-4">Data nilai belum tersedia.</td></tr>
                                                @endforelse
                                            </tbody>
                                            @if($countMapel > 0)
                                            <tfoot>
                                                <tr class="text-center">
                                                    <td colspan="2" class="text-right py-3 pr-4 text-uppercase">Rata-Rata Nilai :</td>
                                                    <td class="font-weight-bold text-primary" style="font-size: 1.1rem; background: #f0f9ff;">
                                                        {{ $countMapel > 0 && round($totalNilai / $countMapel) != 0 ? round($totalNilai / $countMapel) : '' }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                    
                                    <div class="mt-4 small text-muted text-center" style="font-size: 10px; line-height: 1.6;">
                                        Sistem Informasi Akademik SDN Pasiripis. Data nilai di atas bersifat resmi dan divalidasi oleh sistem database sekolah.
                                    </div>
                                </div>
                            </div>

                            {{-- GRAFIK MODERN --}}
                            <div class="card student-card mt-4 overflow-hidden">
                                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                                    <h5 class="text-primary font-weight-bold mb-0">
                                        <i class="fas fa-chart-bar mr-2"></i>Visualisasi Capaian Kompetensi
                                    </h5>
                                    <p class="text-muted small mt-1">Grafik perbandingan skor nilai antar mata pelajaran.</p>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container-wrapper">
                                        <canvas id="chartNilaiModern"></canvas>
                                    </div>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    @if(request()->filled('tahun_id') && isset($tahunAktif))
        const ctx = document.getElementById('chartNilaiModern');
        if(ctx){
            const chartCtx = ctx.getContext('2d');
            const gradient = chartCtx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(130, 82, 250, 0.9)'); 
            gradient.addColorStop(1, 'rgba(236, 162, 241, 0.4)'); 

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($nilaisiswa->toArray())) !!},
                    datasets: [{
                        label: 'Skor Nilai',
                        data: [
                            @foreach($nilaisiswa as $mapel => $data)
                                @php
                                    $h = $data->where('jenis','HARIAN')->avg('nilai');
                                    $p = $data->where('jenis','PTS')->first()?->nilai;
                                    $s = $data->where('jenis','PAS')->first()?->nilai;
                                    $avg = collect([$p, $s])->filter(fn($v) => !is_null($v))->avg();
                                    $na = ($h && $avg) ? (($h * 0.6) + ($avg * 0.4)) : ($h ?: $avg);
                                    echo round($na ?? 0).',';
                                @endphp
                            @endforeach
                        ],
                        backgroundColor: gradient,
                        borderColor: '#8252fa',
                        borderWidth: 0,
                        borderRadius: 12,
                        borderSkipped: false,
                        hoverBackgroundColor: '#6d3df0',
                        barThickness: 32,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(30, 41, 59, 0.95)',
                            titleFont: { size: 14, weight: 'bold', family: "'Inter', sans-serif" },
                            bodyFont: { size: 13, family: "'Inter', sans-serif" },
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return ' Skor Akhir: ' + context.parsed.y + ' Poin';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.6)',
                                drawBorder: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                font: { size: 11, weight: '600' },
                                color: '#64748b',
                                padding: 10
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 10, weight: '700' },
                                color: '#475569',
                                padding: 10,
                                callback: function(value) {
                                    const label = this.getLabelForValue(value);
                                    if (label.length > 12) return label.substr(0, 10) + '...';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    @endif
    
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 4000);
});
</script>
@endpush