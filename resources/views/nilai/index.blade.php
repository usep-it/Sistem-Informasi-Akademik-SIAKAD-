@extends('layouts.backend')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        .main-content { overflow: visible !important; }
        .table th, .table td { vertical-align: middle !important; }
        
        /* Kustomisasi Select Tahun Ajaran */
        .filter-tahun .input-group-text {
            background-color: #6777ef;
            color: white;
            border-color: #6777ef;
        }
        
        /* Kustomisasi Badge Siswa */
        .badge-nilai-item {
            font-size: 0.85rem;
            padding: 0.4em 0.6em;
            margin: 2px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            
            {{-- HEADER & BREADCRUMB --}}
            <div class="section-header">
                @if (auth()->user()->role === 'Guru')
                    <h1><i class="fas fa-clipboard-list mr-2"></i>Manajemen Penilaian</h1>
                @else
                    <h1><i class="fas fa-poll mr-2"></i>Hasil Belajar Saya</h1>
                @endif
                
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Nilai</div>
                </div>
            </div>

            {{-- NOTIFIKASI --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
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

            <div class="section-body">
                <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                    {{-- CARD HEADER & FILTER TAHUN --}}
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap pt-4 pb-3">
                        <h4 class="mb-2 mb-md-0 text-primary">
                            @if (auth()->user()->role === 'Guru')
                                <i class="fas fa-school mr-1"></i> Daftar Kelas yang Diampu
                            @else
                                <i class="fas fa-list-alt mr-1"></i> Rekapitulasi Nilai Per Mapel
                            @endif
                        </h4>

                        @if (auth()->user()->role === 'Guru')
                            {{-- TAMPILAN GURU: Hanya Tampilkan Info Tahun Aktif (Tanpa Filter) --}}
                            <div class="mt-2 mt-md-0">
                                <span class="badge badge-primary shadow-sm" style="font-size: 14px; padding: 8px 15px;">
                                    <i class="fas fa-calendar-check mr-1"></i> TA {{ $tahunAktif->nama ?? '-' }} - Semester {{ ucfirst($tahunAktif->semester ?? '-') }}
                                </span>
                            </div>
                        @else
                            {{-- TAMPILAN SISWA: Pertahankan Dropdown Filter Tahun Ajaran --}}
                            <form method="GET" action="{{ route('nilai.index') }}" class="form-inline filter-tahun mt-2 mt-md-0">
                                <div class="input-group input-group-sm shadow-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <select name="tahun_id" class="form-control font-weight-bold" onchange="this.form.submit()" style="min-width: 200px;">
                                        @foreach ($tahun as $th)
                                            <option value="{{ $th->id }}" {{ request('tahun_id', $tahunAktif->id ?? '') == $th->id ? 'selected' : '' }}>
                                                TA {{ $th->nama }} - Smtr {{ ucfirst($th->semester) }} {{ $th->status == 'Aktif' ? '(Aktif)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        @endif
                    </div>

                    <div class="card-body">
                        
                        {{-- ============================ TAMPILAN GURU ============================ --}}
                        @if (auth()->user()->role === 'Guru')
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered dt-responsive nowrap" id="table-nilai" style="width: 100%;">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th class="text-left">Nama Kelas / Fase</th>
                                            <th width="20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kelasDiampu ?? [] as $kls)
                                            <tr class="text-center">
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-left font-weight-bold text-dark" style="font-size: 15px;">
                                                    Kelas {{ $kls->kelas ?? '-' }} {{ $kls->nama ? '| Fase '.$kls->nama : '' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('nilai.detail', $kls->id) }}" class="btn btn-primary btn-sm shadow-sm" title="Lihat Daftar Siswa">
                                                        <i class="fas fa-users mr-1"></i> Lihat Daftar Siswa
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-5">
                                                    <div class="empty-state">
                                                        <div class="empty-state-icon"><i class="fas fa-calendar-times"></i></div>
                                                        <h2>Tidak Ada Kelas</h2>
                                                        <p class="lead">Anda tidak di-assign untuk mengajar di kelas manapun pada semester aktif ini.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        {{-- ============================ TAMPILAN SISWA ============================ --}}
                        @elseif (auth()->user()->role === 'Siswa')
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered dt-responsive nowrap" id="table-nilai" style="width: 100%;">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th class="text-left" width="40%">Mata Pelajaran</th>
                                            <th class="text-left">Rincian Nilai Terinput</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($nilaisiswa ?? [] as $mapel => $nilaiList)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="font-weight-bold text-primary text-left">{{ $mapel }}</td>
                                                <td class="text-left">
                                                    <div class="d-flex flex-wrap">
                                                        @foreach ($nilaiList as $n)
                                                            @php
                                                                $badgeColor = 'badge-light text-dark border';
                                                                if(strtoupper($n->jenis) === 'HARIAN') $badgeColor = 'badge-info';
                                                                elseif(strtoupper($n->jenis) === 'PTS') $badgeColor = 'badge-warning text-dark';
                                                                elseif(strtoupper($n->jenis) === 'PAS') $badgeColor = 'badge-success';
                                                            @endphp
                                                            <span class="badge {{ $badgeColor }} badge-nilai-item">
                                                                {{ $n->jenis }}: <strong>{{ $n->nilai }}</strong>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-5">
                                                    <div class="empty-state">
                                                        <div class="empty-state-icon"><i class="fas fa-clipboard-list"></i></div>
                                                        <h2>Belum Ada Nilai</h2>
                                                        <p class="lead">Belum ada nilai yang diinput oleh Guru untuk semester ini.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        if ($('#table-nilai tbody tr td').length > 1) {
            $('#table-nilai').DataTable({
                responsive: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                columnDefs: [
                    { orderable: false, targets: [0, -1] }
                ]
            });
        }
        $('.alert').delay(5000).fadeOut(300);
    });
</script>
@endpush