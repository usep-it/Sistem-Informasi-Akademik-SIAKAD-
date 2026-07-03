@extends('layouts.backend')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Agar Select2 tampil rapi di atas modal atau elemen lainnya */
    .select2-container--open { z-index: 9999 !important; }
    .main-content { overflow: visible !important; }
    
    /* Styling khusus untuk card migrasi */
    .card-migration {
        border-radius: 12px;
        transition: all 0.3s;
    }
    .card-migration:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            {{-- HEADER HALAMAN --}}
            <div class="section-header">
                <h1><i class="fas fa-rocket mr-2 text-primary"></i> Pusat Migrasi Data</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Migrasi Data</div>
                </div>
            </div>

            {{-- NOTIFIKASI SUKSES --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm" role="alert">
                    <div class="alert-title font-weight-bold"><i class="fas fa-check-circle mr-2"></i> Berhasil!</div>
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            {{-- NOTIFIKASI ERROR --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="alert-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Terjadi Kesalahan!</div>
                    {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="row">
                    
                    {{-- 📅 CARD 1: IMPORT JADWAL PELAJARAN --}}
                    <div class="col-12 col-lg-5">
                        <div class="card card-primary card-migration shadow-sm border-top-0">
                            <div class="card-header bg-white pb-0 border-bottom-0">
                                <div class="icon-wrapper bg-light-primary">
                                    <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                </div>
                                <h4 class="text-primary">Import Jadwal Ganjil</h4>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-4">Gunakan fitur ini untuk mengunggah jadwal pelajaran semester ganjil secara masal melalui file Excel.</p>
                                
                                <form action="{{ route('jadwal.importGanjil') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label class="font-weight-bold">Target Tahun Ajaran</label>
                                        <select name="tahun_id" class="form-control select2" required>
                                            <option value="">-- Pilih Tahun Ajaran --</option>
                                            @foreach($tahun as $th)
                                                <option value="{{ $th->id }}">TA {{ $th->nama }} - Semester {{ ucfirst($th->semester) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">File Jadwal (.xlsx / .csv)</label>
                                        <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv">
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle mr-1"></i> Format kolom: Hari, Kelas, Mapel, Nama Guru, Jam Mulai, Jam Selesai.
                                        </small>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block shadow-sm font-weight-bold mt-4">
                                        <i class="fas fa-upload mr-2"></i> Jalankan Proses Import
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- 📊 CARD 2: IMPORT NILAI LEGER (E-RAPOR) --}}
                    <div class="col-12 col-lg-7">
                        <div class="card card-success card-migration shadow-sm border-top-0">
                            <div class="card-header bg-white pb-0 border-bottom-0">
                                <div class="icon-wrapper bg-light-success">
                                    <i class="fas fa-file-excel fa-2x text-success"></i>
                                </div>
                                <h4 class="text-success">Sinkronisasi Nilai Leger Ganjil</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info shadow-sm border-0 mb-4" style="background-color: #e3f2fd; color: #0d47a1;">
                                    <i class="fas fa-lightbulb mr-2"></i> 
                                    Fitur ini akan menyalin <strong>Nilai Akhir</strong> dari Leger e-Rapor ke kategori <strong>PAS</strong> di SIAKAD sebagai nilai jadi semester ganjil.
                                </div>

                                <form action="{{ route('nilai.importLeger') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label class="font-weight-bold">Pilih Kelas & Periode</label>
                                            <select name="kelas_id" class="form-control select2" required>
                                                <option value="">-- Pilih Kelas --</option>
                                                @foreach($kelas as $kls)
                                                    {{-- PERBAIKAN: Menggunakan null coalescing (??) agar tidak error jika tahun kosong --}}
                                                    <option value="{{ $kls->id }}">
                                                        Kelas {{ $kls->kelas }} {{ $kls->nama }} - 
                                                        {{ $kls->tahun->nama ?? 'Tahun Tidak Terdeteksi' }} 
                                                        ({{ $kls->tahun->semester ?? '-' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="font-weight-bold">File Leger e-Rapor (.xlsx)</label>
                                            <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv">
                                        </div>
                                    </div>
                                    
                                    <div class="text-right mt-3 border-top pt-4">
                                        <button type="submit" class="btn btn-success px-4 shadow-sm font-weight-bold">
                                            <i class="fas fa-sync-alt mr-2"></i> Mulai Sinkronisasi Nilai
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer bg-whitesmoke">
                                <small><b>Catatan:</b> Pastikan file Leger sesuai dengan template e-Rapor (Data dimulai dari baris ke-8 dan singkatan Mapel di baris ke-5).</small>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 agar pencarian dropdown lebih mudah
        $('.select2').select2({
            width: '100%'
        });

        // Auto-hide alert setelah 7 detik
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 7000);
    });
</script>
@endpush