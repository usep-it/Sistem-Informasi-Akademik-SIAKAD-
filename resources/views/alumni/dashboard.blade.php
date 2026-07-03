@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Portal Alumni</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Alumni</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <img src="{{ Auth::user()->foto ? asset('foto_user/' . Auth::user()->foto) : ($siswa?->foto ? asset('foto_siswa/' . $siswa->foto) : 'https://placehold.co/140x140/EFEFEF/AAAAAA?text=Foto') }}"
                                     alt="Foto {{ $siswa?->nama ?? 'Alumni' }}"
                                     class="rounded-circle mb-3"
                                     style="width:140px; height:140px; object-fit:cover; border: 4px solid #6777ef;">
                                <h4 class="mb-1">{{ $siswa?->nama ?? Auth::user()->name }}</h4>
                                <p class="text-muted mb-2">{{ $siswa?->status ?? 'Alumni' }}</p>
                                <div class="badge badge-primary mb-2">
                                    {{ $siswa?->kelas?->kelas ? 'Kelas ' . $siswa->kelas->kelas . ' ' . $siswa->kelas->nama : 'Belum ada kelas' }}
                                </div>
                                <div class="text-muted small">
                                    NISN: {{ $siswa?->nisn ?? '-' }}<br>
                                    NIS: {{ $siswa?->nis ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Akses Alumni</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-sm text-muted">Sebagai alumni, Anda tetap dapat menggunakan sistem untuk melihat data nilai dan mencetak transkrip. Akses lainnya dibatasi demi keamanan.</p>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Lihat riwayat nilai</li>
                                    <li class="list-group-item">Cetak transkrip</li>
                                    <li class="list-group-item">Ubah profil dan kata sandi</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Selamat Datang, Alumni</h5>
                                    <p class="text-muted mb-0">Halaman khusus untuk siswa yang telah lulus atau berstatus alumni.</p>
                                </div>
                                <span class="badge badge-info">Akses Terbatas</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="card h-100 border-primary">
                                            <div class="card-body d-flex flex-column justify-content-between">
                                                <div>
                                                    <h6 class="font-weight-bold">Nilai Akademik</h6>
                                                    <p class="text-muted">Lihat rekapan nilai Anda per tahun ajaran dan semester.</p>
                                                </div>
                                                <a href="{{ route('nilai.saya') }}" class="btn btn-primary btn-block">Lihat Nilai</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="card h-100 border-success">
                                            <div class="card-body d-flex flex-column justify-content-between">
                                                <div>
                                                    <h6 class="font-weight-bold">Cetak Transkrip</h6>
                                                    <p class="text-muted">Pilih periode lalu cetak dokumen transkrip resmi dalam format PDF.</p>
                                                </div>
                                                <a href="{{ route('nilai.saya') }}" class="btn btn-success btn-block">Cetak Transkrip</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <strong>Catatan:</strong> Jika Anda tidak menemukan periode kelulusan, silakan hubungi administrator sekolah untuk membuka kembali akses data lama.
                                </div>

                                @if ($tahunAktif)
                                    <div class="card border-light mt-3">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold">Tahun Ajaran Saat Ini</h6>
                                            <p class="mb-0">{{ $tahunAktif->nama }} &mdash; {{ ucfirst($tahunAktif->semester) }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
