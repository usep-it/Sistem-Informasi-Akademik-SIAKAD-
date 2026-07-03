@extends('layouts.backend')

@push('styles')
<style>
    .main-content {
        overflow: visible !important;
    }
    table td, table th {
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">

            {{-- Header --}}
            <div class="section-header">
                <h1>Rekap Guru & Tenaga Kependidikan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item active">Rekap GTK</div>
                </div>
            </div>

            <div class="section-body">

                {{-- =============================================== --}}
                {{-- ============== BAGIAN CHART BARU ============== --}}
                {{-- =============================================== --}}
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4>Rekapitulasi Berdasarkan Status Kepegawaian</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="chartStatusKepegawaian" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4>Rekapitulasi Berdasarkan Jabatan</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="chartJabatan" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- =============================================== --}}
                {{-- ============= AKHIR BAGIAN CHART ============= --}}
                {{-- =============================================== --}}


                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-users mr-2"></i>Daftar Guru & Tenaga Kependidikan</h4>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover w-100">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        {{-- HEADER TABEL DIPERBARUI --}}
                                        <th style="width: 60px">#</th>
                                        <th>Nama Pegawai</th>
                                        <th>Jabatan</th>
                                        <th>Status Kepegawaian</th>
                                        {{-- <th style="width: 120px">Jumlah</th> --}} {{-- Kolom Jumlah Dihapus --}}
                                    </tr>
                                </thead>
                                <tbody>

                                    @php $no = 1; @endphp

                                    {{-- PERBAIKAN LOOP: Ganti $rekap ke $pegawai --}}
                                    @forelse($pegawai as $item)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->jabatan }}</td>
                                        <td>{{ $item->status_kepegawaian }}</td>
                                        {{-- <td class="text-center">1</td> --}} {{-- Kolom Jumlah Dihapus --}}
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data pegawai.</td> {{-- Colspan disesuaikan jadi 4 --}}
                                    </tr>
                                    @endforelse
                                    {{-- AKHIR PERBAIKAN LOOP --}}

                                    <tr class="table-primary font-weight-bold">
                                        {{-- TOTAL ROW DIPERBARUI --}}
                                        <td colspan="3" class="text-center font-weight-bold">TOTAL GTK</td> {{-- Colspan disesuaikan jadi 3 --}}
                                        <td class="text-center font-weight-bold">{{ $total ?? 0 }}</td>
                                    </tr>

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

@push('scripts')
{{-- Load Library Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Data dari Controller (dikonversi dari PHP ke JSON)
    const dataStatus = @json($rekapStatus ?? []);
    const dataJabatan = @json($rekapJabatan ?? []);

    // Helper untuk warna-warni chart
    const backgroundColors = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(255, 159, 64, 0.7)'
    ];
    const borderColors = [
        'rgba(54, 162, 235, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ];

    // 1. Chart Status Kepegawaian
    const ctxStatus = document.getElementById('chartStatusKepegawaian');
    if (ctxStatus && Object.keys(dataStatus).length > 0) {
        new Chart(ctxStatus, {
            type: 'pie', // Tipe chart: pie (lingkaran)
            data: {
                labels: Object.keys(dataStatus), // Label (e.g., "PNS", "PPPK", "Honorer")
                datasets: [{
                    label: 'Status Kepegawaian',
                    data: Object.values(dataStatus), // Data (jumlahnya)
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let percentage = ((value / {{ $total ?? 1 }}) * 100).toFixed(1);
                                return ` ${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Chart Jabatan
    const ctxJabatan = document.getElementById('chartJabatan');
    if (ctxJabatan && Object.keys(dataJabatan).length > 0) {
        new Chart(ctxJabatan, {
            type: 'doughnut', // Tipe chart: doughnut (donat)
            data: {
                labels: Object.keys(dataJabatan), // Label (e.g., "Kepala Sekolah", "Guru", "Tenaga Administrasi")
                datasets: [{
                    label: 'Jabatan',
                    data: Object.values(dataJabatan), // Data (jumlahnya)
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                     tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let percentage = ((value / {{ $total ?? 1 }}) * 100).toFixed(1);
                                return ` ${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>
@endpush