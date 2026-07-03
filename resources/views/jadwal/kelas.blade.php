@extends('layouts.backend')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Fix Overflow untuk Main Content (Mencegah terpotong) */
    .main-content { overflow: visible !important; }

    /* Fix Z-index Select2 di dalam Modal */
    .select2-container--open { z-index: 1060 !important; }

    /* Styling untuk Select2 agar serasi dengan Bootstrap/Stisla */
    .select2-container .select2-selection--single {
        height: calc(1.5em + .75rem + 2px) !important;
        padding: 0.375rem 0.75rem;
        border: 1px solid #e4e6fc;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + .75rem - 10px) !important;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem) !important;
        right: 5px !important;
    }

    /* Modal tampilan lebih elegan */
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .modal-header { border-bottom: none; padding-bottom: 10px; }
    .modal-footer { border-top: none; padding-top: 10px; }

    /* Penyesuaian Responsif Layar Mobile (HP) */
    @media (max-width: 575px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .section-header-breadcrumb {
            margin-top: 15px;
            width: 100%;
        }
        .section-header-breadcrumb .btn {
            width: 100%;
            text-align: center;
        }
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .card-header div {
            width: 100%;
            margin-top: 10px;
        }
        .card-header div .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><i class="fas fa-calendar-check mr-2"></i> Jadwal Kelas {{ $kelas->kelas }}</h1>
                    @if($tahunAktif)
                        <small class="text-muted d-block mt-2" style="font-size: 13px;">
                            Jadwal Tahun Pelajaran <b>{{ $tahunAktif->nama }}</b> |
                            Semester <b>{{ ucfirst($tahunAktif->semester) }}</b>
                        </small>
                    @else
                        <small class="text-danger d-block mt-2" style="font-size: 13px;">
                            <i class="fas fa-exclamation-circle mr-1"></i> <b>Tidak ada tahun ajaran aktif. Jadwal mungkin tidak relevan.</b>
                        </small>
                    @endif
                </div>
                <div class="section-header-breadcrumb mt-2 mt-md-0">
                    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Kelas
                    </a>
                </div>
            </div>

            {{-- Notifikasi --}}
            @if (session('notif'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm text-center">
                    {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center">
                    {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="card shadow-sm border-top border-primary" style="border-top-width: 3px !important;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap pt-4 pb-3">
                        <h4 class="mb-2 mb-md-0 text-primary"><i class="fas fa-list-alt mr-2"></i> Rincian Jadwal Pelajaran</h4>
                        <div>
                            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah Jadwal
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="table-1">
                                <thead class="bg-light text-center">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="10%">Hari</th>
                                        <th width="15%">Waktu</th>
                                        <th class="text-left">Mata Pelajaran</th>
                                        <th class="text-left">Guru Pengajar</th>
                                        <th width="12%">Semester</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jadwal as $jd)
                                        <tr class="align-middle text-center">
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="font-weight-bold">{{ $jd->hari->nama ?? '-' }}</td>
                                            <td class="text-muted">
                                                <span class="badge badge-light border text-dark shadow-sm">
                                                    {{ \Carbon\Carbon::parse($jd->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jd->jam_selesai)->format('H:i') }}
                                                </span>
                                            </td>
                                            <td class="text-left font-weight-bold text-dark">{{ $jd->mapel->nama ?? '-' }}</td>
                                            <td class="text-left"><i class="fas fa-user-tie mr-1 text-info"></i> {{ $jd->pegawai->nama ?? '-' }}</td>
                                            <td>{{ ucfirst($jd->tahun->semester ?? '-') }}</td>
                                            <td nowrap>
                                                <div class="d-flex justify-content-center" style="gap: 5px;">
                                                    <a href="{{ route('jadwal.edit', $jd->id) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit Jadwal">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('jadwal.destroy', $jd->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Yakin ingin menghapus jadwal ini secara permanen?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus Jadwal">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon text-muted"><i class="fas fa-calendar-times"></i></div>
                                                    <h2>Belum Ada Jadwal</h2>
                                                    <p class="lead">Belum ada jadwal yang ditambahkan untuk kelas ini di tahun ajaran aktif.</p>
                                                </div>
                                            </td>
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

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('jadwal.store') }}" method="POST" class="modal-content shadow-lg border-0">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahLabel">
                    <i class="fas fa-calendar-plus mr-2"></i> Tambah Jadwal Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <div class="form-row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="font-weight-bold">Hari Mengajar <span class="text-danger">*</span></label>
                        <select name="hari_id" class="form-control select2" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach ($hari as $h)
                                <option value="{{ $h->id }}">{{ $h->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="font-weight-bold">Guru Pengajar <span class="text-danger">*</span></label>
                        <select name="pegawai_id" class="form-control select2" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($guru as $g)
                                <option value="{{ $g->id }}">{{ $g->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="font-weight-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mapel_id" class="form-control select2" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($mapel as $mpl)
                                <option value="{{ $mpl->id }}">{{ $mpl->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="font-weight-bold">Tahun Ajaran & Semester</label>
                        @if($tahunAktif)
                            <input type="text" class="form-control font-weight-bold text-primary bg-whitesmoke"
                                   value="{{ $tahunAktif->nama . ' — ' . ucfirst($tahunAktif->semester) }}" readonly>
                        @else
                            <div class="alert alert-warning p-2 m-0 text-center font-weight-bold shadow-sm">
                                <i class="fas fa-info-circle mr-1"></i> Tidak ada TA aktif.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="font-weight-bold">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-whitesmoke border-top">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary shadow-sm" {{ !$tahunAktif ? 'disabled' : '' }}>
                    <i class="fas fa-save mr-1"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.modal').appendTo("body");

        $('#modalTambah .select2').select2({
            dropdownParent: $('#modalTambah'),
            width: '100%',
            placeholder: '-- Pilih --'
        });

        setTimeout(() => {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush