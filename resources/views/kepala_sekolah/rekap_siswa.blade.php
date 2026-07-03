@extends('layouts.backend')

@push('styles')
<style>
    /* Tambahkan style jika perlu, atau biarkan kosong jika sudah ada di backend.blade.php */
    .table td, .table th {
        vertical-align: middle !important;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Rekap Siswa</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Rekap Siswa</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Rekapitulasi Siswa</h2>
            <p class="section-lead">
                Ringkasan jumlah siswa berdasarkan rombel & jenis kelamin (Tahun Ajaran Aktif).
            </p>

            {{-- =============================================== --}}
            {{-- ============== BAGIAN CHART BARU ============== --}}
            {{-- =============================================== --}}
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4>Grafik Siswa per Rombongan Belajar</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="chartPerRombel" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4>Total Siswa (L/P)</h4>
                        </div>
                        <div class="card-body">
                            {{--  --}}
                            <canvas id="chartTotalGender" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            {{-- =============================================== --}}
            {{-- ============= AKHIR BAGIAN CHART ============= --}}
            {{-- =============================================== --}}

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4>Rekapitulasi Jumlah Siswa per Rombel</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">

                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kelas</th>
                                            <th>Wali Kelas</th>
                                            <th>Laki-laki</th>
                                            <th>Perempuan</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {{-- PERBAIKAN: Loop menggunakan $rekapData dari Controller --}}
                                        @forelse($rekapData as $data)
                                            <tr class="text-center">
    <td>{{ $data['kelas'] }}</td>
    <td class="text-left">{{ $data['wali_kelas'] }}</td>
    <td>{{ $data['laki_laki'] }}</td>
    <td>{{ $data['perempuan'] }}</td>
    <td>{{ $data['total_kelas'] }}</td>
</tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data kelas aktif.</td>
                                            </tr>
                                        @endforelse

                                        {{-- TOTAL --}}
                                        <tr class="table-primary text-center font-weight-bold">
                                            <td colspan="2" class="text-right"><strong>Total Keseluruhan</strong></td>
                                            <td><strong>{{ $totalLaki }}</strong></td>
                                            <td><strong>{{ $totalPerempuan }}</strong></td>
                                            <td><strong>{{ $totalSiswa }}</strong></td>
                                        </tr>
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
@endsection

@push('scripts')
{{-- Load Library Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Data dari Controller
    const dataTotalGender = @json($chartTotalGender ?? []);
    const dataPerRombel = @json($chartPerRombel ?? []);
    const totalSiswa = {{ $totalSiswa ?? 1 }}; // Untuk persentase

    // Warna
    const colorLaki = 'rgba(54, 162, 235, 0.7)';
    const colorPerempuan = 'rgba(255, 99, 132, 0.7)';
    const borderLaki = 'rgba(54, 162, 235, 1)';
    const borderPerempuan = 'rgba(255, 99, 132, 1)';

    // 1. Chart Total Gender (Doughnut)
    const ctxTotal = document.getElementById('chartTotalGender');
    if (ctxTotal && Object.keys(dataTotalGender).length > 0) {
        new Chart(ctxTotal, {
            type: 'doughnut',
            data: {
                labels: Object.keys(dataTotalGender),
                datasets: [{
                    data: Object.values(dataTotalGender),
                    backgroundColor: [colorLaki, colorPerempuan],
                    borderColor: [borderLaki, borderPerempuan],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let percentage = 0;
                                if (totalSiswa > 0) {
                                    percentage = ((value / totalSiswa) * 100).toFixed(1);
                                }
                                return ` ${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Chart Siswa per Rombel (Stacked Bar)
    const ctxRombel = document.getElementById('chartPerRombel');
    if (ctxRombel && dataPerRombel.labels.length > 0) {
        new Chart(ctxRombel, {
            type: 'bar',
            data: {
                labels: dataPerRombel.labels, // ["Kelas 1", "Kelas 2", ...]
                datasets: [
                    {
                        label: 'Laki-laki',
                        data: dataPerRombel.dataLaki,
                        backgroundColor: colorLaki,
                        borderColor: borderLaki,
                        borderWidth: 1
                    },
                    {
                        label: 'Perempuan',
                        data: dataPerRombel.dataPerempuan,
                        backgroundColor: colorPerempuan,
                        borderColor: borderPerempuan,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true }, // Tumpuk di sumbu X
                    y: { stacked: true, beginAtZero: true } // Tumpuk di sumbu Y
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { 
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    }

});
</script>
@endpush