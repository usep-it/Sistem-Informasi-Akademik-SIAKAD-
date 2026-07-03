@extends('layouts.backend')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Z-index diturunkan sedikit agar tidak menimpa header/navbar bawaan template secara ekstrem */
    .select2-container--open {
        z-index: 1060 !important; 
    }

    .action-buttons form {
        display: inline-block;
        margin-left: 5px;
    }

    .card-header h4 {
        font-weight: 600;
        color: #34395e;
    }

    .btn i {
        margin-right: 5px;
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">

            {{-- Header --}}
            <div class="section-header">
                <h1>
                    Kelola Anggota Kelas:
                    <span class="text-primary">{{ $kelas->kelas }}</span>
                </h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="{{ route('kelas.index') }}">Kelas</a></div>
                    <div class="breadcrumb-item active">Kelola Anggota</div>
                </div>
            </div>

             {{-- Info Kelas --}}
            <div class="alert alert-light border-left-primary shadow-sm mb-4">
                <strong>Tahun Pelajaran:</strong> {{ $kelas->tahun?->nama ?? '-' }} <br>
                <strong>Semester:</strong> {{ ucfirst($kelas->tahun?->semester ?? '-') }} <br>
                <strong>Wali Kelas:</strong> {{ $kelas->pegawai?->nama ?? '-' }}
            </div>

            {{-- Notifikasi --}}
            @if (session('notif'))
                <div class="alert alert-primary text-center shadow-sm alert-dismissible fade show">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-center shadow-sm alert-dismissible fade show">
                    {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm alert-dismissible fade show">
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

            {{-- Tombol Aksi --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body d-flex flex-wrap justify-content-end align-items-center p-3">
                    <div class="action-buttons">
                        {{-- Keluarkan semua --}}
                        @if ($anggota_kelas->count() > 0)
                        <form action="{{ route('kelas.removeAllMembers', $kelas->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin mengeluarkan semua siswa dari kelas ini? Mereka akan dikembalikan ke daftar tunggu.');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                                <i class="fas fa-users-slash"></i> Keluarkan Semua Anggota
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Konten Utama --}}
            <div class="row">

                {{-- Form Tambah/Pindahkan Siswa --}}
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-white">
                                <i class="fas fa-user-plus"></i> Tambah Anggota
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('kelas.addMember', $kelas->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="font-weight-bold">Pilih Peserta Didik</label>
                                    <select name="siswa_id" class="form-control select2" required>
                                        <option value="">-- Cari Siswa (Belum Punya Kelas) --</option>
                                        @forelse ($calon_anggota as $siswa)
                                            <option value="{{ $siswa->id }}">
                                                {{ $siswa->nisn ?? $siswa->nis }} - {{ $siswa->nama }}
                                            </option>
                                        @empty
                                            <option value="" disabled>Semua siswa aktif sudah memiliki kelas</option>
                                        @endforelse
                                    </select>
                                    <small class="text-muted mt-2 d-block">Siswa yang sudah berada di kelas lain tidak akan muncul di sini.</small>
                                </div>
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary shadow-sm w-100" {{ $calon_anggota->isEmpty() ? 'disabled' : '' }}>
                                        <i class="fas fa-plus-circle"></i> Tambahkan ke Kelas
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Daftar Anggota --}}
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0 text-white">
                                <i class="fas fa-users"></i>
                                Daftar Anggota ({{ $anggota_kelas->count() }} Siswa)
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">No</th>
                                            <th>Nama Siswa</th>
                                            <th class="text-center">NIS / NISN</th>
                                            <th class="text-center" style="width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($anggota_kelas as $siswa)
                                            <tr>
                                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                                <td class="font-weight-bold align-middle">{{ $siswa->nama }}</td>
                                                <td class="text-center align-middle">{{ $siswa->nisn ?? $siswa->nis }}</td>
                                                <td class="text-center align-middle">
                                                    <form action="{{ route('kelas.removeMember', $siswa->id) }}" method="POST"
                                                          onsubmit="return confirm('Keluarkan siswa ini dari kelas? (Siswa akan dikembalikan ke antrean tanpa kelas)');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm" title="Keluarkan dari kelas">
                                                            <i class="fas fa-user-minus"></i> Keluarkan
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-5">
                                                    <i class="fas fa-user-slash fs-1 d-block mb-3" style="font-size: 30px;"></i>
                                                    Belum ada anggota di kelas ini.<br>Silakan tambahkan dari form di samping.
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

        </section>
    </div>
</main>
@endsection

@push('scripts')
{{-- PERBAIKAN: JQUERY CDN DIHAPUS DARI SINI AGAR TIDAK BENTROK DENGAN BAWAAN TEMPLATE --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2').select2({
        placeholder: '-- Cari Siswa --',
        width: '100%',
        language: {
            noResults: function() {
                return "Siswa tidak ditemukan";
            }
        }
    });
    
    // Auto hide alert setelah 4 detik
    $('.alert:not(.alert-light)').delay(4000).fadeOut(500);
});
</script>
@endpush