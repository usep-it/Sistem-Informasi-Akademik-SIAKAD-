@extends('layouts.backend')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Agar dropdown Select2 tidak terpotong */
    .select2-container--open { z-index: 9999 !important; }
    .main-content { overflow: visible !important; }
    
    /* Penyesuaian UI Select2 */
    .select2-container .select2-selection--single {
        height: calc(1.5em + .75rem + 2px);
        border: 1px solid #e4e6fc;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + .75rem);
        padding-left: .75rem;
    }
    
    /* UI Khusus Laporan */
    .card-laporan {
        border-top: 4px solid #6777ef;
        border-radius: 8px;
    }
    .form-group label {
        font-weight: 600;
        color: #34395e;
        letter-spacing: 0.5px;
    }
    .status-indicator {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .status-archive { background: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }
    .status-active { background: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-file-pdf mr-2"></i> Pusat Laporan Nilai</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Laporan Nilai</div>
                </div>
            </div>

            <div class="section-body">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-10">
                        <div class="card shadow-sm card-laporan">
                            <div class="card-header bg-whitesmoke d-flex justify-content-between">
                                <h4 class="text-primary"><i class="fas fa-sliders-h mr-2"></i> Parameter Cetak</h4>
                                <div id="year-status-badge"></div>
                            </div>

                            <div class="card-body px-4 py-4">
                                <div class="row">
                                    {{-- 1. Pilih Tahun Pelajaran (Termasuk Riwayat/Arsip) --}}
                                    <div class="col-md-6 form-group">
                                        <label>Tahun Pelajaran <span class="text-danger">*</span></label>
                                        <select id="tahun_id" class="form-control select2">
                                            <option value="" disabled {{ !isset($tahunAktif) ? 'selected' : '' }}>-- Pilih Periode --</option>
                                            @foreach ($tahun as $item)
                                                <option value="{{ $item->id }}" 
                                                        data-status="{{ strtolower($item->status) }}"
                                                        {{ (isset($tahunAktif) && $tahunAktif->id == $item->id) ? 'selected' : '' }}>
                                                    TA {{ $item->nama }} - {{ ucfirst($item->semester) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 2. Mode Cetak --}}
                                    <div class="col-md-6 form-group">
                                        <label>Mode Cetak <span class="text-danger">*</span></label>
                                        <select id="modeCetak" class="form-control select2">
                                            <option value="kelas">Seluruh Kelas (Rekapitulasi)</option>
                                            <option value="siswa">Transkip Siswa </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    {{-- 3. Pilih Kelas --}}
                                    <div class="col-md-12 form-group">
                                        <label>Kelas & Rombongan Belajar <span class="text-danger">*</span></label>
                                        <select id="kelas_id" class="form-control select2">
                                            <option value="" selected disabled>-- Pilih Periode Terlebih Dahulu --</option>
                                        </select>
                                    </div>

                                    {{-- 4. Pilih Siswa (Hanya jika mode Per Siswa) --}}
                                    <div class="col-md-12 form-group" id="divSiswa" style="display: none;">
                                        <label>Nama Peserta Didik <span class="text-danger">*</span></label>
                                        <select id="siswa_id" class="form-control select2">
                                            <option value="">-- Pilih Siswa --</option>
                                        </select>
                                        <small class="text-info mt-2 d-block" id="load-msg" style="display:none;">
                                            <i class="fas fa-search"></i> Mencari daftar siswa pada periode tersebut...
                                        </small>
                                    </div>
                                </div>

                                <div class="text-center mt-4 pt-4 border-top">
                                    <button id="cetakLaporan" class="btn btn-lg btn-success px-5 btn-block shadow-sm" style="border-radius: 30px;">
                                        <i class="fas fa-print mr-2"></i> Generate Laporan PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-history text-info mr-3 fa-2x"></i>
                                <div>
                                    <h6 class="mb-1 text-dark">Informasi Akses Riwayat</h6>
                                    <p class="mb-0 small text-muted">Sistem mendukung pencetakan laporan untuk semester lampau. Cukup pilih <b>Tahun Pelajaran</b> yang diinginkan, maka daftar kelas dan siswa akan menyesuaikan data pada periode tersebut.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    const semuaKelas = @json($kelas);
    const tahunSelect = $('#tahun_id');
    const kelasSelect = $('#kelas_id');
    const modeSelect = $('#modeCetak');
    const siswaSelect = $('#siswa_id');

    function filterKelas() {
        let selectedOption = tahunSelect.find(':selected');
        let tahunId = selectedOption.val();
        let status = selectedOption.data('status');
        
        // Update Indikator Status
        let badgeHtml = '';
        if(tahunId) {
            badgeHtml = (status === 'aktif') 
                ? '<span class="status-indicator status-active"><i class="fas fa-check-circle mr-1"></i> Periode Aktif</span>'
                : '<span class="status-indicator status-archive"><i class="fas fa-archive mr-1"></i> Data Arsip</span>';
        }
        $('#year-status-badge').html(badgeHtml);

        // Reset Dropdown
        kelasSelect.empty().append('<option value="" selected disabled>-- Pilih Kelas --</option>');
        
        if (!tahunId) return;

        // Filter data kelas secara fleksibel
        let filtered = semuaKelas.filter(k => k.tahun_id == tahunId);

        if (filtered.length === 0) {
            kelasSelect.append('<option disabled>Tidak ada data kelas di periode ini</option>');
        } else {
            filtered.forEach(k => {
                let text = `Kelas ${k.kelas}${k.nama ? ' | Fase ' + k.nama : ''}`;
                kelasSelect.append(new Option(text, k.id));
            });
        }
        kelasSelect.trigger('change');
    }

    tahunSelect.on('change', filterKelas);
    filterKelas();

    modeSelect.on('change', function() {
        if ($(this).val() === 'siswa') $('#divSiswa').slideDown();
        else {
            $('#divSiswa').slideUp();
            siswaSelect.val(null).trigger('change');
        }
    });

    // ===============================================================
    // PERBAIKAN UTAMA: Mengirim tahun_id ke API untuk pencarian historis
    // ===============================================================
    kelasSelect.on('change', function() {
        let kelasId = $(this).val();
        let tahunId = tahunSelect.val(); // Ambil tahun yang dipilih

        if (modeSelect.val() === 'siswa' && kelasId && tahunId) {
            siswaSelect.empty().append('<option value="">Memuat data siswa...</option>').prop('disabled', true).trigger('change');
            $('#load-msg').show();

            // Tambahkan ?tahun_id=XXX agar SiswaController tahu periode mana yang dicari
            fetch(`/api/siswa-by-kelas/${kelasId}?tahun_id=${tahunId}`)
                .then(r => r.json())
                .then(data => {
                    siswaSelect.empty().append('<option value="">-- Pilih Peserta Didik --</option>').prop('disabled', false);
                    $('#load-msg').hide();
                    
                    if (data.length > 0) {
                        data.forEach(s => {
                            let nisn = s.nisn ? ` (${s.nisn})` : '';
                            siswaSelect.append(new Option(s.nama + nisn, s.id));
                        });
                    } else {
                        siswaSelect.append('<option value="" disabled>Tidak ada riwayat siswa di periode ini</option>');
                    }
                    siswaSelect.trigger('change');
                });
        }
    });

    $('#cetakLaporan').on('click', function(e) {
        e.preventDefault();
        let th = tahunSelect.val();
        let kl = kelasSelect.val();
        let mode = modeSelect.val();
        let si = siswaSelect.val();

        if (!th || !kl) return alert('Mohon pilih Tahun dan Kelas!');
        if (mode === 'siswa' && !si) return alert('Mohon pilih Nama Siswa!');

        let url = (mode === 'kelas') 
            ? "{{ route('laporan.nilai-kelas', ':kl') }}".replace(':kl', kl) + `?tahun=${th}&mode=pdf`
            : "{{ route('laporan.nilai.siswa', [':si', ':th']) }}".replace(':si', si).replace(':th', th) + `?mode=pdf`;

        window.open(url, '_blank').focus();
    });
});
</script>
@endpush