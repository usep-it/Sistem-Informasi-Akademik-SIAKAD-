@extends('layouts.backend')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <style>
        .main-content { overflow: visible !important; }
        .table th, .table td { vertical-align: middle !important; }
        #table-siswa { width: 100% !important; }
    </style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            
            <div class="section-header">
                <h1><i class="fas fa-users mr-2"></i>Daftar Peserta Didik</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('nilai.index') }}">Penilaian</a></div>
                    <div class="breadcrumb-item active">Daftar Siswa</div>
                </div>
            </div>

            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">

                            {{-- INFO KELAS & MAPEL YANG DIAMPU --}}
                            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap pt-4 pb-3">
                                <div>
                                    <h4 class="text-primary mb-1">
                                        Kelas: {{ $kelas->kelas ?? '-' }} {{ $kelas->nama ?? '' }}
                                    </h4>
                                    <p class="mb-0 text-muted">
                                        <i class="fas fa-book mr-1"></i> Mapel yang Anda ajar di kelas ini: 
                                        <strong>{{ $mapelDiampu->pluck('nama')->implode(', ') ?: 'Tidak ada' }}</strong>
                                    </p>
                                </div>
                                <div class="mt-2 mt-md-0">
                                    <a href="{{ route('nilai.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Kelas
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="alert alert-info shadow-sm mb-4">
                                    <i class="fas fa-lightbulb mr-2"></i> 
                                    Klik tombol <strong>"Input Nilai"</strong> untuk mengelola nilai siswa tersebut. Anda akan diminta memilih mapel di halaman berikutnya.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered nowrap" id="table-siswa">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th class="text-left">Nama Peserta Didik</th>
                                                <th>NISN</th>
                                                <th>L/P</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($siswaList as $s)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="font-weight-bold text-dark text-left">{{ $s->nama }}</td>
                                                    <td class="text-center">{{ $s->nisn ?? '-' }}</td>
                                                    <td class="text-center">{{ substr($s->jk, 0, 1) }}</td>
                                                    <td class="text-center" nowrap>
                                                        <a href="{{ route('nilai.langsung', ['siswa_id' => $s->id]) }}" 
                                                           class="btn btn-primary btn-sm shadow-sm" title="Input/Kelola Nilai Siswa">
                                                            <i class="fa fa-edit mr-1"></i> Input Nilai
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5">
                                                        <div class="empty-state">
                                                            <div class="empty-state-icon"><i class="fas fa-user-slash"></i></div>
                                                            <h2>Belum Ada Siswa</h2>
                                                            <p class="lead">Tidak ada peserta didik aktif di kelas ini.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
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
<script>
    $(document).ready(function() {
        if ($('#table-siswa tbody tr td').length > 1) {
            $('#table-siswa').DataTable({
                scrollX: true, 
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                columnDefs: [
                    { orderable: false, targets: [0, 4] }
                ]
            });
        }
        $('.alert').delay(5000).fadeOut(300);
    });
</script>
@endpush