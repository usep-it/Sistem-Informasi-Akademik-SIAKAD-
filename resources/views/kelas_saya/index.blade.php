@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Daftar Kelas yang Anda Ampu</h1>
            </div>

            <div class="section-body">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kelas</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($daftar_kelas as $kelas)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kelas->kelas }} {{ $kelas->nama }}</td>
                                        <td>{{ $kelas->tahun->nama }}</td>
                                        <td>{{ $kelas->siswas_count }} Siswa</td>
                                        <td>
                                            <a href="{{ route('kelas.manage', $kelas->id) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-users"></i> Kelola Anggota
                                            </a>
                                            {{-- Tambahkan link lain jika perlu, misal ke daftar jadwal kelas --}}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Anda belum ditugaskan sebagai wali kelas.</td>
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