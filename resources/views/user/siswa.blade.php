@extends('layouts.backend')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        .main-content { overflow: visible !important; }
        .card-header-action-custom { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-left: auto; }
        @media (max-width: 767px) {
            .card-header { flex-direction: column; align-items: flex-start !important; padding-bottom: 15px; }
            .card-header-action-custom { width: 100%; margin-top: 15px; margin-left: 0; }
            .card-header-action-custom .btn { flex: 1; text-align: center; }
        }
        .select2-container--open { z-index: 1060 !important; }
        .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px) !important; border: 1px solid #e4e6fc; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + .75rem) !important; padding-left: .75rem; }
    </style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas fa-users-cog mr-2"></i> Manajemen Akun Siswa</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Manajemen User</div>
                    <div class="breadcrumb-item">Siswa</div>
                </div>
            </div>

            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4><i class="fas fa-list mr-2"></i> Daftar Akun Siswa</h4>
                                <div class="card-header-action-custom">
                                    <a href="{{ route('user.export.siswa') }}" class="btn btn-sm btn-success shadow-sm">
                                        <i class="fas fa-file-excel mr-1"></i> Eksport
                                    </a>
                                    <a href="{{ route('siswa.generateAkun') }}" 
                                       class="btn btn-sm btn-info shadow-sm" 
                                       onclick="event.preventDefault(); if(confirm('Sistem akan membuatkan akun secara otomatis bagi siswa aktif yang belum memilikinya. Lanjutkan?')) document.getElementById('generate-akun-form').submit();">
                                        <i class="fas fa-magic mr-1"></i> Generate Masal
                                    </a>
                                    <form id="generate-akun-form" action="{{ route('siswa.generateAkun') }}" method="POST" style="display:none;">@csrf</form>
                                    <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahAkunSiswa">
                                        <i class="fas fa-plus-circle mr-1"></i> Tambah Akun
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="table-akun-siswa" style="width:100%">
                                        <thead class="bg-light">
                                            <tr class="text-center">
                                                <th width="5%">No</th>
                                                <th class="text-left">Nama Peserta Didik</th>
                                                <th>Username (NIS)</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($asiswa as $item)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="font-weight-bold">{{ $item->siswa?->nama ?? '-' }}</td>
                                                    <td class="text-center"><span class="badge badge-light border">{{ $item->username }}</span></td>
                                                    <td class="text-center">
                                                        <a href="/user/reset-password/{{ $item->uuid }}/" class="btn btn-warning btn-sm" title="Reset Password" onclick="return confirm('Reset password user {{ $item->username }}?')">
                                                            <i class="fa fa-key"></i>
                                                        </a>
                                                        <form action="{{ route('user.destroy', $item->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center py-5">Belum ada akun siswa.</td></tr>
                                            @endforelse
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
</main>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambahAkunSiswa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Tambah Akun Login</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ url('/user/store/siswa') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="role" value="Siswa">
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Peserta Didik <span class="text-danger">*</span></label>
                        <select name="siswa_id" class="form-control select2" id="siswa_id" required>
                            <option value="" selected disabled>-- Ketik Nama atau NIS --</option>
                            @foreach ($siswa as $sw)
                                <option value="{{ $sw->id }}" data-nis="{{ $sw->nis }}">{{ $sw->nama }} - ({{ $sw->nis }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Username</label>
                            <input type="text" name="username" id="username" class="form-control" readonly required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#table-akun-siswa tbody tr td').length > 1) {
                $('#table-akun-siswa').DataTable({
                    responsive: true,
                    language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
                    // PERBAIKAN: Urutkan berdasarkan kolom Nama (Indeks 1) secara default
                    order: [[1, 'asc']], 
                    columnDefs: [
                        { orderable: false, targets: [0, 3] }, // Matikan sortir untuk No dan Aksi
                    ]
                });
            }

            $('#siswa_id').select2({
                dropdownParent: $('#modalTambahAkunSiswa'),
                width: '100%'
            });

            $('#siswa_id').on('select2:select', function (e) {
                var selectedNis = $(this).find(':selected').data('nis');
                $('#username').val(selectedNis ? selectedNis : '');
            });
        });
    </script>
@endpush