@extends('layouts.backend')

@push('styles')
<style>
    /* Efek hover interaktif pada kartu */
    .card-kelas-wali {
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }
    .card-kelas-wali:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(130, 82, 250, 0.15) !important;
    }
    
    .main-content { overflow: visible !important; }

    .icon-box-class {
        width: 50px;
        height: 50px;
        background: rgba(130, 82, 250, 0.1);
        color: var(--siakad-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 15px;
    }

    .badge-status-year {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-users-cog mr-2"></i> Ruang Wali Kelas</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Siswa Saya</div>
                </div>
            </div>

            {{-- NOTIFIKASI --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm" role="alert">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                {{-- Info Periode Saat Ini --}}
                <div class="alert alert-light border-left-primary shadow-sm mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 10px;">Periode Tampilan</small>
                        <strong class="text-primary">TA {{ $tahunPilihan->nama }} &mdash; {{ ucfirst($tahunPilihan->semester) }}</strong>
                    </div>
                    @if ($tahunAktif && $tahunPilihan->id != $tahunAktif->id)
                         <span class="badge badge-warning badge-status-year"><i class="fas fa-archive mr-1"></i> Data Arsip</span>
                    @else
                         <span class="badge badge-success badge-status-year"><i class="fas fa-check-circle mr-1"></i> Tahun Aktif</span>
                    @endif
                </div>

                <div class="row">
                    @forelse ($daftar_kelas as $kelas)
                        <div class="col-12 col-md-6 col-lg-4 d-flex">
                            <div class="card shadow-sm flex-fill card-kelas-wali">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="icon-box-class">
                                            <i class="fas fa-door-open"></i>
                                        </div>
                                        <span class="badge badge-light border">ID: {{ $kelas->id }}</span>
                                    </div>
                                    
                                    <h4 class="text-dark font-weight-bold mb-1">Kelas {{ $kelas->kelas }}</h4>
                                    <p class="text-muted small mb-4">Rombongan Belajar / Kelas Wali</p>

                                    <div class="bg-light p-3 rounded-lg mb-4" style="border-radius: 12px;">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small font-weight-bold">JUMLAH SISWA</span>
                                            <span class="text-primary font-weight-bold">{{ $kelas->siswas_count ?? '0' }} Peserta</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small font-weight-bold">WALI KELAS</span>
                                            <span class="text-dark font-weight-bold small text-truncate ml-2">{{ $kelas->pegawai->nama ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <a href="{{ route('siswa_saya.rekap', $kelas->id) }}" class="btn btn-primary btn-block rounded-pill font-weight-bold shadow-sm">
                                        Lihat Siswa & Nilai <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                                <div class="card-body py-5 text-center">
                                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="100" class="opacity-20 mb-4">
                                    <h5 class="text-muted">Tidak Ada Kelas yang Diampu</h5>
                                    <p class="text-secondary small">Anda tidak tercatat sebagai Wali Kelas pada periode {{ $tahunPilihan->nama }} {{ $tahunPilihan->semester }}.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                
                {{-- <div class="mt-5 text-center">
                    <p class="text-muted small mb-3">Ingin melihat riwayat tahun ajaran sebelumnya?</p>
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                        <i class="fas fa-history mr-1"></i> Buka Pusat Laporan / Arsip
                    </a>
                </div> --}}
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.alert').delay(4000).fadeOut(300);
    });
</script>
@endpush