@extends('layouts.backend')

@section('content')
<main>
    <div class="main-content">
        <section class="section">

            {{-- HEADER --}}
            <div class="section-header">
                <h1>Detail Nilai Siswa</h1>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('home') }}">Dashboard</a>
                    </div>

                    <div class="breadcrumb-item">
                        <a href="{{ route('siswa_saya.rekap', $kelas->id) }}">
                            Rekap Nilai Kelas
                        </a>
                    </div>

                    <div class="breadcrumb-item">Detail Nilai</div>
                </div>
            </div>

            {{-- NOTIF --}}
            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- BODY --}}
            <div class="section-body">
                <div class="card shadow-sm">

                    {{-- INFO SISWA --}}
                    <div class="card-header bg-light">
                        <div>
                            <h4 class="mb-0 text-primary">{{ $siswa->nama ?? '-' }}</h4>
                            <p class="mb-0 text-muted">
                                NISN: {{ $siswa->nisn ?? $siswa->nis ?? '-' }} |
                                Kelas: {{ $kelas->kelas ?? '-' }} {{ $kelas->nama ?? '' }} |
                                Tahun Ajaran: {{ $tahunTerkait->nama ?? '-' }} {{ $tahunTerkait->semester ? '(Semester ' . ucfirst($tahunTerkait->semester) . ')' : '' }}
                            </p>
                        </div>
                    </div>

                    {{-- TABEL --}}
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Rata² Harian</th>
                                        <th>PTS</th>
                                        <th>PAS</th>
                                        <th>Nilai Akhir</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $totalNilaiRapor = 0;
                                        $jumlahMapelTerhitung = 0;
                                        $kkm = 60;
                                    @endphp

                                    {{-- Loop berdasarkan daftar SEMUA Mapel agar mapel kosong tetap muncul --}}
                                    @forelse ($mapels as $mapel)
                                        @php
                                            // Ambil koleksi nilai untuk mapel ini
                                            $grades = $nilai_per_mapel[$mapel->nama] ?? collect();
                                            
                                            $harian = $grades->where('jenis', 'HARIAN')->avg('nilai');
                                            $pts = $grades->where('jenis', 'PTS')->first()?->nilai;
                                            $pas = $grades->where('jenis', 'PAS')->first()?->nilai;

                                            // Cek kelengkapan komponen nilai
                                            $isLengkap = !is_null($harian) && !is_null($pts) && !is_null($pas);

                                            // Hitung Nilai Akhir (Harian 60%, Rata-rata PTS/PAS 40%)
                                            $nilaiAkhir = null;
                                            $hasHarian = !is_null($harian);
                                            $hasUjian = !is_null($pts) || !is_null($pas);

                                            if ($hasHarian && $hasUjian) {
                                                $avgUjian = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();
                                                $nilaiAkhir = ($harian * 0.6) + ($avgUjian * 0.4);
                                            } elseif ($hasHarian) {
                                                $nilaiAkhir = $harian;
                                            } elseif ($hasUjian) {
                                                $nilaiAkhir = collect([$pts, $pas])->filter(fn($v) => !is_null($v))->avg();
                                            }

                                            // Bulatkan Nilai
                                            if (!is_null($nilaiAkhir)) {
                                                $nilaiAkhir = round($nilaiAkhir);
                                                $totalNilaiRapor += $nilaiAkhir;
                                                $jumlahMapelTerhitung++;
                                            }

                                            // Status Ketuntasan
                                            $status = '-';
                                            $badge = 'badge-light';
                                            if (!is_null($nilaiAkhir)) {
                                                if (!$isLengkap) {
                                                    $status = 'Belum Tuntas';
                                                    $badge = 'badge-danger';
                                                } else {
                                                    $status = ($nilaiAkhir >= $kkm) ? 'Tuntas' : 'Tidak Tuntas';
                                                    $badge = ($nilaiAkhir >= $kkm) ? 'badge-success' : 'badge-danger';
                                                }
                                            }
                                        @endphp

                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="font-weight-bold">{{ $mapel->nama }}</td>

                                            <td class="text-center">
                                                {{ $hasHarian ? round($harian, 1) : '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $pts ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $pas ?? '-' }}
                                            </td>

                                            <td class="text-center font-weight-bold">
                                                {{ $nilaiAkhir ?? '-' }}
                                            </td>

                                            <td class="text-center">
                                                @if(!is_null($nilaiAkhir))
                                                    <span class="badge {{ $badge }}">
                                                        {{ $status }}
                                                    </span>
                                                    @if(!$isLengkap)
                                                        <br><small class="text-danger mt-1 d-block font-weight-bold" style="font-size: 10px;">Data belum lengkap</small>
                                                    @endif
                                                @else
                                                    {{-- Jika belum ada nilai sama sekali --}}
                                                    <span class="badge badge-danger">Belum Tuntas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Data mata pelajaran tidak tersedia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                {{-- FOOTER --}}
                                @if ($jumlahMapelTerhitung > 0)
                                    <tfoot>
                                        <tr class="bg-light font-weight-bold">
                                            <td colspan="5" class="text-right">Rata-Rata Nilai Rapor</td>
                                            <td colspan="2" class="text-center text-primary" style="font-size: 1.1rem;">
                                                {{ number_format($totalNilaiRapor / $jumlahMapelTerhitung, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif

                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
</main>
@endsection