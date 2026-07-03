@extends('layouts.backend')

@push('styles')
<style>
    .main-content { overflow: visible !important; }
    .table-foto { width: 100px; height: 60px; object-fit: cover; border-radius: 8px; }
    .badge-premium { background: var(--siakad-gradient); color: white; border: none; }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-bullhorn mr-2"></i> Manajemen Informasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Informasi</div>
                </div>
            </div>

            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm" role="alert">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-4 pb-3">
                        <h4 class="text-primary">Daftar Pengumuman & Berita</h4>
                        {{-- Tombol dialihkan ke route create (halaman baru) --}}
                        <a href="{{ route('informasi.create') }}" class="btn btn-primary shadow-sm font-weight-bold">
                            <i class="fas fa-edit mr-1"></i> Tulis Berita Baru
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table-1">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th>Foto</th>
                                        <th class="text-left">Judul Berita</th>
                                        <th class="text-left">Ringkasan Isi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($info as $item)
                                        <tr class="align-middle">
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">
                                                <img src="{{ url('/informasi_foto/' . $item->foto) }}" alt="Thumbnail" class="table-foto border shadow-sm">
                                            </td>
                                            <td class="text-left">
                                                <span class="font-weight-bold text-dark">{{ $item->judul }}</span>
                                                <small class="d-block text-muted">{{ $item->created_at->isoFormat('D MMMM Y') }}</small>
                                            </td>
                                            <td class="text-left text-muted small">
                                                {{ Str::limit(strip_tags($item->isi), 120) }}
                                            </td>
                                            <td class="text-center" nowrap>
                                                <div class="d-flex justify-content-center" style="gap: 5px;">
                                                    <a href="{{ route('informasi.edit', $item->id) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('informasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus berita ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-5">Belum ada data berita.</td></tr>
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
@endsection