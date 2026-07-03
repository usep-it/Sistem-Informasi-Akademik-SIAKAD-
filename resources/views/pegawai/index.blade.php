@extends('layouts.backend')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <style>
        .main-content { overflow: visible !important; }
        .card-header-action-custom {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-left: auto;
        }
        @media (max-width: 575px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
            }
            .card-header-action-custom {
                width: 100%;
                margin-top: 15px;
            }
            .card-header-action-custom .btn {
                flex: 1;
                text-align: center;
            }
        }
    </style>
@endpush

@section('content')
<main>
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><i class="fas {{ $kategori == 'tendik' ? 'fa-user-cog' : 'fa-chalkboard-teacher' }} mr-2"></i> {{ $title }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('home') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Data GTK</div>
                    <div class="breadcrumb-item">{{ $kategori == 'tendik' ? 'Tendik' : 'Pendidik' }}</div>
                </div>
            </div>

            @if (session('notif'))
                <div class="alert alert-primary alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {!! session('notif') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {!! session('error') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <p><strong>Oops! Terjadi beberapa kesalahan:</strong></p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="section-body">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Daftar {{ $title }}</h4>
                        <div class="card-header-action-custom">
                            <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambah">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah {{ $kategori == 'tendik' ? 'Tendik / Kepsek' : 'Pendidik' }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap" id="table-pegawai" style="width: 100%;">
                                <thead class="bg-light">
                                    <tr class="text-center">
                                        <th width="5%">No</th>
                                        <th class="text-left">Nama Lengkap</th>
                                        <th>NIP / NUPTK</th>
                                        <th>Jabatan</th>
                                        <th>Status Pegawai</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pegawai as $item)
                                        <tr>
                                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <img alt="image" src="{{ $item->foto ? asset('foto_pegawai/' . $item->foto) : 'https://placehold.co/120x120/EFEFEF/AAAAAA?text=Foto' }}" class="rounded-circle shadow-sm border" width="40" height="40" style="object-fit: cover;" data-toggle="tooltip" title="{{ $item->nama }}">
                                                    <div class="ml-3 font-weight-bold">{{ $item->nama }}</div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-left">
                                                @if($item->nip)
                                                    <span class="d-block">NIP: {{ $item->nip }}</span>
                                                @endif
                                                @if($item->nuptk)
                                                    <span class="d-block text-muted small">NUPTK: {{ $item->nuptk }}</span>
                                                @endif
                                                @if(!$item->nip && !$item->nuptk)
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">{{ $item->jabatan ?? '-' }}</td>
                                            <td class="text-center align-middle">
                                                @php
                                                    $statusColor = 'badge-secondary';
                                                    if($item->status_kepegawaian == 'PNS') $statusColor = 'badge-success';
                                                    elseif($item->status_kepegawaian == 'PPPK') $statusColor = 'badge-info';
                                                    elseif($item->status_kepegawaian == 'Honorer') $statusColor = 'badge-warning text-dark';
                                                @endphp
                                                <div class="badge {{ $statusColor }} shadow-sm">{{ $item->status_kepegawaian ?? '-' }}</div>
                                            </td>
                                            <td class="text-center align-middle" nowrap>
                                                {{-- PERBAIKAN: Tombol Edit sekarang membuka Modal, bukan pindah halaman --}}
                                                <button class="btn btn-warning btn-sm shadow-sm btn-edit" title="Edit Data"
                                                    data-id="{{ $item->id }}"
                                                    data-nama="{{ $item->nama }}"
                                                    data-jk="{{ $item->jk }}"
                                                    data-jabatan="{{ $item->jabatan }}"
                                                    data-tempat_lahir="{{ $item->tempat_lahir }}"
                                                    data-ttl="{{ $item->ttl }}"
                                                    data-nip="{{ $item->nip }}"
                                                    data-status_kepegawaian="{{ $item->status_kepegawaian }}"
                                                    data-nik="{{ $item->nik }}"
                                                    data-nuptk="{{ $item->nuptk }}"
                                                    data-email="{{ $item->email }}"
                                                    data-alamat="{{ $item->alamat }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <form action="{{ route('pegawai.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data pegawai ini secara permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Hapus Data"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon"><i class="fas fa-users-slash"></i></div>
                                                    <h2>Belum Ada Data</h2>
                                                    <p class="lead">Data {{ $title }} belum tersedia di sistem.</p>
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

{{-- ======================== MODAL TAMBAH GTK ======================== --}}
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahLabel"><i class="fas fa-user-plus mr-2"></i> Tambah {{ $kategori == 'tendik' ? 'Tendik / Kepsek' : 'Pendidik' }} Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pegawai.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jk" class="form-control" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="Laki-Laki" {{ old('jk') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Perempuan" {{ old('jk') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jabatan <span class="text-danger">*</span></label>
                            <select name="jabatan" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Jabatan --</option>
                                {{-- LOGIKA JABATAN: Kepsek masuk Tendik --}}
                                @if ($kategori == 'tendik')
                                    <optgroup label="Manajerial">
                                        <option value="Kepala Sekolah" {{ old('jabatan') == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    </optgroup>
                                    <optgroup label="Tenaga Kependidikan">
                                        <option value="Tenaga Administrasi" {{ old('jabatan') == 'Tenaga Administrasi' ? 'selected' : '' }}>Tenaga Administrasi</option>
                                        <option value="Penjaga Sekolah" {{ old('jabatan') == 'Penjaga Sekolah' ? 'selected' : '' }}>Penjaga Sekolah</option>
                                        <option value="Pustakawan" {{ old('jabatan') == 'Pustakawan' ? 'selected' : '' }}>Pustakawan</option>
                                        <option value="Operator Sekolah" {{ old('jabatan') == 'Operator Sekolah' ? 'selected' : '' }}>Operator Sekolah</option>
                                    </optgroup>
                                @else
                                    <optgroup label="Tenaga Pendidik">
                                        <option value="Guru Kelas" {{ old('jabatan') == 'Guru Kelas' ? 'selected' : '' }}>Guru Kelas</option>
                                        <option value="Guru Mapel" {{ old('jabatan') == 'Guru Mapel' ? 'selected' : '' }}>Guru Mapel</option>
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="ttl" class="form-control" value="{{ old('ttl') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ old('nip') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status Kepegawaian <span class="text-danger">*</span></label>
                            <select name="status_kepegawaian" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="PNS" {{ old('status_kepegawaian') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                <option value="PPPK" {{ old('status_kepegawaian') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                 <option value="PPPK Paruh Waktu" {{ old('status_kepegawaian') == 'PPPK PW' ? 'selected' : '' }}>PPPK Paruh Waktu</option>
                                <option value="Honorer" {{ old('status_kepegawaian') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ old('nik') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>NUPTK</label>
                            <input type="text" name="nuptk" class="form-control" value="{{ old('nuptk') }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="2" class="form-control" required>{{ old('alamat') }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label>Foto Profil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Opsional. Ekstensi: JPG/PNG. Maks 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================== MODAL EDIT GTK ======================== --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i> Edit Data Pegawai</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jenis Kelamin <span class="text-danger">*</span></label>
                            <select name="jk" id="edit_jk" class="form-control" required>
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jabatan <span class="text-danger">*</span></label>
                            <select name="jabatan" id="edit_jabatan" class="form-control" required>
                                @if ($kategori == 'tendik')
                                    <optgroup label="Manajerial">
                                        <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    </optgroup>
                                    <optgroup label="Tenaga Kependidikan">
                                        <option value="Tenaga Administrasi">Tenaga Administrasi</option>
                                        <option value="Penjaga Sekolah">Penjaga Sekolah</option>
                                        <option value="Pustakawan">Pustakawan</option>
                                        <option value="Operator Sekolah">Operator Sekolah</option>
                                    </optgroup>
                                @else
                                    <optgroup label="Tenaga Pendidik">
                                        <option value="Guru Kelas">Guru Kelas</option>
                                        <option value="Guru Mapel">Guru Mapel</option>
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="ttl" id="edit_ttl" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>NIP</label>
                            <input type="text" name="nip" id="edit_nip" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Status Kepegawaian <span class="text-danger">*</span></label>
                            <select name="status_kepegawaian" id="edit_status_kepegawaian" class="form-control" required>
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                                <option value="PPPK PW">PPPK Paruh Waktu</option>
                                <option value="Honorer">Honorer</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>NIK</label>
                            <input type="text" name="nik" id="edit_nik" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>NUPTK</label>
                            <input type="text" name="nuptk" id="edit_nuptk" class="form-control">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" id="edit_alamat" rows="2" class="form-control" required></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label>Ganti Foto Profil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
                    </div>
                </div>

                <div class="modal-footer bg-whitesmoke border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Update Data</button>
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

<script>
$(document).ready(function() {
    // Inisialisasi DataTables
    if ($('#table-pegawai tbody tr td').length > 1) {
        $('#table-pegawai').DataTable({
            responsive: true,
            language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" },
            columnDefs: [
                { orderable: false, targets: [0, 5] },
                { responsivePriority: 1, targets: 1 },
                { responsivePriority: 2, targets: 5 }
            ]
        });
    }

    // FUNGSI MEMBUKA MODAL EDIT DAN MENGISI DATA
    $('.btn-edit').on('click', function() {
        const data = $(this).data();
        
        // Ganti URL Action form sesuai dengan ID pegawai
        $('#formEdit').attr('action', '/pegawai/' + data.id);

        // Isi form dengan data yang diambil dari tombol
        $('#edit_nama').val(data.nama);
        $('#edit_jk').val(data.jk);
        $('#edit_jabatan').val(data.jabatan);
        $('#edit_tempat_lahir').val(data.tempat_lahir);
        $('#edit_ttl').val(data.ttl);
        $('#edit_nip').val(data.nip == '-' ? '' : data.nip);
        $('#edit_status_kepegawaian').val(data.status_kepegawaian);
        $('#edit_nik').val(data.nik == '-' ? '' : data.nik);
        $('#edit_nuptk').val(data.nuptk == '-' ? '' : data.nuptk);
        $('#edit_email').val(data.email == '-' ? '' : data.email);
        $('#edit_alamat').val(data.alamat);

        // Tampilkan Modal Edit
        $('#modalEdit').modal('show');
    });

    // Auto-hide alert
    $('.alert').delay(5000).fadeOut(300);
});
</script>
@endpush