@extends('layouts.backend')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<style>
    /* Konten utama agar tidak terpotong */
    .main-content { overflow: visible !important; }
    
    /* Styling Tabel Rekap Modern */
    .table-rekap {
        border-collapse: collapse !important;
        background-color: #fff;
    }
    .table-rekap thead th { 
        background-color: #f8f9fa; 
        color: #334155;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        vertical-align: middle !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .table-rekap td {
        vertical-align: middle !important;
        font-size: 0.9rem;
        color: #1e293b;
    }

    /* Badge Nilai Mewah */
    .badge-score {
        padding: 5px 10px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-block;
        min-width: 35px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .score-danger  { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .score-warning { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .score-success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .score-primary { background: #e0e7ff; color: #4f46e5; border: 1px solid #c7d2fe; }

    /* Legend / Keterangan Mapel */
    .legend-box {
        margin-top: 30px;
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .legend-title {
        font-weight: 800;
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    .legend-item {
        font-size: 12px;
        color: #475569;
    }
    .legend-item b { color: var(--siakad-primary); }

    /* Hover effect */
    .table-hover tbody tr:hover { background-color: #f1f5f9 !important; }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class=" "></i> Rekap Nilai Kelas {{ $kelas->kelas }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('siswa_saya.list', $tahunAktif->id) }}">Siswa Saya</a></div>
                    <div class="breadcrumb-item">Rekapitulasi</div>
                </div>
            </div>

            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-header bg-white pt-4 pb-0 border-0 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="text-dark font-weight-bold mb-1">Peringkat & Hasil Belajar Semester</h4>
                    </div>
                    <div class="badge badge-primary px-3 py-2 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-calendar-alt mr-1"></i>Periode: TA {{ $tahunAktif->nama }} &mdash; {{ ucfirst($tahunAktif->semester) }}
                    </div>
                </div>
                
                <div class="card-body">
                    @php
                        $kkm = 60;
                        $averageScores = [];

                        // 1. Hitung Rata-rata per Siswa
                        foreach ($daftar_siswa as $siswa) {
                            $totalSiswa = 0;
                            $countSiswa = 0;

                            foreach ($mapels as $mapel) {
                                $grades = $siswa->nilai->where('mapel_id', $mapel->id);
                                $harian = $grades->where('jenis', 'HARIAN')->avg('nilai');
                                $pts = $grades->where('jenis', 'PTS')->first()?->nilai;
                                $pas = $grades->where('jenis', 'PAS')->first()?->nilai;

                                $nA = null;
                                $fmtf = !is_null($harian) ? $harian : null;
                                $avgSmtf = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();

                                if (!is_null($fmtf) && !is_null($avgSmtf)) $nA = ($fmtf * 0.6) + ($avgSmtf * 0.4);
                                elseif (!is_null($fmtf)) $nA = $fmtf;
                                elseif (!is_null($avgSmtf)) $nA = $avgSmtf;

                                if (!is_null($nA)) {
                                    $totalSiswa += $nA;
                                    $countSiswa++;
                                }
                            }
                            $averageScores[$siswa->id] = $countSiswa > 0 ? ($totalSiswa / $countSiswa) : 0;
                        }

                        // 2. Tentukan Ranking (hanya yang skornya > 0)
                        $ranked = collect($averageScores)->filter(fn($v) => $v > 0)->sortDesc();
                        $ranks = $ranked->keys()->flip();

                        // 3. Urutkan tampilan berdasarkan Rata-rata tertinggi agar Juara 1 di atas
                        $sortedSiswa = $daftar_siswa->sortByDesc(fn($s) => $averageScores[$s->id] ?? 0);
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-rekap" id="table-rekap-nilai">
                            <thead class="text-center">
                                <tr>
                                    <th width="40px">No</th>
                                    <th class="text-left" style="min-width: 200px;">Nama Peserta Didik</th>
                                    @foreach($mapels as $mapel)
                                        {{-- MENGGUNAKAN SINGKATAN MAPEL AGAR RAPI --}}
                                        <th title="{{ $mapel->nama }}">{{ $mapel->singkatan ?? $mapel->nama }}</th>
                                    @endforeach
                                    <th width="70px" class="bg-light">Rata²</th>
                                    <th width="60px" class="bg-light">Rank</th>
                                    <th width="50px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sortedSiswa as $siswa)
                                    @php
                                        $rata = $averageScores[$siswa->id] ?? 0;
                                        $rank = isset($ranks[$siswa->id]) ? $ranks[$siswa->id] + 1 : '-';
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold text-dark">{{ $siswa->nama }}</td>

                                        @foreach($mapels as $mapel)
                                            @php
                                                $grades = $siswa->nilai->where('mapel_id', $mapel->id);
                                                $harian = $grades->where('jenis', 'HARIAN')->avg('nilai');
                                                $pts = $grades->where('jenis', 'PTS')->first()?->nilai;
                                                $pas = $grades->where('jenis', 'PAS')->first()?->nilai;

                                                $nA = null;
                                                $fmtf = !is_null($harian) ? $harian : null;
                                                $avgSmtf = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();

                                                if (!is_null($fmtf) && !is_null($avgSmtf)) $nA = ($fmtf * 0.6) + ($avgSmtf * 0.4);
                                                elseif (!is_null($fmtf)) $nA = $fmtf;
                                                elseif (!is_null($avgSmtf)) $nA = $avgSmtf;

                                                $nABulat = !is_null($nA) ? round($nA) : null;
                                                
                                                $styleClass = '';
                                                if($nABulat) {
                                                    if($nABulat < $kkm) $styleClass = 'score-danger';
                                                    elseif($nABulat < 70) $styleClass = 'score-warning';
                                                    elseif($nABulat < 85) $styleClass = 'score-success';
                                                    else $styleClass = 'score-primary';
                                                }
                                            @endphp
                                            <td class="text-center">
                                                @if($nABulat)
                                                    <span class="badge-score {{ $styleClass }}">{{ $nABulat }}</span>
                                                @else
                                                    <span class="text-muted opacity-50">&bull;</span>
                                                @endif
                                            </td>
                                        @endforeach

                                        <td class="text-center font-weight-bold text-primary bg-light">
                                            {{ $rata > 0 ? number_format($rata, 1) : '-' }}
                                        </td>
                                        <td class="text-center bg-light">
                                            @if($rank !== '-')
                                                @if($rank <= 3)
                                                    <span class="badge badge-warning font-weight-bold"><i class="fas fa-medal mr-1"></i>{{ $rank }}</span>
                                                @else
                                                    <span class="badge badge-dark" style="border-radius:8px;">{{ $rank }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted opacity-50">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('siswa_saya.nilai', $siswa->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               style="border-radius: 8px;"
                                               title="Detail Nilai">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 5 + $mapels->count() }}" class="text-center py-5">
                                            <div class="empty-state">
                                                <div class="empty-state-icon bg-light text-muted"><i class="fas fa-folder-open"></i></div>
                                                <h6 class="mt-3">Belum ada data nilai yang diinput.</h6>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- LEGEND KETERANGAN MAPEL --}}
                    @if($mapels->isNotEmpty())
                    <div class="legend-box animate-in">
                        <div class="legend-title">
                            <i class="fas fa-info-circle"></i> Keterangan Mata Pelajaran
                        </div>
                        <div class="legend-grid">
                            @foreach($mapels as $m)
                                <div class="legend-item">
                                    <b>{{ $m->singkatan ?? $m->nama }}</b>: {{ $m->nama }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function(){
        $('#table-rekap-nilai').DataTable({
            paging: false,
            searching: true,
            info: false,
            ordering: false,
            language: {
                search: "",
                searchPlaceholder: "Cari Nama Siswa...",
                zeroRecords: "Siswa tidak ditemukan"
            }
        });
        
        // Mempercantik input pencarian DataTables
        $('.dataTables_filter input').addClass('form-control-sm').css({
            'border-radius': '10px',
            'padding': '10px 15px',
            'width': '250px',
            'border': '1px solid #e2e8f0'
        });
    });
</script>
@endpush