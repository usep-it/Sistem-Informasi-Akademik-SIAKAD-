@extends('layouts.backend')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        /* Override Stisla untuk fix overflow */
        .main-content {
            overflow: visible !important;
        }
        
        /* Styling Toolbar Action (Kanan Atas) */
        .card-header-action-custom {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-left: auto;
        }

        /* Styling Input Pencarian */
        .search-wrapper .input-group-text, 
        .search-wrapper .form-control, 
        .search-wrapper .btn {
            border-radius: 6px;
        }

        /* Ukuran badge agar seragam */
        .badge-status {
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Responsivitas Mobile */
        @media (max-width: 575px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
                padding-bottom: 15px;
            }
            .card-header-action-custom {
                width: 100%;
                margin-top: 1rem;
            }
            .card-header-action-custom .search-wrapper {
                width: 100%;
                max-width: 100% !important;
            }
        }

        /* Styling Filter Tabs / Pills */
        .nav-pills-custom .nav-link {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            color: #6c757d;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            margin-right: 10px;
            margin-bottom: 10px;
            transition: all 0.3s;
            font-size: 13px;
        }
        .nav-pills-custom .nav-link.active {
            background-color: #6777ef;
            color: #fff;
            border-color: #6777ef;
            box-shadow: 0 4px 8px rgba(103, 119, 239, 0.3);
        }
        .nav-pills-custom .nav-link:hover:not(.active) {
            background-color: #e2e6ea;
            color: #6777ef;
        }
    </style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            {{-- HEADER & BREADCRUMB --}}
            <div class="section-header">
                <h1><i class="fas fa-user-minus mr-2"></i> Riwayat Siswa Keluar</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('siswa.index') }}">Data Siswa</a></div>
                    <div class="breadcrumb-item active">Siswa Keluar</div>
                </div>
            </div>

            {{-- NOTIFIKASI --}}
            @if (session('notif'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                 <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <p class="font-weight-bold mb-1"><i class="fas fa-times-circle mr-1"></i> Oops! Terjadi kesalahan:</p>
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
                {{-- CARD DATA SISWA KELUAR --}}
                <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap pt-4 pb-3">
                        <h4 class="text-primary mb-2 mb-md-0"><i class="fas fa-history mr-2"></i> Daftar Riwayat Siswa Keluar</h4>
                        
                        <div class="card-header-action-custom">
                            {{-- Input Pencarian Kustom --}}
                            <div class="input-group shadow-sm search-wrapper" style="max-width: 300px;">
                                <input type="text" id="search-input" class="form-control" placeholder="Cari nama atau NISN...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- FILTER STATUS (TABS) --}}
                        <ul class="nav nav-pills nav-pills-custom mb-4 border-bottom pb-3" id="pills-tab-status">
                            <li class="nav-item">
                                <a class="nav-link active filter-status shadow-sm" href="#" data-status="">
                                    <i class="fas fa-border-all mr-1"></i> Semua
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-status shadow-sm" href="#" data-status="Lulus">
                                    <i class="fas fa-graduation-cap mr-1"></i> Lulus
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-status shadow-sm" href="#" data-status="Mutasi">
                                    <i class="fas fa-exchange-alt mr-1"></i> Mutasi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-status shadow-sm" href="#" data-status="Dikeluarkan">
                                    <i class="fas fa-user-times mr-1"></i> Dikeluarkan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-status shadow-sm" href="#" data-status="Mengundurkan diri">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Mengundurkan diri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-status shadow-sm" href="#" data-status="Putus Sekolah">
                                    <i class="fas fa-unlink mr-1"></i> Putus Sekolah
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-status shadow-sm" href="#" data-status="Wafat">
                                    <i class="fas fa-book-dead mr-1"></i> Wafat
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link filter-status shadow-sm" href="#" data-status="Hilang">
                                    <i class="fas fa-question-circle mr-1"></i> Hilang
                                </a>
                            </li>
                        </ul>

                        {{-- TABEL DATA --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" id="table-siswa-keluar" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th class="text-left">Nama Lengkap</th>
                                        <th class="text-left">NISN</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-left">Tanggal Keluar</th>
                                        <th class="text-left">Keterangan</th>
                                        <th width="15%" class="text-center">Aksi</th> {{-- Tambahan Kolom Aksi --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($siswa_keluar as $index => $item)
                                        <tr class="align-middle">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-left font-weight-bold text-dark">
                                                {{ $item->siswa->nama ?? 'Siswa (Data Dihapus)' }}
                                            </td>
                                            <td class="text-left">{{ $item->siswa->nisn ?? '-' }}</td>
                                            <td class="text-center">
                                                @switch($item->alasan_keluar)
                                                    @case('Lulus')
                                                        <span class="badge badge-success badge-status shadow-sm">Lulus</span>
                                                        @break
                                                    @case('Mutasi')
                                                        <span class="badge badge-warning text-dark badge-status shadow-sm">Mutasi</span>
                                                        @break
                                                    @case('Dikeluarkan')
                                                    @case('Putus Sekolah')
                                                        <span class="badge badge-danger badge-status shadow-sm">{{ $item->alasan_keluar }}</span>
                                                        @break
                                                    @case('Wafat')
                                                    @case('Hilang')
                                                        <span class="badge badge-dark badge-status shadow-sm">{{ $item->alasan_keluar }}</span>
                                                        @break
                                                    @case('Mengundurkan diri')
                                                        <span class="badge badge-secondary badge-status shadow-sm">{{ $item->alasan_keluar }}</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-light border badge-status shadow-sm">{{ $item->alasan_keluar }}</span>
                                                @endswitch
                                            </td>
                                            <td class="text-left">
                                                 {{ \Carbon\Carbon::parse($item->tanggal_keluar)->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </td>
                                            <td class="text-left text-muted">
                                                {{ $item->keterangan ?? '-' }}
                                            </td>
                                            <td class="text-center" nowrap>
                                                {{-- Tombol Aksi Khusus Lulus --}}
                                                @if($item->alasan_keluar === 'Lulus' && $item->siswa)
                                                    <button class="btn btn-sm btn-info shadow-sm" data-toggle="modal" data-target="#modalSkl{{ $item->siswa->id }}" title="Upload / Edit Link SKL">
                                                        <i class="fas fa-link"></i> SKL
                                                    </button>
                                                    @if($item->siswa->link_skl)
                                                        <a href="{{ $item->siswa->link_skl }}" target="_blank" class="btn btn-sm btn-success shadow-sm ml-1" title="Lihat/Buka Dokumen SKL">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="empty-state">
                                                     <div class="empty-state-icon text-muted">
                                                         <i class="fas fa-user-slash"></i>
                                                     </div>
                                                     <h2>Belum Ada Data Riwayat</h2>
                                                     <p class="lead">Tidak ada data siswa yang tercatat di sistem.</p>
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
        </section>
    </div>
</main>

{{-- ===================== MODAL INPUT SKL ===================== --}}
@foreach ($siswa_keluar as $item)
    @if($item->alasan_keluar === 'Lulus' && $item->siswa)
    <div class="modal fade" id="modalSkl{{ $item->siswa->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="{{ route('siswa.updateSkl', $item->siswa->id) }}" method="POST" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-file-alt mr-2"></i> Input Link Surat Keterangan Lulus</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border-left-info shadow-sm mb-4">
                        <strong class="text-dark">{{ $item->siswa->nama }}</strong><br>
                        <small class="text-muted">NISN: {{ $item->siswa->nisn ?? '-' }}</small>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Link Google Drive (File SKL) <span class="text-danger">*</span></label>
                        <input type="url" name="link_skl" class="form-control" value="{{ $item->siswa->link_skl }}" placeholder="https://drive.google.com/..." required>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle text-info"></i> Pastikan akses link Google Drive diatur ke "Siapa saja yang memiliki link (Viewer)".
                        </small>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Link</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

@endsection

@push('scripts')
{{-- Muat script datatables & responsive --}}
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        // Pindahkan modal ke body untuk mencegah z-index error
        $('.modal').appendTo("body");

        // Inisialisasi DataTables
        if ($('#table-siswa-keluar tbody tr td').length > 1 && !$($('#table-siswa-keluar tbody tr td')[0]).attr('colspan')) { 
             const dataTable = $('#table-siswa-keluar').DataTable({
                responsive: true,
                autoWidth: false,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
                columnDefs: [
                    { orderable: false, targets: [0, 6] }, 
                    { responsivePriority: 1, targets: 1 }, // Nama
                    { responsivePriority: 2, targets: 6 }, // Aksi SKL
                    { responsivePriority: 3, targets: 3 }  // Status
                ],
                // Gunakan pencarian DataTables kustom, sembunyikan fitur filter bawaan
                 dom: 'rt<"d-flex justify-content-between"<"d-flex align-items-center"li><"mt-3"p>>', 
                 lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                 pageLength: 10
             });

             // Hubungkan input search custom ke DataTables
             $('#search-input').on('keyup', function() {
                dataTable.search(this.value).draw();
             });

             // Fitur Filter Status dengan Tab Nav Pills
             $('.filter-status').on('click', function(e) {
                 e.preventDefault();
                 // 1. Ubah tampilan tab aktif
                 $('.filter-status').removeClass('active');
                 $(this).addClass('active');

                 // 2. Ambil nilai status dari atribut data-status
                 let status = $(this).data('status');
                 
                 // 3. Filter kolom ke-3 (Index 3 adalah kolom "Status" di tabel)
                 // Menggunakan Regex '^' dan '$' agar pencocokan teks tepat 100%
                 if(status === '') {
                     dataTable.column(3).search('').draw(); // Tampilkan semua
                 } else {
                     dataTable.column(3).search('^' + status + '$', true, false).draw(); // Tampilkan spesifik
                 }
             });
        }

        // Menutup alert otomatis setelah 5 detik
        $('.alert').delay(5000).fadeOut(300);
    });
</script>
@endpush