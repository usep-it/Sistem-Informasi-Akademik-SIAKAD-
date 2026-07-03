@extends('layouts.backend')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* PERBAIKAN 1: Masalah z-index Select2 di dalam Modal */
    .select2-container--open {
        z-index: 9999999 !important;
    }

    /* PERBAIKAN 2: Masalah scroll/konten terpotong oleh layout Stisla */
    .main-content {
        overflow: visible !important;
    }

    /* Style tambahan untuk Select2 di modal */
    #modalKelas .select2-container .select2-selection--single {
        height: calc(1.5em + .75rem + 2px); 
    }
     #modalKelas .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + .75rem); 
        padding-left: .75rem;
    }
     #modalKelas .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem); 
    }

    /* ==== FIX RESPONSIVITAS HEADER (Tahun Aktif, Lanjut Semester, Tambah) ==== */
    .header-action-wrapper {
        display: flex;
        align-items: center;
        gap: 8px; /* Jarak antar tombol */
        flex-wrap: wrap;
    }
    .header-action-wrapper .badge {
        font-size: 0.85rem;
        padding: 8px 15px;
    }

    /* Tampilan khusus layar HP (Mobile) */
    @media (max-width: 767.98px) {
        .card-header {
            flex-direction: column !important;
            align-items: flex-start !important;
            padding-bottom: 15px;
        }
        .header-action-wrapper {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            margin-top: 15px;
            gap: 10px;
        }
        .header-action-wrapper .badge,
        .header-action-wrapper form,
        .header-action-wrapper form button,
        .header-action-wrapper > button {
            width: 100%; /* Tombol full width di HP */
            margin: 0 !important;
            text-align: center;
        }
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
@endpush

@section('content')
<main id="halaman-kelas">
    <div class="main-content">
        <section class="section">

            {{-- Header --}}
            <div class="section-header">
                <h1>Manajemen Kelas</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('home') }}">Dashboard</a>
                    </div>
                    <div class="breadcrumb-item">Kelas</div>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm" role="alert">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                 <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Error validasi --}}
            @if ($errors->any())
                 <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <p><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
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

            {{-- Konten utama --}}
            <div class="section-body">
                <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                    
                    {{-- Header Tabel & Tombol Aksi --}}
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap pt-4 pb-3">
                        <h4 class="mb-0 text-primary"><i class="fas fa-school mr-2"></i>Daftar Kelas</h4>
                        
                        {{-- Wrapper Responsif untuk Tombol-tombol --}}
                        <div class="header-action-wrapper">
                            @if ($tahunAktif)
                                <span class="badge badge-success shadow-sm">
                                    Aktif: {{ $tahunAktif->nama }} - {{ ucfirst($tahunAktif->semester) }}
                                </span>

                                @php
                                    $isGanjil = strtolower($tahunAktif->semester) === 'ganjil';
                                    $nextStepText = $isGanjil ? 'Lanjut Semester' : 'Naik Tahun Ajaran';
                                    $confirmText = $isGanjil ? 'Semester Genap' : 'Tahun Ajaran Baru';
                                    $btnClass = $isGanjil ? 'btn-info' : 'btn-warning';
                                @endphp
                                <form action="{{ route('kelas.gantiSemester') }}" method="POST" class="m-0"
                                      onsubmit="return confirm('Yakin ingin {{ strtolower($nextStepText) }}? Pastikan data untuk {{ $confirmText }} sudah disiapkan.');">
                                    @csrf
                                    <button type="submit" class="btn {{ $btnClass }} btn-sm shadow-sm">
                                        <i class="fas {{ $isGanjil ? 'fa-arrow-right' : 'fa-level-up-alt' }}"></i> {{ $nextStepText }}
                                    </button>
                                </form>
                            @else
                                <span class="badge badge-warning shadow-sm">Belum ada tahun aktif</span>
                            @endif

                            <button type="button" class="btn btn-primary btn-sm btn-add shadow-sm" data-toggle="modal" data-target="#modalKelas">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah Kelas
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th>Kelas</th>
                                        <th>Fase</th>
                                        <th class="text-left">Wali Kelas</th>
                                        <th>Jml. Siswa</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kls as $item)
                                        <tr class="text-center align-middle">
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="font-weight-bold">{{ $item->kelas }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td class="text-left">{{ $item->pegawai?->nama ?? '-' }}</td>
                                            <td><span class="badge badge-primary">{{ $item->siswas_count }}</span></td>
                                            <td nowrap>
                                                <div class="d-flex justify-content-center" style="gap: 5px;">
                                                    {{-- Tombol Luluskan Semua Siswa (Kelas 6 Semester Genap) --}}
                                                    @if(strtolower($item->tahun?->semester) == 'genap' 
                                                        && (int)filter_var($item->kelas, FILTER_SANITIZE_NUMBER_INT) == 6 
                                                        && $item->siswas_count > 0)
                                                        <button type="button" 
                                                                class="btn btn-success btn-sm btn-luluskan shadow-sm"
                                                                data-id="{{ $item->id }}"
                                                                data-toggle="modal" 
                                                                data-target="#modalLuluskan"
                                                                title="Luluskan Semua Siswa Kelas Ini">
                                                            <i class="fas fa-graduation-cap"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <a href="{{ route('kelas.manage', $item->id) }}" 
                                                       class="btn btn-info btn-sm shadow-sm" title="Kelola Anggota Kelas">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('kelas.edit', $item->id) }}" 
                                                       class="btn btn-warning btn-sm shadow-sm" title="Edit Kelas">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('kelas.destroy', $item->id) }}" 
                                                          method="POST" class="d-inline m-0" 
                                                          onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus Kelas">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-4">Belum ada data kelas untuk tahun ajaran aktif.</td></tr>
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

{{-- ============================ MODAL TAMBAH KELAS ============================ --}}
<div class="modal fade" id="modalKelas" tabindex="-1" role="dialog" aria-labelledby="modalKelasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <form id="formKelas" method="POST" action="{{ route('kelas.store') }}">
                @csrf
                <input type="hidden" name="modal_form" value="tambah">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalKelasLabel"><i class="fas fa-plus-circle mr-2"></i>Tambah Rombongan Belajar Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="font-weight-bold">Tingkat Kelas <span class="text-danger">*</span></label>
                                <select name="kelas" class="form-control @error('kelas') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Tingkat --</option>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ old('kelas') == $i ? 'selected' : '' }}>Kelas {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Fase <span class="text-danger">*</span></label>
                                <select name="nama" class="form-control @error('nama') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Fase --</option>
                                    <option value="A" {{ old('nama') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('nama') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C" {{ old('nama') == 'C' ? 'selected' : '' }}>C</option>
                                </select>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Wali Kelas <span class="text-danger">*</span></label>
                        <select name="pegawai_id" class="form-control select2 @error('pegawai_id') is-invalid @enderror" required style="width: 100%;" data-placeholder="-- Pilih Wali Kelas --">
                            <option value="">-- Pilih Wali Kelas --</option>
                            @foreach ($wali as $wl)
                                <option value="{{ $wl->id }}" {{ old('pegawai_id') == $wl->id ? 'selected' : '' }}>{{ $wl->nama }} {{ $wl->nip ? '('.$wl->nip.')' : '' }}</option>
                            @endforeach
                        </select>
                         @error('pegawai_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Tahun Pelajaran & Semester</label>
                        @if ($tahunAktif)
                            <input type="hidden" name="tahun_id" value="{{ $tahunAktif->id }}">
                            <input type="text" class="form-control bg-whitesmoke text-primary font-weight-bold" 
                                   value="{{ $tahunAktif->nama }} - Semester {{ ucfirst($tahunAktif->semester) }}" 
                                   readonly>
                        @else
                             <div class="alert alert-warning mb-0 shadow-sm font-weight-bold">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Tidak ada tahun ajaran aktif.
                             </div>
                        @endif
                         @error('tahun_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="modal-footer bg-whitesmoke border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary shadow-sm" {{ !$tahunAktif ? 'disabled' : '' }}><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================== MODAL LULUSKAN KELAS ======================== --}}
<div class="modal fade" id="modalLuluskan" tabindex="-1" role="dialog" aria-labelledby="modalLuluskanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <form id="formLuluskanSemua" method="POST" action="">
                @csrf
                 <input type="hidden" name="modal_form" value="luluskan">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalLuluskanLabel"><i class="fas fa-graduation-cap mr-2"></i>Penetapan Kelulusan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-0">
                        <label for="tanggal_kelulusan" class="font-weight-bold">Tanggal Kelulusan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_kelulusan" id="tanggal_kelulusan" class="form-control @error('tanggal_kelulusan') is-invalid @enderror" value="{{ old('tanggal_kelulusan') }}" required max="{{ date('Y-m-d') }}">
                         @error('tanggal_kelulusan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle mr-1"></i> Pastikan tanggal kelulusan sesuai dengan SK Kelulusan.</small>
                </div>
                <div class="modal-footer bg-whitesmoke border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success shadow-sm"><i class="fas fa-check mr-1"></i> Luluskan Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Pindahkan modal ke body (Mencegah terjebak backdrop)
    $('.modal').appendTo("body");

    // Inisialisasi Select2 di dalam modal tambah
     $('#modalKelas .select2').select2({
        dropdownParent: $('#modalKelas'),
        width: '100%' 
     });

    // Buka modal yang sesuai jika ada error validasi
    @if ($errors->any())
         @if (old('modal_form') === 'tambah')
             $('#modalKelas').modal('show');
         @elseif (old('modal_form') === 'luluskan')
             $('#modalLuluskan').modal('show');
             let kelasIdOnError = "{{ old('kelas_id_error') }}"; 
             if(kelasIdOnError) {
                 let actionUrl = "{{ url('kelas') }}/" + kelasIdOnError + "/luluskan-semua";
                 $('#formLuluskanSemua').attr('action', actionUrl);
             }
         @endif
    @endif

    // Saat klik tombol luluskan, set action form sesuai id kelas
    $('.btn-luluskan').click(function() {
        let kelasId = $(this).data('id');
        let actionUrl = "{{ url('kelas') }}/" + kelasId + "/luluskan-semua";
        $('#formLuluskanSemua').attr('action', actionUrl);
        $('#tanggal_kelulusan').val('');
        $('#formLuluskanSemua').append('<input type="hidden" name="kelas_id_error" id="kelas_id_error_input" value="' + kelasId + '">');
    });

     // Hapus input hidden kelas_id_error saat modal luluskan ditutup
     $('#modalLuluskan').on('hidden.bs.modal', function () {
         $('#kelas_id_error_input').remove();
     });

     // Script auto-hide alert
     $('.alert').delay(5000).fadeOut(300);
});
</script>
@endpush