@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-calendar-check mr-2"></i> Tahun Pelajaran</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Tahun Pelajaran</div>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            {{-- Error Validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h4 class="text-primary">Daftar Periode Akademik</h4>
                        <a href="#" data-toggle="modal" data-target="#modalTambah" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Tahun
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered" id="table-1">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th width="6%">No</th>
                                        <th>Tahun Pelajaran</th>
                                        <th>Semester</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $tahunAktif = $tahun->where('status', 'Aktif')->first();
                                        $namaAktif = $tahunAktif ? $tahunAktif->nama : '';
                                    @endphp
                                    @forelse ($tahun as $item)
                                        @php
                                            // Tentukan apakah tahun ini sudah berlalu (arsip lama)
                                            // Logika: Jika bukan aktif DAN nama tahunnya secara abjad lebih kecil dari tahun aktif saat ini
                                            $isBerlalu = ($item->status !== 'Aktif' && $namaAktif && $item->nama < $namaAktif);
                                        @endphp
                                        <tr class="{{ $item->status == 'Aktif' ? 'table-info' : '' }}">
                                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                            <td class="align-middle font-weight-bold">{{ $item->nama }}</td>
                                            <td class="align-middle">{{ $item->semester ?? '-' }}</td>
                                            <td class="text-center align-middle">
                                                @if ($item->status == 'Aktif')
                                                    <span class="badge badge-success shadow-sm"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                                                @elseif($isBerlalu)
                                                    <span class="badge badge-light border text-muted"><i class="fas fa-archive mr-1"></i> Arsip Lampau</span>
                                                @else
                                                    <span class="badge badge-secondary shadow-sm">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle" nowrap>
                                                
                                                @if($isBerlalu)
                                                    {{-- Jika Tahun Sudah Berlalu: Kunci Aksi --}}
                                                    <span class="text-muted small italic"><i class="fas fa-lock mr-1"></i> Data Terkunci</span>
                                                @else
                                                    {{-- Tombol Aktifkan (Hanya muncul jika tidak aktif dan bukan masa lalu) --}}
                                                    @if ($item->status != 'Aktif')
                                                        <form action="{{ route('tahun.toggleStatus', $item->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm shadow-sm" title="Aktifkan Periode Ini" onclick="return confirm('Mengaktifkan tahun ini akan menonaktifkan tahun lainnya secara otomatis. Lanjutkan?')">
                                                                <i class="fas fa-power-off"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Tombol Edit --}}
                                                    <a href="#" data-toggle="modal" data-target="#modalEdit{{ $item->id }}" class="btn btn-warning btn-sm shadow-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    {{-- Tombol Hapus (Hanya muncul jika tidak aktif) --}}
                                                    @if ($item->status != 'Aktif')
                                                        <form action="{{ route('tahun.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4">Belum ada data tahun pelajaran.</td></tr>
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

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-plus mr-2"></i>Tambah Tahun Pelajaran</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('tahun.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Tahun Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" placeholder="Contoh: 2025/2026" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
@foreach ($tahun as $item)
    @php
        $namaAktif = $tahun->where('status', 'Aktif')->first()?->nama;
        $isBerlalu = ($item->status !== 'Aktif' && $namaAktif && $item->nama < $namaAktif);
    @endphp
    
    @if(!$isBerlalu)
    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Tahun Pelajaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('tahun.update', $item->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Tahun Pelajaran</label>
                            <input type="text" class="form-control" name="nama" value="{{ $item->nama }}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Semester</label>
                            <select name="semester" class="form-control" required>
                                <option value="Ganjil" {{ $item->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ $item->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-whitesmoke border-top">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning shadow-sm"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
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
        $('.alert').delay(5000).fadeOut(300);
    });
</script>
@endpush