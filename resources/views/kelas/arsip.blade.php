@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">📦 Arsip Angkatan Siswa Lulus</h3>

    @forelse ($angkatan as $tahun => $siswas)
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <strong>Angkatan {{ $tahun }}</strong> ({{ count($siswas) }} siswa)
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat, Tanggal Lahir</th>
                            <th>NISN</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswas as $i => $s)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>{{ $s->jk }}</td>
                                <td>{{ $s->tempat }}, {{ \Carbon\Carbon::parse($s->ttl)->format('d M Y') }}</td>
                                <td>{{ $s->nisn }}</td>
                                <td><span class="badge bg-success">{{ $s->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">Belum ada data angkatan yang diarsipkan.</div>
    @endforelse
</div>
@endsection
