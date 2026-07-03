@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Mata Pelajaran</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Mata Pelajaran</div>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm text-center" role="alert">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center pt-4 pb-3">
                                <h4 class="text-primary"><i class="fas fa-book mr-2"></i>Daftar Mata Pelajaran</h4>
                                <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#tambahMapelModal">
                                    <i class="fa fa-plus-circle mr-1"></i> Tambah Mapel
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered" id="table-1">
                                        <thead class="bg-light">
                                            <tr class="text-center">
                                                <th width="6%">No</th>
                                                <th class="text-left">Nama Mata Pelajaran</th>
                                                <th width="20%">Singkatan</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($mapel as $item)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="font-weight-bold text-dark">{{ $item->nama }}</td>
                                                    <td class="text-center">
                                                        @if($item->singkatan)
                                                            <span class="badge badge-info">{{ $item->singkatan }}</span>
                                                        @else
                                                            <span class="text-muted small font-italic">Belum diatur</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center" nowrap>
                                                        <a href="{{ route('mapel.edit', $item->uuid) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('mapel.destroy', $item->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus mapel ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr align="center">
                                                    <td colspan="4" class="py-4">Belum ada data mata pelajaran.</td>
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

{{-- Modal Tambah Mapel --}}
<div class="modal fade" id="tambahMapelModal" tabindex="-1" role="dialog" aria-labelledby="tambahMapelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tambahMapelLabel"><i class="fas fa-plus-circle mr-2"></i>Tambah Mata Pelajaran</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('mapel.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Lengkap Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" placeholder="Contoh: Pendidikan Agama Islam" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Singkatan (Untuk Laporan Rekap)</label>
                        <input type="text" class="form-control" name="singkatan" placeholder="Contoh: PAI" maxlength="15">
                        <small class="text-muted mt-2 d-block">Singkatan ini akan digunakan pada kolom tabel rekap nilai agar lebih rapi.</small>
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
@endsection