@extends('layouts.backend')

@push('styles')
<style>
    .table th, .table td { vertical-align: middle !important; }
    .card-info-nilai { border-left: 5px solid #6777ef; }
    
    /* Styling Action Icon Kecil */
    .btn-action-icon {
        font-size: 0.85rem;
        cursor: pointer;
        transition: transform 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 4px;
    }
    .btn-action-icon:hover {
        transform: scale(1.15);
        background-color: rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-edit mr-2"></i>Manajemen Nilai Siswa</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('nilai.detail', $data->kelas->id) }}">Daftar Siswa</a></div>
                    <div class="breadcrumb-item">Input Nilai</div>
                </div>
            </div>

            {{-- NOTIFIKASI --}}
            @if (session('notif'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                @php
                    // =========================================================================
                    // L O G I K A   K E L E N G K A P A N   (Indikator Global Khusus Guru Ybs)
                    // =========================================================================
                    
                    // PERBAIKAN: Gunakan data mapel dan nilai yang dikirim dari Controller 
                    // (Hanya mapel yang DIAMPU oleh guru yang sedang login di kelas ini)
                    $semuaMapel = $data->mapelDiampu;
                    $semuaNilaiSiswa = $data->nilaiList;

                    $totalMapel = $semuaMapel->count();
                    $mapelLengkapCount = 0;

                    foreach($semuaMapel as $m) {
                        $g = $semuaNilaiSiswa->where('mapel_id', $m->id);
                        $j = $g->pluck('jenis')->map(fn($x) => strtoupper($x))->toArray();
                        // Syarat lengkap per mapel: Punya HARIAN, PTS, dan PAS
                        if (in_array('HARIAN', $j) && in_array('PTS', $j) && in_array('PAS', $j)) {
                            $mapelLengkapCount++;
                        }
                    }

                    // Tentukan status global
                    $isGlobalLengkap = ($totalMapel > 0 && $mapelLengkapCount === $totalMapel);
                @endphp

                <div class="row">
                    {{-- KOLOM KIRI: INFO SISWA & INDIKATOR --}}
                    <div class="col-12 col-lg-4">
                        <div class="card card-info-nilai shadow-sm">
                            <div class="card-header bg-white border-bottom-0 pb-0">
                                <h4><i class="fas fa-user-graduate mr-2 text-primary"></i> Identitas Siswa</h4>
                            </div>
                            <div class="card-body pt-3">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Nama Lengkap:</small>
                                    <h5 class="text-primary font-weight-bold">{{ $data->siswa->nama ?? '-' }}</h5>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">NISN / NIS:</small>
                                    <h6 class="font-weight-bold">{{ $data->siswa->nisn ?? '-' }} / {{ $data->siswa->nis ?? '-' }}</h6>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Kelas / Rombel:</small>
                                    <h6 class="font-weight-bold">Kelas {{ $data->kelas->kelas ?? '-' }} {{ $data->kelas->nama ? '| Fase '.$data->kelas->nama : '' }}</h6>
                                </div>
                                <hr>
                                <div class="mb-0">
                                    <small class="text-muted d-block mb-2">Mata Pelajaran yang Anda Ampu ({{ $totalMapel }}):</small>
                                    <div class="d-flex flex-wrap" style="gap: 5px;">
                                        @forelse($semuaMapel as $m)
                                            <span class="badge badge-light border text-dark shadow-sm">{{ $m->nama }}</span>
                                        @empty
                                            <span class="text-danger small">Anda tidak memiliki jadwal mengajar di kelas ini.</span>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- INDIKATOR KELENGKAPAN GLOBAL --}}
                                @if($totalMapel > 0)
                                <div class="mt-4 border-top pt-3">
                                    <small class="text-muted d-block mb-2 font-weight-bold">Status Pengisian Nilai Anda:</small>
                                    @if($isGlobalLengkap)
                                        <div class="alert alert-success mb-0 py-2 shadow-sm d-flex align-items-center">
                                            <i class="fas fa-check-circle mr-3" style="font-size: 1.8rem;"></i>
                                            <div>
                                                <strong style="font-size: 14px;">Lengkap</strong><br>
                                                <span style="font-size: 11px;">Semua nilai telah diinput ({{ $mapelLengkapCount }}/{{ $totalMapel }} Mapel).</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0 py-2 shadow-sm text-dark d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle mr-3" style="font-size: 1.8rem;"></i>
                                            <div>
                                                <strong style="font-size: 14px;">Belum Lengkap</strong><br>
                                                <span style="font-size: 11px;">Masih ada nilai yang kosong ({{ $mapelLengkapCount }}/{{ $totalMapel }} Lengkap).</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: TABEL REKAPITULASI & INPUT --}}
                    <div class="col-12 col-lg-8">
                        <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                            <div class="card-header bg-white border-bottom pt-4 pb-3">
                                <h4><i class="fas fa-clipboard-check mr-2 text-primary"></i> Tabel Penilaian Per Mata Pelajaran</h4>
                            </div>
                            <div class="card-body p-0">
                                
                                @if($semuaMapel->isEmpty())
                                    <div class="p-5 text-center">
                                        <i class="fas fa-user-lock text-muted mb-3 d-block" style="font-size: 32px;"></i>
                                        <h6 class="text-muted">Akses Terbatas</h6>
                                        <p class="small text-secondary mb-0">Anda hanya dapat melihat dan menginput nilai pada mata pelajaran yang Anda ajarkan di kelas ini.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="bg-light text-center">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th class="text-left">Mata Pelajaran</th>
                                                    <th width="12%">Harian</th>
                                                    <th width="12%">PTS</th>
                                                    <th width="12%">PAS</th>
                                                    <th width="15%">Nilai Akhir</th>
                                                    <th width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($semuaMapel as $mapel)
                                                    @php
                                                        $grades = $semuaNilaiSiswa->where('mapel_id', $mapel->id);
                                                        
                                                        $harian = $grades->where('jenis','HARIAN')->first();
                                                        $pts = $grades->where('jenis','PTS')->first();
                                                        $pas = $grades->where('jenis','PAS')->first();

                                                        // ==============================
// LOGIKA NILAI AKHIR
// ==============================

// Ambil nilai
$vHarian = $harian->nilai ?? null;
$vPts    = $pts->nilai ?? null;
$vPas    = $pas->nilai ?? null;

// Bobot Penilaian
$bobotHarian = 0.60; // 60%
$bobotUjian  = 0.40; // 40%

// Hitung rata-rata ujian (PTS + PAS)
$rataUjian = null;

if (!is_null($vPts) && !is_null($vPas)) {
    $rataUjian = ($vPts + $vPas) / 2;
} elseif (!is_null($vPts)) {
    $rataUjian = $vPts;
} elseif (!is_null($vPas)) {
    $rataUjian = $vPas;
}

// Hitung nilai akhir
$nA = null;

if (!is_null($vHarian) && !is_null($rataUjian)) {

    // Rumus:
    // (Nilai Harian × 60%) + (Rata Ujian × 40%)

    $nA = ($vHarian * $bobotHarian)
        + ($rataUjian * $bobotUjian);

} elseif (!is_null($vHarian)) {

    // Jika hanya nilai harian tersedia
    $nA = $vHarian;

} elseif (!is_null($rataUjian)) {

    // Jika hanya nilai ujian tersedia
    $nA = $rataUjian;
}

// Pembulatan nilai akhir
$nA = !is_null($nA) ? round($nA) : null;
                                                        // Status kelengkapan per mapel
                                                        $isLengkap = !is_null($vHarian) && !is_null($vPts) && !is_null($vPas);
                                                    @endphp
                                                    <tr class="text-center align-middle">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="text-left font-weight-bold text-dark">{{ $mapel->nama }}</td>
                                                        
                                                        {{-- Rata-Rata Harian --}}
                                                        <td>
                                                            @if($harian)
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <span class="font-weight-bold text-dark mb-1 d-block" style="font-size: 1rem;">{{ $harian->nilai }}</span>
                                                                    <div class="d-flex justify-content-center mt-1" style="gap: 5px;">
                                                                        <a href="{{ route('nilai.edit', $harian->uuid) }}" class="text-warning btn-action-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                                                        <form action="{{ route('nilai.destroy', $harian->uuid) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Hapus nilai ini?')">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="border-0 bg-transparent text-danger p-0 m-0 btn-action-icon" title="Hapus"><i class="fas fa-trash"></i></button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted font-italic">-</span>
                                                            @endif
                                                        </td>

                                                        {{-- PTS --}}
                                                        <td>
                                                            @if($pts)
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <span class="font-weight-bold text-dark mb-1 d-block" style="font-size: 1rem;">{{ $pts->nilai }}</span>
                                                                    <div class="d-flex justify-content-center mt-1" style="gap: 5px;">
                                                                        <a href="{{ route('nilai.edit', $pts->uuid) }}" class="text-warning btn-action-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                                                        <form action="{{ route('nilai.destroy', $pts->uuid) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Hapus nilai ini?')">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="border-0 bg-transparent text-danger p-0 m-0 btn-action-icon" title="Hapus"><i class="fas fa-trash"></i></button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted font-italic">-</span>
                                                            @endif
                                                        </td>

                                                        {{-- PAS --}}
                                                        <td>
                                                            @if($pas)
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <span class="font-weight-bold text-dark mb-1 d-block" style="font-size: 1rem;">{{ $pas->nilai }}</span>
                                                                    <div class="d-flex justify-content-center mt-1" style="gap: 5px;">
                                                                        <a href="{{ route('nilai.edit', $pas->uuid) }}" class="text-warning btn-action-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                                                        <form action="{{ route('nilai.destroy', $pas->uuid) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Hapus nilai ini?')">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="border-0 bg-transparent text-danger p-0 m-0 btn-action-icon" title="Hapus"><i class="fas fa-trash"></i></button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted font-italic">-</span>
                                                            @endif
                                                        </td>

                                                        {{-- Nilai Akhir --}}
                                                        <td>
                                                            @if(!is_null($nA))
                                                                <span class="font-weight-bold text-dark" style="font-size: 1.1rem;">{{ $nA }}</span>
                                                            @else
                                                                <span class="text-muted font-italic">-</span>
                                                            @endif
                                                        </td>

                                                        {{-- Kolom Aksi --}}
                                                        <td>
                                                            @if($isLengkap)
                                                                <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Tuntas</span>
                                                            @else
                                                                <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahNilai_{{ $mapel->id }}">
                                                                    <i class="fas fa-plus mr-1"></i> Input
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

{{-- ================= MODAL TAMBAH NILAI (Dibuat Dinamis per Mapel) ================= --}}
@foreach($semuaMapel as $mapel)
    @php
        $grades = $semuaNilaiSiswa->where('mapel_id', $mapel->id);
        $jenisSudahAda = $grades->pluck('jenis')->map(fn($j) => strtoupper($j))->toArray();
        $jenisList = ['HARIAN', 'PTS', 'PAS'];
        $jenisBelumAda = array_diff($jenisList, $jenisSudahAda);
    @endphp

    @if(!empty($jenisBelumAda))
    <div class="modal fade" id="modalTambahNilai_{{ $mapel->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('nilai.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Input Nilai: {{ $mapel->nama }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        {{-- Hidden Data --}}
                        <input type="hidden" name="siswa_id" value="{{ $data->siswa->id }}">
                        <input type="hidden" name="kelas_id" value="{{ $data->kelas->id }}">
                        <input type="hidden" name="mapel_id" value="{{ $mapel->id }}">

                        <div class="alert alert-light border-left-info shadow-sm">
                            Siswa: <strong>{{ $data->siswa->nama }}</strong><br>
                            Mata Pelajaran: <strong>{{ $mapel->nama }}</strong>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Pilih Kategori Nilai <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                @foreach($jenisBelumAda as $j)
                                    <option value="{{ $j }}">
                                        @switch($j)
                                            @case('HARIAN') Rata-Rata Harian @break
                                            @case('PTS') PTS (Penilaian Tengah Semester) @break
                                            @case('PAS') PAS (Penilaian Akhir Semester) @break
                                            @default {{ $j }}
                                        @endswitch
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block">Hanya menampilkan kategori nilai yang belum diinput pada mapel ini.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Skor Nilai (0-100) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-whitesmoke"><i class="fas fa-sort-numeric-up"></i></div>
                                </div>
                                <input type="number" name="nilai" class="form-control font-weight-bold" min="0" max="100" placeholder="Contoh: 85" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-whitesmoke border-top">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-hide alert
        $('.alert').delay(5000).fadeOut(300);
        
        // Fix z-index modal agar tidak terperangkap backdrop
        $('.modal').appendTo("body");
    });
</script>
@endpush