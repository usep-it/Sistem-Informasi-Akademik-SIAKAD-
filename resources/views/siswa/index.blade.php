@extends('layouts.backend')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        /* Override Stisla untuk fix overflow */
        .main-content {
            overflow: visible !important;
        }
        
        /* Menghindari masalah Modal Backdrop */
        .modal { z-index: 1050 !important; }
        .modal-backdrop { z-index: 1040 !important; }

        /* Styling Toolbar Action (Kanan Atas) */
        .card-header-action-custom {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Styling Box Filter Kelas */
        .filter-wrapper {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 20px;
        }

        /* Styling Modal agar lebih profesional */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .modal-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 15px 25px;
        }

        /* Responsivitas Mobile */
        @media (max-width: 575px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            .card-header-action-custom {
                width: 100%;
                margin-top: 1rem;
            }
            .card-header-action-custom .btn, .card-header-action-custom .dropdown {
                flex: 1;
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-users mr-2"></i> Data Siswa</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Daftar PD</div>
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
                {{-- INFO TAHUN AJARAN --}}
                <div class="alert alert-light border-left-info shadow-sm mb-4">
                     @if($tahunAktif)
                         <i class="fas fa-calendar-check text-info mr-2"></i> 
                         Tahun Ajaran Aktif saat ini: <strong>{{ $tahunAktif->nama }} — Semester {{ ucfirst($tahunAktif->semester) }}</strong>
                     @else
                         <i class="fas fa-exclamation-circle text-warning mr-2"></i> 
                         <span class="text-warning font-weight-bold">Belum ada tahun ajaran aktif. Silakan aktifkan di menu Tahun Pelajaran.</span>
                     @endif
                </div>

                {{-- CARD DATA SISWA --}}
                <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap pt-4 pb-3">
                        <h4 class="text-primary mb-2 mb-md-0"><i class="fas fa-user-graduate mr-2"></i> Daftar Peserta Didik Aktif</h4>
                        
                        <div class="card-header-action-custom">
                            <!-- Opsi Data (Dropdown) -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm font-weight-bold" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-database mr-1"></i> Kelola Data
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                    <a class="dropdown-item has-icon" href="{{ route('siswa.export') }}">
                                        <i class="fas fa-file-excel text-success"></i> Ekspor Excel
                                    </a>
                                    <a class="dropdown-item has-icon" href="#" data-toggle="modal" data-target="#modalImport">
                                        <i class="fas fa-file-import text-info"></i> Import Excel
                                    </a>
                                    <div class="dropdown-divider"></div>
                                </div>
                            </div>
                            <form id="hapus-semua-form" action="{{ route('siswa.destroyAll') }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <!-- Tambah Siswa -->
                            <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalTambahSiswa">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah Siswa
                            </button>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- FILTER KELAS (Wrapper Estetik) --}}
                        <div class="filter-wrapper shadow-sm d-flex align-items-center flex-wrap gap-3">
                            <h6 class="mb-0 text-muted mr-3"><i class="fas fa-filter mr-1"></i> Tampilkan Berdasarkan:</h6>
                            <form method="GET" action="{{ route('siswa.index') }}" class="form-inline m-0">
                                <select name="kelas_id" id="kelas_id" class="form-control form-control-sm mr-2 shadow-sm" onchange="this.form.submit()" style="min-width: 220px; border-radius: 6px;">
                                    <option value="">-- Semua Kelas Aktif --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                            Kelas {{ $k->kelas ?? '?' }} {{ $k->nama ? '| Fase '.$k->nama : '' }}
                                        </option>
                                    @endforeach
                                     <option value="tanpa_kelas" {{ request('kelas_id') == 'tanpa_kelas' ? 'selected' : '' }}>-- Belum Ada Kelas --</option>
                                </select>
                                @if(request('kelas_id'))
                                    <a href="{{ route('siswa.index') }}" class="btn btn-outline-danger btn-sm shadow-sm" title="Reset Filter" style="border-radius: 6px;">
                                        <i class="fas fa-times mr-1"></i> Reset
                                    </a>
                                @endif
                            </form>
                        </div>

                        {{-- TABEL DATA SISWA --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" id="table-siswa" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th class="text-left">Nama Lengkap</th>
                                        <th class="text-left">Tempat, Tgl Lahir</th>
                                        <th class="text-center">L/P</th>
                                        <th class="text-left">NIS / NISN</th>
                                        <th class="text-left">Kelas | Fase</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($siswa as $item)
                                        <tr class="align-middle">
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-left font-weight-bold">{{ $item->nama }}</td>
                                            <td class="text-left">
                                                {{ $item->tempat }}, {{ \Carbon\Carbon::parse($item->ttl)->locale('id')->isoFormat('D MMM Y') }}
                                            </td>
                                            <td class="text-center">
                                                {{ substr($item->jk, 0, 1) ?? '-' }}
                                            </td>
                                            <td class="text-left">
                                                {{ $item->nis ?? '-' }} / {{ $item->nisn ?? '-' }}
                                            </td>
                                            <td class="text-left">
                                                @if($item->kelas)
                                                    Kelas {{ $item->kelas->kelas }} {{ $item->kelas->nama ? '| Fase '.$item->kelas->nama : '' }}
                                                @else
                                                    <span class="text-muted fst-italic">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center" nowrap>
                                                <div class="d-flex justify-content-center" style="gap: 5px;">
                                                    <a href="{{ route('siswa.edit', $item->id) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit Data Siswa">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-info btn-sm shadow-sm" data-toggle="modal" data-target="#modalKeluar{{ $item->id }}" title="Registrasi Keluar (Lulus/Mutasi/Berhenti)">
                                                        <i class="fa fa-sign-out-alt"></i>
                                                    </button>
                                                    <form action="{{ route('siswa.destroy', $item->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('PENTING! Menghapus siswa hanya diperbolehkan jika siswa belum punya nilai DAN statusnya sudah non-aktif (Lulus/Mutasi/Berhenti). Yakin ingin mencoba menghapus?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus Permanen Data Siswa">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon text-muted"><i class="fas fa-users-slash"></i></div>
                                                    <h2>Belum Ada Data Siswa</h2>
                                                    <p class="lead">Tidak ada data siswa aktif yang sesuai dengan filter kelas saat ini.</p>
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

{{-- ===================== MODAL TAMBAH SISWA ===================== --}}
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{ route('siswa.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            {{-- Input hidden untuk deteksi error agar modal terbuka lagi --}}
            <input type="hidden" name="modal" value="tambah">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-user-plus mr-2"></i> Tambah Siswa Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat" class="form-control" value="{{ old('tempat') }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="ttl" class="form-control" value="{{ old('ttl') }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jk" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-Laki" {{ old('jk') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="Perempuan" {{ old('jk') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                     <div class="col-md-4 form-group">
                        <label class="font-weight-bold">NIS <span class="text-danger">*</span></label>
                        <input type="number" name="nis" class="form-control" value="{{ old('nis') }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">NISN <span class="text-danger">*</span></label>
                        <input type="number" name="nisn" class="form-control" value="{{ old('nisn') }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">No HP (Orang Tua/Wali)</label>
                        <input type="number" name="hp" class="form-control" value="{{ old('hp') }}">
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Siswa</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MODAL REGISTRASI KELUAR SISWA ===================== --}}
@foreach ($siswa as $item)
<div class="modal fade" id="modalKeluar{{ $item->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('siswa.keluar', $item->id) }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            {{-- Input hidden untuk deteksi error agar modal terbuka lagi --}}
            <input type="hidden" name="modal_id" value="keluar">
            <input type="hidden" name="item_id" value="{{ $item->id }}">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fa fa-sign-out-alt mr-2"></i> Registrasi Keluar Siswa</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border-left-info shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <span class="mr-3">
                            <i class="fas {{ strtolower(substr($item->jk, 0, 1)) == 'l' ? 'fa-mars text-primary' : 'fa-venus text-danger' }} fa-2x"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 text-dark">{{ $item->nama }}</h6>
                            <small class="text-muted">NISN: {{ $item->nisn ?? '-' }}</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Keluar karena <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Mutasi">Mutasi</option>
                        <option value="Dikeluarkan">Dikeluarkan</option>
                        <option value="Mengundurkan diri">Mengundurkan diri</option>
                        <option value="Putus Sekolah">Putus Sekolah</option>
                        <option value="Wafat">Wafat</option>
                        <option value="Hilang">Hilang</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Tanggal keluar sekolah <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_keluar" class="form-control" required max="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-bold">Alasan keluar</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Mengikuti orang tua pindah tugas"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-info shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Registrasi</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- ===================== MODAL IMPORT ===================== --}}
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            @csrf
            {{-- Input hidden untuk deteksi error agar modal terbuka lagi --}}
            <input type="hidden" name="modal" value="import">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa fa-file-excel mr-2"></i> Import Data Siswa</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold">Pilih File Excel (.xlsx / .csv) <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control-file" required accept=".xlsx, .csv">
                </div>
                
                <div class="alert alert-light mt-4 mb-3 shadow-sm border-left-success">
                    <strong class="d-block mb-2 text-success"><i class="fas fa-info-circle mr-1"></i> Panduan Format Excel:</strong>
                    <ul class="mb-0 text-muted" style="font-size: 13px; line-height: 1.6;">
                        <li>Pastikan kolom sesuai dengan <em>template</em> sistem.</li>
                        <li>Format Tanggal Lahir (wajib): <strong>YYYY-MM-DD</strong> (contoh: 2010-05-15).</li>
                        <li>Jenis Kelamin (wajib): <strong>'Laki-Laki'</strong> atau <strong>'Perempuan'</strong>.</li>
                        <li>Kosongkan ID Kelas jika belum ingin menetapkan kelas.</li>
                    </ul>
                </div>

                <div class="text-center">
                    <a href="{{ route('siswa.template.download') }}" class="btn btn-outline-success btn-sm rounded-pill shadow-sm">
                        <i class="fa fa-download mr-1"></i> Unduh Template Excel
                    </a>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke border-top">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success shadow-sm"><i class="fas fa-upload mr-1"></i> Proses Import</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        // PERBAIKAN 1: Pindahkan semua modal ke body agar tidak terperangkap backdrop abu-abu (Stisla Fix)
        $('.modal').appendTo("body");

        // Inisialisasi DataTables
        if ($('#table-siswa tbody tr td').length > 1) { 
             $('#table-siswa').DataTable({
                responsive: true,
                autoWidth: false,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
                columnDefs: [
                    { orderable: false, targets: [0, 6] },
                    { responsivePriority: 1, targets: 1 }, // Nama
                    { responsivePriority: 2, targets: 5 }, // Kelas
                    { responsivePriority: 3, targets: 6 }  // Aksi
                ]
             });
        }

        // Menutup alert otomatis setelah 5 detik
        $('.alert').delay(5000).fadeOut(300);

        // PERBAIKAN 2: Buka otomatis modal yang bersangkutan jika ada error validasi backend
        @if ($errors->any())
            @if (old('modal') === 'tambah') 
                 $('#modalTambahSiswa').modal('show');
            @elseif (old('modal') === 'import')
                 $('#modalImport').modal('show');
            @elseif (old('modal_id') === 'keluar')
                $('#modalKeluar{{ old('item_id') }}').modal('show');
            @endif
        @endif
    });
</script>
@endpush