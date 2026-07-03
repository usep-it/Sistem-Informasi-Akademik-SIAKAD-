@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Manajemen Jadwal</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Pilih Kelas</div>
                </div>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-header">
                        <h4>Pilih Kelas untuk Mengelola Jadwal</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kelas</th>
                                        <th>Wali Kelas</th>
                                        <th>Tahun Pelajaran</th>
                                        <th class="text-center">Jumlah Jadwal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kelas as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kelas }}</td>
                                            <td>{{ $item->pegawai?->nama ?? '-' }}</td>
                                            <td>{{ $item->tahun?->nama ?? '-' }} ({{ ucfirst($item->tahun?->semester ?? '-') }})</td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">{{ $item->jadwal_count }} Jadwal</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('jadwal.kelas', $item->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-search"></i> Lihat & Kelola Jadwal
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data kelas. Silakan tambah data kelas terlebih dahulu.</td>
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
@endsection

