@extends('layouts.backend')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Pengaturan Pengumuman Kelulusan</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Pengaturan Kelulusan</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Ada Kesalahan!</strong>
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Daftar Pengaturan Kelulusan</h4>
                            <div class="card-header-action">
                                <a href="{{ route('graduation-settings.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Pengaturan
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="20%">Waktu Buka</th>
                                            <th width="20%">Waktu Tutup</th>
                                            <th width="15%">Status</th>
                                            <th width="30%">Keterangan</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($settings as $setting)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $setting->waktu_buka->format('d M Y, H:i') }}</strong>
                                                </td>
                                                <td>
                                                    <strong>{{ $setting->waktu_tutup->format('d M Y, H:i') }}</strong>
                                                </td>
                                                <td>
                                                    @if($setting->status === 'active')
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-secondary">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ Str::limit($setting->keterangan, 50) }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('graduation-settings.edit', $setting) }}" class="btn btn-sm btn-info" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('graduation-settings.toggleStatus', $setting) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm {{ $setting->status === 'active' ? 'btn-warning' : 'btn-success' }}" title="{{ $setting->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                            <i class="fas {{ $setting->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('graduation-settings.destroy', $setting) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Tidak ada data pengaturan</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4><i class="fas fa-info-circle"></i> Informasi Penggunaan</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Catatan Penting:</strong></p>
                            <ul>
                                <li>Gunakan format tanggal: <code>YYYY-MM-DD HH:MM:SS</code> (contoh: 2026-05-01 00:00:00)</li>
                                <li>Waktu tutup harus lebih besar dari waktu buka</li>
                                <li>Status <span class="badge badge-success">Aktif</span> akan digunakan di halaman cek kelulusan</li>
                                <li>Anda dapat memiliki beberapa pengaturan, tetapi hanya 1 yang aktif sekaligus</li>
                                <li>Tidak dapat menghapus pengaturan aktif yang terakhir</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
